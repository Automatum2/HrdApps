<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;

class EnsureManagerAccounts extends Command
{
    protected $signature = 'manager:ensure-accounts';

    protected $description = 'Buat akun manager departemen untuk semua departemen, password default: password';

    public function handle()
    {
        $this->info('Memastikan akun manager untuk setiap departemen...');

        $position = Position::firstOrCreate(
            ['nama_jabatan' => 'Manager'],
            ['level' => 'manager', 'tunjangan_jabatan' => 0]
        );

        $rows = [];
        $departments = Department::orderBy('nama_department')->get();

        foreach ($departments as $dept) {
            $existingUser = User::where('role', 'manager_departemen')
                ->whereHas('employee', function ($q) use ($dept) {
                    $q->where('department_id', $dept->id);
                })
                ->first();

            $username = $this->makeUsername($dept);

            if (!$existingUser) {
                $employee = $this->createManagerEmployee($dept, $position, $username);
                $user = User::create([
                    'username' => $username,
                    'email' => $username . '@example.com',
                    'password' => Hash::make('password'),
                    'role' => 'manager_departemen',
                    'employee_id' => $employee->id,
                ]);

                if (!$dept->manager_id) {
                    $dept->update(['manager_id' => $employee->id]);
                }

                $this->info("=> Departemen \"{$dept->nama_department}\" akun baru dibuat: {$username} / password");
            } else {
                $existingUser->update(['password' => Hash::make('password')]);
                $this->info("=> Departemen \"{$dept->nama_department}\" akun lama ({$existingUser->username}) password direset.");
                $username = $existingUser->username;
            }

            $rows[] = [$dept->nama_department, $username, 'password'];
        }

        $this->newLine();
        $this->table(['Departemen', 'Username', 'Password'], $rows);
        $this->info('Selesai.');
    }

    protected function makeUsername(Department $dept)
    {
        $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $dept->kode_department ?? ''));
        if (strlen($base) < 2) {
            $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', Str::slug($dept->nama_department)));
        }
        $base = substr($base ?: 'manager', 0, 12);

        $username = $base;
        $i = 2;
        while (User::where('username', $username)->exists()) {
            $username = $base . $i;
            $i++;
        }

        return $username;
    }

    protected function createManagerEmployee(Department $dept, Position $position, $username)
    {
        $nik = 'MNG-' . strtoupper($dept->kode_department ?? 'GEN') . '-' . random_int(1000, 9999);
        while (Employee::where('nik', $nik)->exists()) {
            $nik = 'MNG-' . strtoupper($dept->kode_department ?? 'GEN') . '-' . random_int(1000, 9999);
        }

        return Employee::create([
            'nik' => $nik,
            'nama_lengkap' => 'Manager ' . $dept->nama_department,
            'email' => $username . '@example.com',
            'department_id' => $dept->id,
            'position_id' => $position->id,
            'status_kerja' => 'tetap',
            'status' => 'aktif',
            'gaji_pokok' => 0,
            'is_cv_approved' => true,
        ]);
    }
}
