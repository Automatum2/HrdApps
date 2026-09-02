<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a department
        $deptId = \Illuminate\Support\Facades\DB::table('departments')->insertGetId([
            'kode_department' => 'IT',
            'nama_department' => 'IT Department',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create a position
        $posId = \Illuminate\Support\Facades\DB::table('positions')->insertGetId([
            'nama_jabatan' => 'Staff',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Super Admin
        \App\Models\User::create([
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('123456'),
            'role' => 'super_admin'
        ]);
    }
}
