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

            if ($user->employee && $user->employee->status === 'nonaktif') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'username' => 'Akun Anda telah dinonaktifkan oleh administrator.',
                ])->onlyInput('username');
            }

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
