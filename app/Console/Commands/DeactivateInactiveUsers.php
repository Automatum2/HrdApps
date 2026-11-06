<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class DeactivateInactiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:deactivate-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nonaktifkan status karyawan yang akunnya tidak pernah login selama lebih dari 7 hari';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threshold = now()->subDays(7);

        // Ambil user karyawan yang memiliki catatan last_login dan lebih dari 7 hari yang lalu
        $inactiveUsers = User::where('role', 'karyawan')
            ->whereNotNull('last_login')
            ->where('last_login', '<', $threshold)
            ->whereHas('employee', function ($query) {
                $query->where('status', '!=', 'nonaktif');
            })
            ->with('employee')
            ->get();

        $count = 0;
        foreach ($inactiveUsers as $user) {
            if ($user->employee && $user->employee->status !== 'nonaktif') {
                $user->employee->update(['status' => 'nonaktif']);
                $count++;
                $this->info("Karyawan {$user->employee->nama_lengkap} (Username: {$user->username}) dinonaktifkan otomatis.");
            }
        }

        $this->info("Selesai. Total {$count} akun karyawan dinonaktifkan.");
        return Command::SUCCESS;
    }
}

