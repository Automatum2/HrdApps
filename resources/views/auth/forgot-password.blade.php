@extends('layouts.guest')

@section('title', 'Lupa Password - HRDApps Management System')

@section('content')
<!-- Floating Background Orbs -->
<div class="floating-orb orb-1"></div>
<div class="floating-orb orb-2"></div>
<div class="floating-orb orb-3"></div>

<!-- Login Container -->
<main class="w-full max-w-[440px] animate-in fade-in slide-in-from-bottom-4 duration-700 relative z-10">
    <div class="login-card bg-surface-container-lowest rounded-xl p-xl flex flex-col items-center">
        <!-- Logo Section -->
        <div class="mb-lg flex flex-col items-center text-center">
            <div class="w-20 h-20 mb-md rounded-full overflow-hidden bg-primary flex items-center justify-center logo-shine shadow-lg border-2 border-primary-container">
                <span class="text-white text-4xl font-extrabold font-display" style="font-family: 'Inter', sans-serif;">H</span>
            </div>
            <h1 class="font-display-lg text-display-lg text-primary tracking-tight">HRDApps</h1>
        </div>
        
        <!-- Prompt -->
        <div class="w-full mb-lg text-center">
            <h2 class="font-title-sm text-title-sm text-on-surface-variant">Lupa Password?</h2>
            <p class="text-sm text-secondary mt-2">Masukkan email Anda yang terdaftar, kami akan mengirimkan link untuk mereset password Anda.</p>
        </div>
        
        <!-- Form -->
        <form class="w-full space-y-md" action="{{ route('password.email') }}" method="POST">
            @csrf
            
            @if($errors->any())
                <div class="bg-error/10 text-error px-4 py-2 rounded-lg text-sm mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            <!-- Email Field -->
            <div class="space-y-base">
                <label class="font-label-uppercase text-label-uppercase text-on-surface-variant px-1" for="email">Email</label>
                <div class="flex items-center border border-outline-variant rounded-lg bg-white overflow-hidden input-focus-ring transition-all">
                    <div class="flex items-center justify-center w-12 h-11 bg-surface-container-low text-secondary">
                        <span class="material-symbols-outlined">mail</span>
                    </div>
                    <input class="flex-1 px-md py-sm border-none focus:ring-0 font-body-md text-on-surface placeholder:text-outline" id="email" name="email" placeholder="contoh@email.com" type="email" required>
                </div>
            </div>
            
            <!-- Submit Button -->
            <button class="w-full bg-primary hover:bg-primary-container text-on-primary font-headline-md text-headline-md py-3 rounded-lg shadow-sm transform active:scale-[0.98] transition-all duration-200 btn-ripple mt-4" type="submit">
                Kirim Kode OTP
            </button>
            
            <!-- Back to Login -->
            <div class="text-center mt-4">
                <a class="font-body-sm text-body-sm text-primary hover:underline font-semibold" href="{{ route('login') }}">Kembali ke Login</a>
            </div>
        </form>
    </div>
</main>
@endsection
