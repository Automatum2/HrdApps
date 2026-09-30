@extends('layouts.guest')

@section('title', 'Login - HRDApps Management System')

@section('content')
<main class="w-full max-w-[420px] relative z-10 py-6">
    <div class="bg-white rounded-2xl border border-slate-200/80 p-8 shadow-sm">
        <!-- Logo Section -->
        <div class="mb-6 flex flex-col items-center text-center">
            <div class="w-14 h-14 mb-3 rounded-xl bg-blue-600 flex items-center justify-center shadow-md">
                <img src="{{ asset('images/logo.svg') }}" alt="HRDApps Logo" class="w-8 h-8 object-contain">
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">HRDApps</h1>
            <p class="text-xs text-slate-500 mt-1 font-medium">Human Resource Digital System</p>
        </div>
        
        <!-- Login Prompt -->
        <div class="w-full mb-6">
            <h2 class="text-sm font-semibold text-slate-700 text-center">Masuk ke akun Anda</h2>
        </div>
        
        <!-- Form -->
        <form class="w-full space-y-4" action="{{ route('login.post') }}" method="POST">
            @csrf
            
            @if($errors->any())
                <div class="bg-red-50 text-red-700 border border-red-200 px-4 py-3 rounded-xl text-xs font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 text-green-700 border border-green-200 px-4 py-3 rounded-xl text-xs font-semibold">
                    {{ session('success') }}
                </div>
            @endif
            
            <!-- Username Field -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700" for="username">Username atau Email</label>
                <div class="flex items-center border border-slate-300 rounded-xl bg-slate-50/50 focus-within:border-blue-600 focus-within:bg-white focus-within:ring-2 focus-within:ring-blue-600/10 transition-all overflow-hidden">
                    <div class="flex items-center justify-center w-11 h-11 text-slate-400">
                        <span class="material-symbols-outlined text-lg">person</span>
                    </div>
                    <input class="flex-1 pr-4 py-2.5 bg-transparent border-none focus:outline-none text-sm text-slate-900 placeholder:text-slate-400 font-medium" id="username" name="username" placeholder="admin@perusahaan.com" type="text" value="{{ old('username') }}" required autofocus>
                </div>
            </div>
            
            <!-- Password Field -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700" for="password">Password</label>
                <div class="flex items-center border border-slate-300 rounded-xl bg-slate-50/50 focus-within:border-blue-600 focus-within:bg-white focus-within:ring-2 focus-within:ring-blue-600/10 transition-all overflow-hidden">
                    <div class="flex items-center justify-center w-11 h-11 text-slate-400">
                        <span class="material-symbols-outlined text-lg">lock</span>
                    </div>
                    <input class="flex-1 pr-4 py-2.5 bg-transparent border-none focus:outline-none text-sm text-slate-900 placeholder:text-slate-400 font-medium" id="password" name="password" placeholder="••••••••" type="password" required>
                </div>
            </div>
            
            <!-- Options -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-600/20 cursor-pointer" type="checkbox" name="remember">
                    <span class="text-xs font-medium text-slate-600 group-hover:text-slate-900 transition-colors">Ingat Saya</span>
                </label>
                <a class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline" href="{{ route('password.request') }}">Lupa Password?</a>
            </div>
            
            <!-- Submit Button -->
            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm py-3 rounded-xl shadow-sm hover:shadow transition-all active:scale-[0.99] cursor-pointer mt-2" type="submit">
                Masuk ke Portal
            </button>
        </form>
        
        <!-- Portal Karir link -->
        <div class="mt-6 pt-5 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">
                Ingin bergabung bersama kami?
                <a href="{{ route('karir.index') }}" class="font-bold text-blue-600 hover:text-blue-700 hover:underline ml-1">Kirim Lamaran (CV)</a>
            </p>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="mt-6 text-center">
        <p class="text-[11px] text-slate-400 font-medium">
            &copy; {{ date('Y') }} HRDApps Management System. All rights reserved.
        </p>
    </div>
</main>
@endsection