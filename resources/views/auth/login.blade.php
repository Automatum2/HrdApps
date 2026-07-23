@extends('layouts.guest')

@section('title', 'Login - HRDApps Management System')

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
            <p class="font-body-md text-body-md text-secondary mt-1">Human Resource Digital Apps</p>
        </div>
        
        <!-- Login Prompt -->
        <div class="w-full mb-lg">
            <h2 class="font-title-sm text-title-sm text-on-surface-variant text-center">Sign in to start your session</h2>
        </div>
        
        <!-- Form -->
        <form class="w-full space-y-md" action="{{ route('login.post') }}" method="POST">
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
            
            <!-- Username Field -->
            <div class="space-y-base">
                <label class="font-label-uppercase text-label-uppercase text-on-surface-variant px-1" for="username">Username</label>
                <div class="flex items-center border border-outline-variant rounded-lg bg-white overflow-hidden input-focus-ring transition-all">
                    <div class="flex items-center justify-center w-12 h-11 bg-surface-container-low text-secondary">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                    <input class="flex-1 px-md py-sm border-none focus:ring-0 font-body-md text-on-surface placeholder:text-outline" id="username" name="username" placeholder="Enter your username" type="text" required>
                </div>
            </div>
            
            <!-- Password Field -->
            <div class="space-y-base">
                <label class="font-label-uppercase text-label-uppercase text-on-surface-variant px-1" for="password">Password</label>
                <div class="flex items-center border border-outline-variant rounded-lg bg-white overflow-hidden input-focus-ring transition-all">
                    <div class="flex items-center justify-center w-12 h-11 bg-surface-container-low text-secondary">
                        <span class="material-symbols-outlined">lock</span>
                    </div>
                    <input class="flex-1 px-md py-sm border-none focus:ring-0 font-body-md text-on-surface placeholder:text-outline" id="password" name="password" placeholder="••••••••" type="password" required>
                </div>
            </div>
            

            
            <!-- Options -->
            <div class="flex items-center justify-between py-1">
                <label class="flex items-center gap-xs cursor-pointer group">
                    <input class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary/20 transition-all cursor-pointer" type="checkbox" name="remember">
                    <span class="font-body-sm text-body-sm text-on-surface-variant group-hover:text-primary transition-colors">Remember Me</span>
                </label>
                <a class="font-body-sm text-body-sm text-primary hover:underline font-semibold" href="#">Forgot Password?</a>
            </div>
            
            <!-- Submit Button -->
            <button class="w-full bg-primary hover:bg-primary-container text-on-primary font-headline-md text-headline-md py-3 rounded-lg shadow-sm transform active:scale-[0.98] transition-all duration-200 btn-ripple" type="submit">
                Log In
            </button>
        </form>
        
        <!-- Divider -->
        <div class="w-full h-px bg-outline-variant my-lg"></div>
        
        <!-- Footer Logo / Info -->
        <footer class="text-center">
            <p class="font-body-sm text-body-sm text-outline">Powered by <span class="font-semibold text-on-surface-variant">HRDApps.id</span></p>
        </footer>
    </div>
    
    <!-- Decorative atmospheric element -->
    <div class="mt-xl text-center">
        <p class="font-body-sm text-body-sm text-on-surface-variant/60">
            © 2026 HRDApps Management System. All rights reserved.
        </p>
    </div>
</main>

<script>
    // Interactivity removed since we want real form submission

    // Toggle focus state visuals for custom input wrappers if needed
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.classList.add('ring-2', 'ring-primary/20');
        });
        input.addEventListener('blur', () => {
            input.parentElement.classList.remove('ring-2', 'ring-primary/20');
        });
    });
</script>
@endsection
