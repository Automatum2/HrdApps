@extends('layouts.guest')

@section('title', (isset($type) && $type === 'activation' ? 'Aktivasi Akun' : 'Reset Kata Sandi') . ' - HRDApps Management System')

@section('content')
<!-- Floating Background Orbs -->
<div class="floating-orb orb-1"></div>
<div class="floating-orb orb-2"></div>
<div class="floating-orb orb-3"></div>

<!-- Container -->
<main class="auth-container w-full max-w-md max-w-[460px] mx-auto animate-in fade-in slide-in-from-bottom-4 duration-700 relative z-10 my-8" style="max-width: 460px; width: 100%;">
    <div class="login-card bg-surface-container-lowest rounded-xl p-xl flex flex-col items-center">
        <!-- Logo Section -->
        <div class="mb-lg flex flex-col items-center text-center">
            <div class="w-20 h-20 mb-md rounded-full overflow-hidden bg-primary flex items-center justify-center logo-shine shadow-lg border-2 border-primary-container">
                <span class="text-white text-4xl font-extrabold font-display" style="font-family: 'Inter', sans-serif;">H</span>
            </div>
            <h1 class="font-display-lg text-display-lg text-primary tracking-tight">HRDApps</h1>
            <p class="font-body-md text-body-md text-secondary mt-1">Human Resource Digital Apps</p>
        </div>
        
        <!-- Prompt Title -->
        <div class="w-full mb-lg text-center">
            @if(isset($type) && $type === 'activation')
                <h2 class="font-title-sm text-title-sm text-on-surface-variant font-bold">Aktivasi Akun Karyawan</h2>
                <p class="text-xs text-secondary mt-1">Masukkan kode OTP dari email dan buat password baru Anda.</p>
            @else
                <h2 class="font-title-sm text-title-sm text-on-surface-variant font-bold">Reset Kata Sandi</h2>
                <p class="text-xs text-secondary mt-1">Silakan buat password baru untuk mengakses akun Anda kembali.</p>
            @endif
        </div>
        
        <!-- Form -->
        <form class="w-full space-y-md" action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="type" value="{{ $type ?? 'reset' }}">
            
            @if($errors->any())
                <div class="bg-error/10 text-error px-4 py-2.5 rounded-lg text-xs mb-4">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-100 text-green-700 px-4 py-2.5 rounded-lg text-xs mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(isset($namaLengkap) && $namaLengkap)
            <!-- Nama Lengkap Field -->
            <div class="space-y-base">
                <label class="font-label-uppercase text-label-uppercase text-on-surface-variant px-1">Nama Lengkap</label>
                <div class="flex items-center border border-outline-variant rounded-lg bg-surface-container-low overflow-hidden">
                    <div class="flex items-center justify-center w-12 h-11 text-secondary">
                        <span class="material-symbols-outlined">badge</span>
                    </div>
                    <input class="flex-1 px-md py-sm bg-transparent border-none font-body-md text-on-surface font-semibold cursor-not-allowed" value="{{ $namaLengkap }}" disabled type="text">
                </div>
            </div>
            @endif

            <!-- Email Field -->
            <div class="space-y-base">
                <label class="font-label-uppercase text-label-uppercase text-on-surface-variant px-1">Email</label>
                <div class="flex items-center border border-outline-variant rounded-lg bg-surface-container-low overflow-hidden">
                    <div class="flex items-center justify-center w-12 h-11 text-secondary">
                        <span class="material-symbols-outlined">mail</span>
                    </div>
                    <input class="flex-1 px-md py-sm bg-transparent border-none font-body-md text-on-surface cursor-not-allowed" value="{{ $email }}" disabled type="email">
                </div>
            </div>

            <!-- Username Field -->
            <div class="space-y-base">
                <div class="flex justify-between items-center px-1">
                    <label class="font-label-uppercase text-label-uppercase text-on-surface-variant">Username</label>
                    <span class="text-[11px] text-primary font-medium">Gunakan ini untuk login</span>
                </div>
                <div class="flex items-center border border-primary/30 rounded-lg bg-primary/5 overflow-hidden">
                    <div class="flex items-center justify-center w-12 h-11 text-primary">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                    <input class="flex-1 px-md py-sm bg-transparent border-none font-body-md text-primary font-bold cursor-not-allowed select-all" value="{{ $username ?? 'Username belum dimuat' }}" disabled type="text">
                </div>
            </div>

            @if(isset($type) && $type === 'activation')
            <!-- Kode OTP Field (Hanya untuk flow Aktivasi Baru) -->
            <div class="space-y-base">
                <label class="font-label-uppercase text-label-uppercase text-primary font-bold px-1">Kode OTP Aktivasi (6 Digit)</label>
                <div class="flex items-center border-2 border-primary rounded-lg bg-white overflow-hidden input-focus-ring">
                    <div class="flex items-center justify-center w-12 h-11 bg-primary/10 text-primary">
                        <span class="material-symbols-outlined">pin</span>
                    </div>
                    <input class="flex-1 px-md py-sm border-none focus:ring-0 font-mono text-center tracking-[0.3em] font-bold text-lg text-primary placeholder:text-outline" name="otp" placeholder="123456" maxlength="6" pattern="[0-9]{6}" required type="text">
                </div>
            </div>
            @endif

            <!-- Password Baru Field -->
            <div class="space-y-base">
                <label class="font-label-uppercase text-label-uppercase text-on-surface-variant px-1" for="password">Password Baru</label>
                <div class="flex items-center border border-outline-variant rounded-lg bg-white overflow-hidden input-focus-ring transition-all">
                    <div class="flex items-center justify-center w-12 h-11 bg-surface-container-low text-secondary">
                        <span class="material-symbols-outlined">lock</span>
                    </div>
                    <input class="flex-1 px-md py-sm border-none focus:ring-0 font-body-md text-on-surface placeholder:text-outline" id="password" name="password" placeholder="Minimal 8 karakter" type="password" required>
                </div>
            </div>

            <!-- Konfirmasi Password Field -->
            <div class="space-y-base">
                <label class="font-label-uppercase text-label-uppercase text-on-surface-variant px-1" for="password_confirmation">Konfirmasi Password</label>
                <div class="flex items-center border border-outline-variant rounded-lg bg-white overflow-hidden input-focus-ring transition-all">
                    <div class="flex items-center justify-center w-12 h-11 bg-surface-container-low text-secondary">
                        <span class="material-symbols-outlined">lock_reset</span>
                    </div>
                    <input class="flex-1 px-md py-sm border-none focus:ring-0 font-body-md text-on-surface placeholder:text-outline" id="password_confirmation" name="password_confirmation" placeholder="Ketik ulang password baru" type="password" required>
                </div>
            </div>
            
            <!-- Submit Button -->
            <button class="w-full bg-primary hover:bg-primary-container text-on-primary font-headline-md text-headline-md py-3 rounded-lg shadow-sm transform active:scale-[0.98] transition-all duration-200 btn-ripple mt-6 flex items-center justify-center gap-2 cursor-pointer" type="submit">
                <span class="material-symbols-outlined text-lg">check_circle</span>
                <span>Simpan Password & Login</span>
            </button>
        </form>
    </div>
</main>
@endsection
