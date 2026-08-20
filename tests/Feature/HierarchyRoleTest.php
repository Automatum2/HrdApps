<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class HierarchyRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_specific_hierarchy_roles()
    {
        $user = User::create([
            'name' => 'Test HR Training',
            'username' => 'hr_training_test',
            'email' => 'hr_training@example.com',
            'password' => bcrypt('password'),
            'role' => 'hr_training_manager'
        ]);
        $this->assertEquals('hr_training_manager', $user->role);
        
        $department = \App\Models\Department::create([
            'nama_department' => 'Test Dept',
            'kode_department' => 'TD01',
            'manager_id' => $user->id
        ]);
        $this->assertEquals($user->id, $department->manager_id);

        $position = \App\Models\Position::create([
            'nama_jabatan' => 'Test Position',
        ]);

        $employee = \App\Models\Employee::create([
            'user_id' => $user->id,
            'nama_lengkap' => 'Test Employee',
            'email' => 'emp@example.com',
            'nik' => '12345678',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Test Address',
            'no_telepon' => '08123456789',
            'jabatan_id' => $position->id,
            'department_id' => $department->id,
            'gaji_pokok' => 5000000,
            'tanggal_bergabung' => '2023-01-01',
            'status_kerja' => 'harian'
        ]);
        $this->assertEquals('harian', $employee->status_kerja);
    }
}
