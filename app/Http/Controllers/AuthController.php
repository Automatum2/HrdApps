<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = $request->input('username');
        $loginType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $loginInput,
            'password' => $request->input('password')
        ];

        \Illuminate\Support\Facades\Log::info('Login attempt', ['login' => $loginInput]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Pengecekan otomatis: Nonaktifkan akun karyawan jika tidak ada login selama > 7 hari
            // Catatan: Hanya berlaku jika akun sudah pernah memiliki riwayat login sebelumnya (last_login tidak null)
            if ($user->role === 'karyawan') {
                $threshold = now()->subDays(7);
                $isInactive = false;

                if ($user->last_login && $user->last_login < $threshold) {
                    $isInactive = true;
                }

                if ($isInactive) {
                    if ($user->employee) {
                        $user->employee->update(['status' => 'nonaktif']);
                    }
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return back()->withErrors([
                        'username' => 'Akun Anda dinonaktifkan otomatis oleh sistem karena tidak ada aktivitas login selama lebih dari 7 hari. Silakan hubungi administrator.',
                    ])->onlyInput('username');
                }
            }

            // Pemulihan otomatis jika karyawan lama ter-nonaktifkan keliru karena belum memiliki last_login
            if ($user->employee && $user->employee->status === 'nonaktif') {
                if ($user->employee->department_id && is_null($user->last_login)) {
                    $user->employee->update(['status' => 'aktif']);
                    $user->update(['last_login' => now()]);
                } else {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return back()->withErrors([
                        'username' => 'Akun Anda telah dinonaktifkan oleh administrator.',
                    ])->onlyInput('username');
                }
            }

            // Catat waktu login terakhir
            $user->update(['last_login' => now()]);

            $request->session()->regenerate();

            session([
                'user_role' => $user->role,
                'user_name' => $user->username,
                'employee_id' => $user->employee_id,
                'user_photo' => ''
            ]);

            return redirect()->intended('/backoffice/dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
