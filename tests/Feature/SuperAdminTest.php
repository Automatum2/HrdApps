<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function loginAs(string $role = 'super_admin', ?Employee $employee = null): User
    {
        $user = User::create([
            'username' => $role . 'test',
            'email' => $role . '@example.com',
            'password' => bcrypt('password'),
            'role' => $role,
            'employee_id' => $employee ? $employee->id : null,
        ]);

        $this->actingAs($user)->withSession(['user_role' => $role]);

        return $user;
    }

    public function test_super_admin_can_access_dashboard()
    {
        $this->loginAs();

        $response = $this->get('/backoffice/dashboard');

        $response->assertStatus(200);
    }

    public function test_non_super_admin_redirected_from_super_admin_routes()
    {
        $this->loginAs('karyawan');

        $response = $this->get(route('backoffice.super_admin.kelola_hr'));
        $response->assertRedirect(route('backoffice.dashboard'));

        $response = $this->get(route('backoffice.super_admin.kelola_karyawan'));
        $response->assertRedirect(route('backoffice.dashboard'));
    }

    public function test_super_admin_can_create_hr_manager()
    {
        $this->loginAs();

        $response = $this->post(route('backoffice.super_admin.kelola_hr.store'), [
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

        $this->loginAs();

        $response = $this->put(route('backoffice.super_admin.kelola_hr.update', $user->id), [
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

        $this->loginAs();

        $response = $this->delete(route('backoffice.super_admin.kelola_hr.destroy', $user->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'status' => 'nonaktif',
        ]);
    }

    public function test_super_admin_cannot_add_karyawan_directly()
    {
        $this->loginAs();

        $response = $this->post('/backoffice/super-admin/kelola-karyawan', [
            'nama' => 'Karyawan Baru',
            'email' => 'karyawanbaru@example.com',
        ]);

        $response->assertMethodNotAllowed();
    }

    public function test_super_admin_cannot_access_cv_review()
    {
        $this->loginAs();

        $response = $this->get(route('backoffice.cv.index'));

        $response->assertRedirect(route('backoffice.dashboard'));
    }

    public function test_super_admin_can_promote_employee_to_manager()
    {
        $this->loginAs();

        $employee = Employee::create([
            'nik' => 'EMP-5555',
            'nama_lengkap' => 'Jaya Karyawan',
            'email' => 'jaya@email.com',
            'status_kerja' => 'tetap',
            'status' => 'aktif',
            'gaji_pokok' => 4000000,
            'is_cv_approved' => true
        ]);

        $user = User::create([
            'username' => 'jayakaryawan',
            'email' => 'jaya@email.com',
            'password' => bcrypt('password'),
            'role' => 'karyawan',
            'employee_id' => $employee->id
        ]);

        $department = Department::create([
            'nama_department' => 'IT',
            'kode_department' => 'IT',
        ]);

        $response = $this->post(route('backoffice.super_admin.kelola_hr.store'), [
            'type' => 'promosi',
            'employee_id' => $employee->id,
            'role' => 'manager_departemen',
            'jabatan' => 'Manager IT',
            'department_id' => $department->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Tidak ada record ganda di employees
        $this->assertDatabaseCount('employees', 1);
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'nik' => 'EMP-5555',
            'department_id' => $department->id,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'manager_departemen',
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

        User::create([
            'username' => 'oldkaryawan77',
            'email' => 'oldkaryawan@example.com',
            'password' => bcrypt('password'),
            'role' => 'karyawan',
            'employee_id' => $employee->id
        ]);

        $this->loginAs();

        $response = $this->put(route('backoffice.super_admin.kelola_karyawan.update', $employee->id), [
            'nama' => 'Updated Karyawan',
            'email' => 'updatedkaryawan@example.com',
            'gaji_pokok' => 6000000,
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

        User::create([
            'username' => 'deletekaryawan66',
            'email' => 'deletekaryawan@example.com',
            'password' => bcrypt('password'),
            'role' => 'karyawan',
            'employee_id' => $employee->id
        ]);

        $this->loginAs();

        $response = $this->delete(route('backoffice.super_admin.kelola_karyawan.destroy', $employee->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'status' => 'nonaktif',
        ]);
    }
}