<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\LeaveRequest;

class LeaveTest extends TestCase
{
    use DatabaseTransactions;

    public function test_leave_requires_dept_manager_and_hr_approval()
    {
        $dept = Department::create(['nama_department' => 'IT', 'kode_department' => 'IT-001']);
        
        $managerUser = User::factory()->create(['role' => 'manager_departemen']);
        $manager = Employee::create(['user_id' => $managerUser->id, 'department_id' => $dept->id, 'nama_lengkap' => 'Mgr IT', 'email' => 'mgr@example.com', 'nik' => 'NIK-001', 'gaji_pokok' => 5000000]);

        $hrUser = User::factory()->create(['role' => 'hr_manager']);
        
        $employeeUser = User::factory()->create(['role' => 'karyawan']);
        $employee = Employee::create(['user_id' => $employeeUser->id, 'department_id' => $dept->id, 'nama_lengkap' => 'Staff IT', 'email' => 'staff@example.com', 'nik' => 'NIK-002', 'gaji_pokok' => 4000000]);

        $leave = LeaveRequest::create([
            'employee_id' => $employee->id,
            'tanggal_mulai' => now()->addDays(2)->toDateString(),
            'tanggal_selesai' => now()->addDays(3)->toDateString(),
            'tipe' => 'cuti',
            'keterangan' => 'Liburan',
            'status' => 'menunggu_manager'
        ]);

        $this->actingAs($managerUser)
             ->withSession(['user_role' => 'manager_departemen'])
             ->post(route('backoffice.leaves.approve', $leave->id));
        $this->assertEquals('menunggu_hr', $leave->fresh()->status);

        $this->actingAs($hrUser)
             ->withSession(['user_role' => 'hr_manager'])
             ->post(route('backoffice.leaves.approve', $leave->id));
        $this->assertEquals('disetujui', $leave->fresh()->status);
    }
}
