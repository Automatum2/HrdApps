<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable Exception Handling to see exact errors
        // $this->withoutExceptionHandling();
    }

    public function test_super_admin_can_access_dashboard()
    {
        $response = $this->withSession(['user_role' => 'super_admin'])->get('/backoffice/dashboard');

        $response->assertStatus(200);
    }

    public function test_non_super_admin_redirected_from_super_admin_routes()
    {
        $response = $this->withSession(['user_role' => 'karyawan'])->get(route('backoffice.super_admin.kelola_hr'));
        $response->assertRedirect(route('backoffice.dashboard'));

        $response = $this->withSession(['user_role' => 'karyawan'])->get(route('backoffice.super_admin.kelola_karyawan'));
        $response->assertRedirect(route('backoffice.dashboard'));
    }

    public function test_super_admin_can_create_hr_manager()
    {
        $response = $this->withSession(['user_role' => 'super_admin'])
            ->post(route('backoffice.super_admin.kelola_hr.store'), [
                'nik' => 'HR-1234',
                'nama' => 'HR Test',
                'email' => 'hrtest@example.com',
                'jabatan' => 'HR Manager',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employees', [
            'nik' => 'HR-1234',
            'email' => 'hrtest@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'hrtest@example.com',
            'role' => 'hr_manager',
        ]);
    }

    public function test_super_admin_can_update_hr_manager()
    {
        $employee = Employee::create([
            'nik' => 'HR-9999',
            'nama_lengkap' => 'Old HR',
            'email' => 'oldhr@example.com',
            'status_kerja' => 'tetap',
            'status' => 'aktif',
            'gaji_pokok' => 0
        ]);

        $user = User::create([
            'username' => 'oldhr99',
            'email' => 'oldhr@example.com',
            'password' => bcrypt('password'),
            'role' => 'hr_manager',
            'employee_id' => $employee->id
        ]);

        $response = $this->withSession(['user_role' => 'super_admin'])
            ->put(route('backoffice.super_admin.kelola_hr.update', $user->id), [
                'nama' => 'Updated HR',
                'email' => 'updatedhr@example.com',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'nama_lengkap' => 'Updated HR',
            'email' => 'updatedhr@example.com',
        ]);
    }

    public function test_super_admin_can_nonaktif_hr_manager()
    {
        $employee = Employee::create([
            'nik' => 'HR-8888',
            'nama_lengkap' => 'To Delete HR',
            'email' => 'deletehr@example.com',
            'status_kerja' => 'tetap',
            'status' => 'aktif',
            'gaji_pokok' => 0
        ]);

        $user = User::create([
            'username' => 'deletehr88',
            'email' => 'deletehr@example.com',
            'password' => bcrypt('password'),
            'role' => 'hr_manager',
            'employee_id' => $employee->id
        ]);

        $response = $this->withSession(['user_role' => 'super_admin'])
            ->delete(route('backoffice.super_admin.kelola_hr.destroy', $user->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'status' => 'nonaktif',
        ]);
    }

    public function test_super_admin_can_create_karyawan()
    {
        $response = $this->withSession(['user_role' => 'super_admin'])
            ->post(route('backoffice.super_admin.kelola_karyawan.store'), [
                'nama' => 'Karyawan Baru',
                'email' => 'karyawanbaru@example.com',
                'gaji' => 5000000,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employees', [
            'nama_lengkap' => 'Karyawan Baru',
            'email' => 'karyawanbaru@example.com',
            'gaji_pokok' => 5000000,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'karyawanbaru@example.com',
            'role' => 'karyawan',
        ]);
    }

    public function test_super_admin_can_update_karyawan()
    {
        $employee = Employee::create([
            'nik' => 'EMP-7777',
            'nama_lengkap' => 'Old Karyawan',
            'email' => 'oldkaryawan@example.com',
            'status_kerja' => 'tetap',
            'status' => 'aktif',
            'gaji_pokok' => 4000000
        ]);

        $user = User::create([
            'username' => 'oldkaryawan77',
            'email' => 'oldkaryawan@example.com',
            'password' => bcrypt('password'),
            'role' => 'karyawan',
            'employee_id' => $employee->id
        ]);

        $response = $this->withSession(['user_role' => 'super_admin'])
            ->put(route('backoffice.super_admin.kelola_karyawan.update', $employee->id), [
                'nama' => 'Updated Karyawan',
                'email' => 'updatedkaryawan@example.com',
                'gaji' => 6000000,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'nama_lengkap' => 'Updated Karyawan',
            'email' => 'updatedkaryawan@example.com',
            'gaji_pokok' => 6000000,
        ]);
    }

    public function test_super_admin_can_nonaktif_karyawan()
    {
        $employee = Employee::create([
            'nik' => 'EMP-6666',
            'nama_lengkap' => 'To Delete Karyawan',
            'email' => 'deletekaryawan@example.com',
            'status_kerja' => 'tetap',
            'status' => 'aktif',
            'gaji_pokok' => 4000000
        ]);

        $user = User::create([
            'username' => 'deletekaryawan66',
            'email' => 'deletekaryawan@example.com',
            'password' => bcrypt('password'),
            'role' => 'karyawan',
            'employee_id' => $employee->id
        ]);

        $response = $this->withSession(['user_role' => 'super_admin'])
            ->delete(route('backoffice.super_admin.kelola_karyawan.destroy', $employee->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'status' => 'nonaktif',
        ]);
    }
}
