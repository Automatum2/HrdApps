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
        if (session('user_role') !== 'super_admin') {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        // Ambil User dengan role hr_manager beserta data employee-nya
        $managers = User::where('role', 'hr_manager')->with('employee')->orderBy('id', 'desc')->paginate(10);
        $positions = \App\Models\Position::where('level', 'manager')->get();
        return view('backoffice.super_admin_kelola_hr', compact('managers', 'positions'));
    }

    public function store(Request $request)
    {
        if (session('user_role') !== 'super_admin') {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'nik' => 'required|string|max:255|unique:employees,nik',
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email|unique:employees,email',
            'jabatan' => 'required|string|max:255',
        ]);

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
            'status_kerja' => 'tetap', 
            'status' => 'aktif',
            'gaji_pokok' => 0 // Default 0
        ]);

        // Buat record User
        $user = User::create([
            'username' => strtolower(str_replace(' ', '', $request->nama)) . rand(10,99),
            'email' => $request->email,
            'password' => Hash::make(Str::random(24)),
            'role' => 'hr_manager',
            'employee_id' => $employee->id
        ]);

        // Kirim link aktivasi
        $token = \Illuminate\Support\Facades\Password::broker()->createToken($user);
        $user->notify(new \App\Notifications\AccountActivation($token));

        return redirect()->back()->with('success', 'HR Manager ' . $employee->nama_lengkap . ' berhasil ditambahkan dan email aktivasi telah dikirim.');
    }

    public function update(Request $request, $id)
    {
        if (session('user_role') !== 'super_admin') {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        $user = User::where('role', 'hr_manager')->findOrFail($id);
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

        if ($employee) {
            $employee->update([
                'nama_lengkap' => $request->nama,
                'email' => $request->email,
            ]);
        }

        $user->update([
            'email' => $request->email,
        ]);

        return redirect()->back()->with('success', 'Data HR Manager berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (session('user_role') !== 'super_admin') {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        $user = User::where('role', 'hr_manager')->findOrFail($id);
        
        if ($user->employee) {
            $user->employee->update(['status' => 'nonaktif']);
        }
        
        return redirect()->back()->with('success', 'Status HR Manager berhasil dinonaktifkan.');
    }
}
