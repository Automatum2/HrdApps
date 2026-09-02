<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Notifications\AccountActivation;

class KarirAndCVTest extends TestCase
{
    use RefreshDatabase;

    protected function loginAs(string $role = 'hr_manager', ?Employee $employee = null): User
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

    public function test_pelamar_can_submit_application_with_file_and_url()
    {
        Storage::fake('public');

        $response = $this->post(route('karir.submit'), [
            'nama' => 'Budi Pelamar',
            'email' => 'budi.pelamar@example.com',
            'status_kerja' => 'kontrak',
            'cv_text' => '<p>Saya seorang Fullstack Developer berpengalaman.</p>',
            'cv_file' => UploadedFile::fake()->create('cv-budi.pdf', 500, 'application/pdf'),
            'cv_url' => 'https://linkedin.com/in/budipelamar',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employees', [
            'nama_lengkap' => 'Budi Pelamar',
            'email' => 'budi.pelamar@example.com',
            'status_kerja' => 'kontrak',
            'is_cv_approved' => false,
            'cv_url' => 'https://linkedin.com/in/budipelamar',
        ]);

        $employee = Employee::where('email', 'budi.pelamar@example.com')->first();
        $this->assertNotNull($employee->cv_file);
        Storage::disk('public')->assertExists($employee->cv_file);
    }

    public function test_hr_can_approve_applicant_which_creates_account_and_generates_otp()
    {
        Notification::fake();

        $employee = Employee::create([
            'nik' => 'APP-1111',
            'nama_lengkap' => 'Siti Pelamar',
            'email' => 'siti@example.com',
            'status_kerja' => 'harian',
            'is_cv_approved' => false,
            'status' => 'nonaktif',
            'gaji_pokok' => 0
        ]);

        $this->loginAs('hr_manager');

        $response = $this->post(route('backoffice.cv.approve', $employee->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $employee->refresh();
        $this->assertTrue((bool) $employee->is_cv_approved);
        $this->assertEquals('harian', $employee->status_kerja);
        $this->assertEquals('aktif', $employee->status);
        $this->assertNotNull($employee->activation_otp);
        $this->assertNotNull($employee->activation_otp_expires_at);

        $this->assertDatabaseHas('users', [
            'email' => 'siti@example.com',
            'role' => 'karyawan',
            'employee_id' => $employee->id,
        ]);

        $user = User::where('email', 'siti@example.com')->first();
        Notification::assertSentTo($user, AccountActivation::class, function ($notification) {
            return !empty($notification->otp) && strlen($notification->otp) === 6;
        });
    }

    public function test_superadmin_promotion_automatically_updates_departments_manager_id()
    {
        $employee = Employee::create([
            'nik' => 'EMP-9000',
            'nama_lengkap' => 'Andi Senior',
            'email' => 'andi@example.com',
            'status_kerja' => 'tetap',
            'status' => 'aktif',
            'gaji_pokok' => 5000000,
            'is_cv_approved' => true,
        ]);

        $user = User::create([
            'username' => 'andisenior',
            'email' => 'andi@example.com',
            'password' => bcrypt('password'),
            'role' => 'karyawan',
            'employee_id' => $employee->id,
        ]);

        $department = Department::create([
            'nama_department' => 'Keuangan',
            'kode_department' => 'KEU',
        ]);

        $this->loginAs('super_admin');

        $response = $this->post(route('backoffice.super_admin.kelola_hr.store'), [
            'type' => 'promosi',
            'employee_id' => $employee->id,
            'role' => 'manager_departemen',
            'jabatan' => 'Manager Keuangan',
            'department_id' => $department->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $department->refresh();
        $this->assertEquals($employee->id, $department->manager_id);

        $user->refresh();
        $this->assertEquals('manager_departemen', $user->role);
    }
}
