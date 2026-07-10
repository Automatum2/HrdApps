@extends('layouts.guest')

@section('title', 'Login - HRDApps Management System')

@section('content')
<!-- Login Container -->
<main class="w-full max-w-[440px] animate-in fade-in slide-in-from-bottom-4 duration-700">
    <div class="login-card bg-surface-container-lowest rounded-xl p-xl flex flex-col items-center">
        <!-- Logo Section -->
        <div class="mb-lg flex flex-col items-center text-center">
            <div class="w-20 h-20 mb-md rounded-full overflow-hidden bg-primary/5 flex items-center justify-center">
                <img alt="HRDApps Logo" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida/AP1WRLvpFHUCy_Yq6LtQqNmX6ag1aIQKY-BWSHyohEUquy3wK6mK85LadnnQrcjTEKOl-u_Di5Vu_1inn1-FwSS_RsfRS_lxkWf7dUun0xT-mZ4hif9k1elklSKL6tnImnswj7HdtCeFyE7ZDzAGtf2O8_P3wIDQBduk0NNjEqw58GjppC2mqBIK_gipbo4FFeiiuaNQLvWr-HV4Ke8Zho1gzNOMGkijIz0wKaS-Soge8ZsRPwXNtquAQwv47ow">
            </div>
            <h1 class="font-display-lg text-display-lg text-primary tracking-tight">HRDApps</h1>
            <p class="font-body-md text-body-md text-secondary mt-1">Human Resource Digital Apps</p>
        </div>
        
        <!-- Login Prompt -->
        <div class="w-full mb-lg">
            <h2 class="font-title-sm text-title-sm text-on-surface-variant text-center">Sign in to start your session</h2>
        </div>
        
        <!-- Form -->
        <form class="w-full space-y-md" action="{{ route('login.post') }}" method="POST" onsubmit="event.preventDefault();">
            @csrf
            
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
            
            <!-- Status Kerja Dropdown -->
            <div class="space-y-base">
                <label class="font-label-uppercase text-label-uppercase text-on-surface-variant px-1" for="status">Status Kerja</label>
                <div class="relative group">
                    <select class="w-full appearance-none px-md py-sm border border-outline-variant rounded-lg bg-white font-body-md text-on-surface focus:ring-2 focus:ring-primary focus:border-primary transition-all cursor-pointer" id="status" name="status">
                        <option value="WFO">WFO</option>
                        <option value="WFH">WFH</option>
                        <option value="WFF">WFF</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-md pointer-events-none text-secondary">
                        <span class="material-symbols-outlined">expand_more</span>
                    </div>
                </div>
            </div>

            <!-- Role Dropdown -->
            <div class="space-y-base">
                <label class="font-label-uppercase text-label-uppercase text-on-surface-variant px-1" for="role">Masuk Sebagai</label>
                <div class="relative group">
                    <select class="w-full appearance-none px-md py-sm border border-outline-variant rounded-lg bg-white font-body-md text-on-surface focus:ring-2 focus:ring-primary focus:border-primary transition-all cursor-pointer" id="role" name="role">
                        <option value="manager">HRD Manager (Budi Santoso)</option>
                        <option value="employee">Karyawan (Ahmad Fadillah)</option>
                        <option value="super_admin">Super Admin (Admin Utama)</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-md pointer-events-none text-secondary">
                        <span class="material-symbols-outlined">expand_more</span>
                    </div>
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
            <button class="w-full bg-primary hover:bg-primary-container text-on-primary font-headline-md text-headline-md py-3 rounded-lg shadow-sm transform active:scale-[0.98] transition-all duration-200" type="submit">
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
    // Simple interactivity for the login button
    document.querySelector('button[type="submit"]').addEventListener('click', function(e) {
        const form = document.querySelector('form');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        const btn = e.currentTarget;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="flex items-center justify-center gap-2"><svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Authenticating...</span>';
        btn.disabled = true;
        btn.classList.add('opacity-80', 'cursor-not-allowed');

        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.remove('opacity-80', 'cursor-not-allowed');
            
            // Submit form to Laravel POST route
            form.submit();
        }, 1500);
    });

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
