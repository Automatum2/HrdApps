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

