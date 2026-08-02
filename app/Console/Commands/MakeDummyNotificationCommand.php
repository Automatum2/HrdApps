<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Notifications\Notification;

class DummyNotification extends Notification
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return $this->data;
    }
}

class MakeDummyNotificationCommand extends Command
{
    protected $signature = 'make:dummy-notification';
    protected $description = 'Generate dummy notifications for testing';

    public function handle()
    {
        $users = User::all();
        foreach ($users as $user) {
            $user->notify(new DummyNotification([
                'title' => 'Pengingat Absensi',
                'message' => 'Anda belum melakukan absen keluar (Clock Out) untuk hari ini.',
                'icon' => 'warning',
            ]));
            
            $user->notify(new DummyNotification([
                'title' => 'Slip Gaji Tersedia',
                'message' => 'Slip gaji untuk periode bulan ini sudah dapat diunduh.',
                'icon' => 'payments',
            ]));
        }

        $this->info('Dummy notifications generated successfully.');
    }
}
