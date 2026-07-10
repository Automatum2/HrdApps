<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    if (session()->has('user_role')) {
        return redirect()->route('backoffice.dashboard');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $role = $request->input('role', 'manager');
    
    if ($role === 'super_admin') {
        session([
            'user_role' => 'super_admin',
            'user_name' => 'Super Admin',
            'user_photo' => ''
        ]);
    } elseif ($role === 'manager') {
        session([
            'user_role' => 'manager',
            'user_name' => 'Budi Santoso',
            'user_photo' => 'https://lh3.googleusercontent.com/aida/AP1WRLs3mGjsESMPhmv8tzbwL4Cv8eybl3L-pVFuT7bSZKkYATCbB3SohTtuQWEIJDs_lr89hffZfJdshr0JX6-tHTDP0Q5kvayq-J4PoHfMmI2WZkUVxA2N0VBZS0aU3saEKTTh3VmVA36ZoHnDvLB1iKE8iL_q31WiqrXHIprZpUQt_qGXEWo-wL-jx_CZkSqMHy8mg2AR9Lfxbyvc-04EzQfgbgVhuWhp89YgVQXF0zjbCgKQFyA8qEgUjHo'
        ]);
    } else {
        session([
            'user_role' => 'employee',
            'user_name' => 'Ahmad Fadillah',
            'employee_id' => '00001221',
            'user_photo' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDt-Tp2JvaAamqP87-OVpjjXEHNsk39BuXwtYQrACb2iSUBqXcY4j4GS_lbc-lFLjnPOljiiQMEDIsffSHY3s6oY3HUAj6CTaQPrCVOq_LsEtUhqb9tMb9dhjFZC9ySMxQwgFXumtngt_itDiohSclLfVA-xeMWsBSoibPuh_aUqZK6HMGq70yyVKGZgzYaBX5HTCjUJHtOso0i8CF57cq5cVDamADpt3IQPAuJZ4X6NoTFcs3vD6O1'
        ]);
    }
    
    return redirect()->route('backoffice.dashboard');
})->name('login.post');

Route::get('/logout', function () {
    session()->flush();
    return redirect()->route('login');
})->name('logout');

Route::get('/backoffice/dashboard', function () {
    $role = session('user_role', 'manager');
    if ($role === 'super_admin') {
        return view('backoffice.super_admin_dashboard');
    } elseif ($role === 'employee') {
        return view('backoffice.dashboard_karyawan');
    }
    return view('backoffice.dashboard');
})->name('backoffice.dashboard');

Route::get('/backoffice/super-admin/kelola-hr', function () {
    if (session('user_role') !== 'super_admin') {
        return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak. Halaman ini hanya untuk Super Admin.');
    }
    return view('backoffice.super_admin_kelola_hr');
})->name('backoffice.super_admin.kelola_hr');

Route::get('/backoffice/super-admin/kelola-karyawan', function () {
    if (session('user_role') !== 'super_admin') {
        return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak. Halaman ini hanya untuk Super Admin.');
    }
    return view('backoffice.super_admin_kelola_karyawan');
})->name('backoffice.super_admin.kelola_karyawan');

Route::get('/backoffice/karyawan', function () {
    // Proteksi Role: Hanya dapat diakses oleh Manager
    if (session('user_role', 'manager') !== 'manager') {
        return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak. Halaman Karyawan hanya dapat diakses oleh Manager.');
    }
    return view('backoffice.karyawan');
})->name('backoffice.karyawan');

Route::get('/backoffice/absensi', function () {
    // Proteksi Role: Hanya dapat diakses oleh Manager
    if (session('user_role', 'manager') !== 'manager') {
        return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak. Halaman Absensi hanya dapat diakses oleh Manager.');
    }
    return view('backoffice.absensi');
})->name('backoffice.absensi');

Route::get('/backoffice/penggajian', function () {
    return view('backoffice.penggajian');
})->name('backoffice.penggajian');

Route::get('/backoffice/laporan', function () {
    // Proteksi Role: Hanya dapat diakses oleh Manager
    if (session('user_role', 'manager') !== 'manager') {
        return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak. Halaman Laporan hanya dapat diakses oleh Manager.');
    }
    return view('backoffice.laporan');
})->name('backoffice.laporan');

Route::get('/backoffice/pengaturan', function () {
    return view('backoffice.pengaturan');
})->name('backoffice.pengaturan');

