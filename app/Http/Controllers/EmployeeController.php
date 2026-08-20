<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index()
    {
        if (session('user_role') !== 'superadmin') {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak. Halaman ini hanya untuk Super Admin.');
        }

        $employees = Employee::where('is_cv_approved', true)
            ->whereDoesntHave('user', function ($q) {
                $q->whereIn('role', ['hr_manager', 'superadmin', 'manager_departemen']);
            })
            ->orderBy('id', 'desc')->paginate(10);
        $departments = Department::orderBy('nama_department', 'asc')->get();
        $positions = Position::orderBy('nama_jabatan', 'asc')->get();

        return view('backoffice.super_admin_kelola_karyawan', compact('employees', 'departments', 'positions'));
    }

    public function show($id)
    {
        // For now, only Super Admin (and eventually HRD Manager) will access this specific controller method.
        // Karyawan themselves might have a different route (e.g. /profile) pointing to a different controller/method.
        if (!in_array(session('user_role'), ['superadmin', 'hr_manager', 'manager_departemen'])) {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        $employee = Employee::findOrFail($id);
        
        // Pass a 'back_route' variable to know where the "Kembali" button should point
        $back_route = session('user_role') === 'superadmin' ? route('backoffice.super_admin.kelola_karyawan') : route('backoffice.karyawan'); 

        return view('backoffice.detail_karyawan', compact('employee', 'back_route'));
    }

    public function store(Request $request)
    {
        if (session('user_role') !== 'superadmin') {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email|unique:employees,email',
        ]);

        // Create random NIK
        $nik = 'EMP-' . rand(1000, 9999);

        $employee = Employee::create([
            'nik' => $nik,
            'nama_lengkap' => $request->nama,
            'email' => $request->email,
            'gaji_pokok' => 0,
            'status_kerja' => 'magang', // Default based on mockup
            'status' => 'aktif',
            'is_cv_approved' => true
        ]);

        // Create User
        $user = User::create([
            'username' => strtolower(str_replace(' ', '', $request->nama)) . rand(10,99),
            'email' => $request->email,
            'password' => Hash::make(Str::random(24)), // Random temporary password
            'role' => 'karyawan',
            'employee_id' => $employee->id
        ]);

        // Send activation link
        $token = \Illuminate\Support\Facades\Password::broker()->createToken($user);
        $user->notify(new \App\Notifications\AccountActivation($token));

        return redirect()->back()->with('success', 'Karyawan ' . $employee->nama_lengkap . ' berhasil ditambahkan dan email aktivasi telah dikirim.');
    }

    public function update(Request $request, $id)
    {
        if (session('user_role') !== 'superadmin') {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        $employee = Employee::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => [
                'required', 'string', 'email', 'max:255',
                \Illuminate\Validation\Rule::unique('employees')->ignore($employee->id),
                $employee->user ? \Illuminate\Validation\Rule::unique('users')->ignore($employee->user->id) : ''
            ],
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'status_kerja' => 'nullable|in:tetap,kontrak,magang,musiman',
        ]);

        $employee->update([
            'nama_lengkap' => $request->nama,
            'email' => $request->email,
            'department_id' => $request->department_id,
            'position_id' => $request->position_id,
            'gaji_pokok' => $request->gaji_pokok ?? 0,
            'status_kerja' => $request->status_kerja ?? 'tetap',
        ]);

        if ($employee->user) {
            $employee->user->update([
                'email' => $request->email,
            ]);
        }

        return redirect()->back()->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (session('user_role') !== 'superadmin') {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        $employee = Employee::findOrFail($id);
        
        $employee->update(['status' => 'nonaktif']);
        
        return redirect()->back()->with('success', 'Status karyawan berhasil diubah menjadi Nonaktif.');
    }
}
