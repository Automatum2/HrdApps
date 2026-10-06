@extends('layouts.guest')

@section('title', 'Verifikasi OTP - HRDApps Management System')

@section('content')
<!-- Floating Background Orbs -->
<div class="floating-orb orb-1"></div>
<div class="floating-orb orb-2"></div>
<div class="floating-orb orb-3"></div>

<!-- Login Container -->
<main class="auth-container w-full max-w-md max-w-[440px] mx-auto animate-in fade-in slide-in-from-bottom-4 duration-700 relative z-10" style="max-width: 440px; width: 100%;">
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
            <h2 class="font-title-sm text-title-sm text-on-surface-variant">Verifikasi Kode OTP</h2>
            <p class="text-sm text-secondary mt-2">Masukkan 6-digit kode OTP yang telah dikirim ke email <strong>{{ $email }}</strong></p>
        </div>
        
        @if(session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm mb-4 w-full">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form Verify OTP -->
        <form class="w-full space-y-md" action="{{ route('password.verify-otp.post') }}" method="POST">
            @csrf
            
            <input type="hidden" name="email" value="{{ $email }}">
            
            @if($errors->has('otp'))
                <div class="bg-error/10 text-error px-4 py-2 rounded-lg text-sm mb-4">
                    {{ $errors->first('otp') }}
                </div>
            @endif
            
            <!-- OTP Field -->
            <div class="space-y-base">
                <label class="font-label-uppercase text-label-uppercase text-on-surface-variant px-1" for="otp">Kode OTP</label>
                <div class="flex items-center border border-outline-variant rounded-lg bg-white overflow-hidden input-focus-ring transition-all">
                    <div class="flex items-center justify-center w-12 h-11 bg-surface-container-low text-secondary">
                        <span class="material-symbols-outlined">pin</span>
                    </div>
                    <input class="flex-1 px-md py-sm border-none focus:ring-0 font-body-md text-on-surface placeholder:text-outline text-center tracking-widest text-lg" id="otp" name="otp" placeholder="------" type="text" maxlength="6" required autofocus>
                </div>
            </div>
            
            <!-- Submit Button -->
            <button class="w-full bg-primary hover:bg-primary-container text-on-primary font-headline-md text-headline-md py-3 rounded-lg shadow-sm transform active:scale-[0.98] transition-all duration-200 btn-ripple mt-4" type="submit">
                Verifikasi OTP
            </button>
        </form>

        <div class="w-full h-px bg-outline-variant my-lg"></div>

        <!-- Form Resend OTP -->
        <div class="w-full text-center space-y-3">
            <p class="text-sm text-secondary">Belum menerima kode OTP?</p>
            <form id="resend-form" action="{{ route('password.email') }}" method="POST">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit" id="resend-btn" class="font-body-sm text-body-sm text-outline cursor-not-allowed bg-transparent border-none p-0 m-0" disabled>
                    Kirim Ulang Kode OTP (<span id="countdown">60</span>s)
                </button>
            </form>
            
            <div class="mt-4 pt-4">
                <a class="font-body-sm text-body-sm text-outline hover:text-on-surface-variant transition-colors" href="{{ route('login') }}">Kembali ke Login</a>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const resendBtn = document.getElementById('resend-btn');
        const countdownSpan = document.getElementById('countdown');
        let timeLeft = 60;
        
        const timer = setInterval(() => {
            timeLeft--;
            if (countdownSpan) {
                countdownSpan.textContent = timeLeft;
            }
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                resendBtn.disabled = false;
                resendBtn.innerHTML = 'Kirim Ulang Kode OTP';
                resendBtn.classList.remove('text-outline', 'cursor-not-allowed');
                resendBtn.classList.add('text-primary', 'hover:underline', 'font-semibold', 'cursor-pointer');
            }
        }, 1000);
        
        resendBtn.addEventListener('click', function() {
            // Optional: disable immediately upon click to prevent double submission
            if (!this.disabled) {
                this.disabled = true;
                this.classList.remove('text-primary', 'hover:underline', 'font-semibold', 'cursor-pointer');
                this.classList.add('text-outline', 'cursor-not-allowed');
                this.innerHTML = 'Memproses...';
                document.getElementById('resend-form').submit();
            }
        });
    });
</script>
@endsection
