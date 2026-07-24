<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject(Lang::get('Notifikasi Reset Kata Sandi'))
                ->greeting(Lang::get('Halo!'))
                ->line(Lang::get('Anda menerima email ini karena kami menerima permintaan reset kata sandi untuk akun Anda.'))
                ->action(Lang::get('Reset Kata Sandi'), $url)
                ->line(Lang::get('Tautan reset kata sandi ini akan kedaluwarsa dalam :count menit.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
                ->line(Lang::get('Jika Anda tidak meminta reset kata sandi, abaikan saja email ini.'))
                ->salutation(Lang::get('Salam hangat, Tim HRDApps'));
        });
    }
}
