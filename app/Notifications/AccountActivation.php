<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountActivation extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
            'type'  => 'activation', // Parameter to differentiate from normal reset
        ], false));

        return (new MailMessage)
            ->subject('Selamat Datang di HRDApps!')
            ->greeting('Halo!')
            ->line('Akun Anda telah berhasil didaftarkan di sistem HRDApps oleh administrator.')
            ->line('Silakan klik tombol di bawah ini untuk mengaktifkan akun Anda sekaligus membuat kata sandi pertama Anda.')
            ->action('Aktivasi Akun', $url)
            ->line('Tautan aktivasi ini akan kedaluwarsa dalam ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire') . ' menit.')
            ->salutation('Salam hangat, Tim HRDApps');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
