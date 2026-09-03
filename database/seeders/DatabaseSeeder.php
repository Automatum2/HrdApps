<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Departemen
        $deptIT = Department::firstOrCreate(
            ['kode_department' => 'IT'],
            ['nama_department' => 'IT & Technology']
        );

        $deptHR = Department::firstOrCreate(
            ['kode_department' => 'HR'],
            ['nama_department' => 'Human Resources']
        );

        // 2. Jabatan
        $posManager = Position::firstOrCreate(
            ['nama_jabatan' => 'Manager'],
            ['level' => 'manager', 'tunjangan_jabatan' => 1500000]
        );

        $posStaff = Position::firstOrCreate(
            ['nama_jabatan' => 'Staff'],
            ['level' => 'staff', 'tunjangan_jabatan' => 500000]
        );

        // 3. Super Admin Account
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin'
            ]
        );

        // 4. HR Manager Account & Employee
        $empHR = Employee::updateOrCreate(
            ['nik' => 'EMP-0001'],
            [
                'nama_lengkap' => 'HR Manager',
                'email' => 'hrmanager@example.com',
                'department_id' => $deptHR->id,
                'position_id' => $posManager->id,
                'gaji_pokok' => 8000000,
                'status_kerja' => 'tetap',
                'status' => 'aktif',
                'is_cv_approved' => true
            ]
        );

        User::updateOrCreate(
            ['username' => 'hrmanager'],
            [
                'email' => 'hrmanager@example.com',
                'password' => Hash::make('password'),
                'role' => 'hr_manager',
                'employee_id' => $empHR->id
            ]
        );

        // 5. Manager Departemen Account & Employee
        $empManager = Employee::updateOrCreate(
            ['nik' => 'EMP-0002'],
            [
                'nama_lengkap' => 'Manager IT',
                'email' => 'managerit@example.com',
                'department_id' => $deptIT->id,
                'position_id' => $posManager->id,
                'gaji_pokok' => 7500000,
                'status_kerja' => 'tetap',
                'status' => 'aktif',
                'is_cv_approved' => true
            ]
        );

        User::updateOrCreate(
            ['username' => 'managerit'],
            [
                'email' => 'managerit@example.com',
                'password' => Hash::make('password'),
                'role' => 'manager_departemen',
                'employee_id' => $empManager->id
            ]
        );

        // 6. Karyawan Account & Employee
        $empStaff = Employee::updateOrCreate(
            ['nik' => 'EMP-0003'],
            [
                'nama_lengkap' => 'Karyawan IT',
                'email' => 'karyawan@example.com',
                'department_id' => $deptIT->id,
                'position_id' => $posStaff->id,
                'gaji_pokok' => 5000000,
                'status_kerja' => 'tetap',
                'status' => 'aktif',
                'is_cv_approved' => true
            ]
        );

        User::updateOrCreate(
            ['username' => 'karyawan'],
            [
                'email' => 'karyawan@example.com',
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'employee_id' => $empStaff->id
            ]
        );
    }
}
