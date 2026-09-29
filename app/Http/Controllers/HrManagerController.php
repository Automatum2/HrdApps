<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class HrManagerController extends Controller
{
    public function index()
    {
        if (!in_array(session('user_role'), ['super_admin', 'hr_manager'])) {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        // Ambil User dengan role hr_manager atau manager_departemen beserta data employee-nya
        if (session('user_role') === 'hr_manager') {
            $managers = User::where('role', 'manager_departemen')->with('employee')->orderBy('id', 'desc')->paginate(10);
        } else {
            $managers = User::whereIn('role', ['hr_manager', 'manager_departemen'])->with('employee')->orderBy('id', 'desc')->paginate(10);
        }
        $positions = \App\Models\Position::where('level', 'manager')->get();
        $departments = \App\Models\Department::all();
        $employees = Employee::where('is_cv_approved', true)
            ->where('status', 'aktif')
            ->whereHas('user', function ($q) {
                $q->where('role', 'karyawan');
            })
            ->with('department', 'position')
            ->orderBy('nama_lengkap', 'asc')
            ->get();
        return view('backoffice.super_admin_kelola_hr', compact('managers', 'positions', 'departments', 'employees'));
    }

    public function store(Request $request)
    {
        if (!in_array(session('user_role'), ['super_admin', 'hr_manager'])) {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }
        
        if (session('user_role') === 'hr_manager' && $request->role !== 'manager_departemen') {
            return redirect()->back()->with('error', 'Akses ditolak. HR Manager hanya dapat menambah Manager Departemen.');
        }

        // Opsi 1: Promosi Karyawan Internal (tidak membuat record baru)
        if ($request->type === 'promosi') {
            return $this->storePromotion($request);
        }

        // Opsi 2: Manager Eksternal (record baru)
        $request->validate([
            'nik' => 'required|string|max:255|unique:employees,nik',
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email|unique:employees,email',
            'jabatan' => 'required|string|max:255',
        ]);

        if ($request->role === 'hr_manager') {
            $existingHR = User::where('role', 'hr_manager')->count();
            if ($existingHR >= 1) {
                return redirect()->back()->with('error', 'Hanya boleh ada 1 HR Manager di dalam sistem.');
            }
        }

        // Buat record Employee
        // Buat atau cari jabatan
        $position = \App\Models\Position::firstOrCreate(
            ['nama_jabatan' => $request->jabatan],
            ['level' => 'manager', 'tunjangan_jabatan' => 0]
        );

        $employee = Employee::create([
            'nik' => $request->nik,
            'nama_lengkap' => $request->nama,
            'email' => $request->email,
            'position_id' => $position->id,
            'department_id' => $request->role === 'manager_departemen' ? $request->department_id : null,
            'status_kerja' => 'tetap', 
            'status' => 'aktif',
            'gaji_pokok' => 0, // Default 0
            'is_cv_approved' => true
        ]);

        // Generate username ringkas & mudah diingat
        $firstName = explode(' ', trim($request->nama))[0];
        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstName));
        if (strlen($baseUsername) < 3) {
            $parts = explode(' ', trim($request->nama));
            $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', ($parts[0] ?? '') . ($parts[1] ?? '')));
        }
        $baseUsername = substr($baseUsername ?: 'manager', 0, 8);
        $username = $baseUsername . rand(10, 99);
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . rand(100, 999);
        }

        // Buat record User
        $user = User::create([
            'username' => $username,
            'email' => $request->email,
            'password' => Hash::make(Str::random(24)),
            'role' => $request->role === 'manager_departemen' ? 'manager_departemen' : 'hr_manager',
            'employee_id' => $employee->id
        ]);

        // Otomatis set departemen manager_id jika role manager_departemen
        if ($request->role === 'manager_departemen' && $request->filled('department_id')) {
            \App\Models\Department::where('id', $request->department_id)->update(['manager_id' => $employee->id]);
        }

        // Kirim link aktivasi
        $token = \Illuminate\Support\Facades\Password::broker()->createToken($user);
        $user->notify(new \App\Notifications\AccountActivation($token));

        return redirect()->back()->with('success', 'HR Manager ' . $employee->nama_lengkap . ' berhasil ditambahkan dan email aktivasi telah dikirim.');
    }

    protected function storePromotion(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'role' => 'required|in:hr_manager,manager_departemen',
            'jabatan' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        if ($request->role === 'hr_manager') {
            $existingHR = User::where('role', 'hr_manager')->count();
            if ($existingHR >= 1) {
                return redirect()->back()->with('error', 'Hanya boleh ada 1 HR Manager di dalam sistem.');
            }
        }

        if ($request->role === 'manager_departemen' && blank($request->department_id)) {
            return redirect()->back()->with('error', 'Departemen wajib dipilih untuk Manager Departemen.');
        }

        $employee = Employee::with('user')->findOrFail($request->employee_id);

        if (!$employee->user) {
            return redirect()->back()->with('error', 'Karyawan ini belum memiliki akun login, gunakan opsi Manager Eksternal.');
        }

        if ($employee->user->role !== 'karyawan') {
            return redirect()->back()->with('error', 'Karyawan ini sudah memiliki role ' . $employee->user->role . ' dan tidak dapat dipromosikan lagi.');
        }

        // Jabatan Manager (tunjangan_jabatan otomatis merujuk pada data positions)
        $position = \App\Models\Position::firstOrCreate(
            ['nama_jabatan' => $request->jabatan],
            ['level' => 'manager', 'tunjangan_jabatan' => 0]
        );

        $employee->update([
            'position_id' => $position->id,
            'department_id' => $request->role === 'manager_departemen' ? $request->department_id : null,
            'status_kerja' => 'tetap',
            'status' => 'aktif',
        ]);

        $employee->user->update([
            'role' => $request->role === 'manager_departemen' ? 'manager_departemen' : 'hr_manager',
        ]);

        // Otomatis sinkronkan ID karyawan menjadi manager_id di departemen terkait
        if ($request->role === 'manager_departemen' && $request->filled('department_id')) {
            \App\Models\Department::where('id', $request->department_id)->update([
                'manager_id' => $employee->id
            ]);
        }

        $namaRole = $request->role === 'manager_departemen' ? 'Manager Departemen' : 'HR Manager';

        return redirect()->back()->with('success', 'Karyawan ' . $employee->nama_lengkap . ' berhasil dipromosikan menjadi ' . $namaRole . '. NIK tetap sama dan tidak ada record duplikat.');
    }

    public function update(Request $request, $id)
    {
        if (!in_array(session('user_role'), ['super_admin', 'hr_manager'])) {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        $user = User::whereIn('role', ['hr_manager', 'manager_departemen'])->findOrFail($id);
        
        if (session('user_role') === 'hr_manager' && $user->role !== 'manager_departemen') {
            return redirect()->back()->with('error', 'Akses ditolak. HR Manager hanya dapat mengubah data Manager Departemen.');
        }
        if (session('user_role') === 'hr_manager' && $request->role !== 'manager_departemen') {
            return redirect()->back()->with('error', 'Akses ditolak. HR Manager tidak dapat mengubah role menjadi HR Manager.');
        }

        $employee = $user->employee;

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => [
                'required', 'string', 'email', 'max:255',
                \Illuminate\Validation\Rule::unique('employees')->ignore($employee->id),
                \Illuminate\Validation\Rule::unique('users')->ignore($user->id)
            ],
            // 'jabatan' => 'required|string|max:255',
        ]);

        if ($request->role === 'hr_manager' && $user->role !== 'hr_manager') {
            $existingHR = User::where('role', 'hr_manager')->count();
            if ($existingHR >= 1) {
                return redirect()->back()->with('error', 'Hanya boleh ada 1 HR Manager di dalam sistem.');
            }
        }

        if ($employee) {
            $employee->update([
                'nama_lengkap' => $request->nama,
                'email' => $request->email,
                'department_id' => $request->role === 'manager_departemen' ? $request->department_id : null,
            ]);
        }

        $user->update([
            'email' => $request->email,
            'role' => $request->role === 'manager_departemen' ? 'manager_departemen' : 'hr_manager',
        ]);

        return redirect()->back()->with('success', 'Data Manager berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (!in_array(session('user_role'), ['super_admin', 'hr_manager'])) {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        $user = User::whereIn('role', ['hr_manager', 'manager_departemen'])->findOrFail($id);

        if (session('user_role') === 'hr_manager' && $user->role !== 'manager_departemen') {
            return redirect()->back()->with('error', 'Akses ditolak. HR Manager hanya dapat menonaktifkan Manager Departemen.');
        }

        if ($user->employee) {
            $user->employee->update(['status' => 'nonaktif']);
        }
        
        return redirect()->back()->with('success', 'Status Manager berhasil dinonaktifkan.');
    }
}
