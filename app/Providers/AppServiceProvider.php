<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Employee;
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
        Gate::define('manage-payslip', function (User $user, Employee $targetEmployee) {
            if ($user->role === 'super_admin') return true;
            if (!$targetEmployee->user) return true;
            return $user->hierarchyLevel() > $targetEmployee->user->hierarchyLevel();
        });

        Gate::define('manage-allowance', function (User $user, Employee $targetEmployee) {
            if ($user->role === 'super_admin') return true;
            if (!$targetEmployee->user) return true;
            return $user->hierarchyLevel() > $targetEmployee->user->hierarchyLevel();
        });

        Gate::define('manageTraining', function (User $user) {
            return in_array($user->role, ['super_admin', 'hr_manager', 'manager_departemen']);
        });

        Gate::define('approveCV', function (User $user) {
            return in_array($user->role, ['hr_manager', 'manager_departemen']);
        });

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
