<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetSuperadminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'superadmin:reset {password=Admin12345!}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset password akun Super Admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $newPassword = $this->argument('password');

        $admin = User::where('role', 'super_admin')->first();

        if (!$admin) {
            $admin = User::create([
                'username' => 'superadmin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make($newPassword),
                'role' => 'super_admin',
            ]);
            $this->info("Akun Super Admin baru berhasil dibuat.");
        } else {
            $admin->password = Hash::make($newPassword);
            $admin->save();
            $this->info("Password Super Admin berhasil di-reset.");
        }

        $this->table(
            ['Username', 'Email', 'Password Baru'],
            [[$admin->username, $admin->email, $newPassword]]
        );

        return Command::SUCCESS;
    }
}

