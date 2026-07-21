<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            $roleMap = [
                'super_admin' => 'super_admin',
                'hr_manager' => 'manager',
                'karyawan' => 'employee'
            ];

            session([
                'user_role' => $roleMap[$user->role] ?? 'employee',
                'user_name' => $user->username,
                'employee_id' => $user->employee_id ?? '0000',
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
