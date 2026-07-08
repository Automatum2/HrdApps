<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/backoffice/dashboard', function () {
    return view('backoffice.dashboard');
})->name('backoffice.dashboard');

Route::get('/backoffice/karyawan', function () {
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

