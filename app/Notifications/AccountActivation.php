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
    public $otp;

    /**
     * Create a new notification instance.
     */
    public function __construct($token, $otp = null)
    {
        $this->token = $token;
        $this->otp = $otp;
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

        $mail = (new MailMessage)
            ->subject('Selamat Datang di HRDApps - Aktivasi Akun Anda')
            ->greeting('Halo, ' . ($notifiable->employee ? $notifiable->employee->nama_lengkap : 'Kandidat') . '!')
            ->line('Selamat! Lamaran Anda telah disetujui dan akun Anda telah didaftarkan di sistem HRDApps.')
            ->line('Untuk mengaktifkan akun dan membuat kata sandi Anda, silakan gunakan tautan dan kode OTP di bawah ini:');

        if ($this->otp) {
            $mail->line('Kode OTP Aktivasi Anda: ' . $this->otp);
            $mail->line('(Gunakan kode OTP ini saat melakukan aktivasi akun)');
        }

        return $mail
            ->action('Aktivasi Akun & Buat Password', $url)
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
