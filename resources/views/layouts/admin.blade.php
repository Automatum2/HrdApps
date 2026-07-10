<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'HRDApps Management Portal')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background-color: #f8f9ff;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
        }
        .sidebar-active-gradient {
            background: linear-gradient(90deg, rgba(0, 102, 255, 0.15) 0%, rgba(0, 102, 255, 0) 100%);
        }
        .card-shadow {
            box-shadow: 0px 4px 20px -4px rgba(0, 0, 0, 0.05);
        }
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-background text-on-background font-body-md min-h-screen">
    <!-- Side Navigation Shell -->
    <aside class="fixed left-0 top-0 h-screen w-sidebar-width bg-[#1e293b] border-r border-outline-variant flex flex-col py-4 z-50 justify-between">
        <div class="px-6 mb-10 flex items-center gap-3">
            <img alt="HRDApps Logo" class="w-10 h-10 rounded-lg shadow-lg" src="https://lh3.googleusercontent.com/aida/AP1WRLvpFHUCy_Yq6LtQqNmX6ag1aIQKY-BWSHyohEUquy3wK6mK85LadnnQrcjTEKOl-u_Di5Vu_1inn1-FwSS_RsfRS_lxkWf7dUun0xT-mZ4hif9k1elklSKL6tnImnswj7HdtCeFyE7ZDzAGtf2O8_P3wIDQBduk0NNjEqw58GjppC2mqBIK_gipbo4FFeiiuaNQLvWr-HV4Ke8Zho1gzNOMGkijIz0wKaS-Soge8ZsRPwXNtquAQwv47ow">
            <div>
                <h1 class="font-headline-md text-headline-md font-extrabold text-white leading-none">HRDApps</h1>
                <p class="text-[10px] text-white/70 uppercase tracking-widest mt-1">Management Portal</p>
            </div>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-2">
            @php
                $role = session('user_role', 'manager');
            @endphp

            <!-- MENU UNTUK SUPER ADMIN -->
            @if($role === 'super_admin')
            <!-- Dashboard Super Admin -->
            <a class="{{ request()->routeIs('backoffice.dashboard') ? 'bg-primary/10 text-primary border-l-4 border-primary font-bold' : 'text-white hover:bg-white/10' }} flex items-center px-4 py-3 transition-colors duration-200 group" href="{{ route('backoffice.dashboard') }}">
                <span class="material-symbols-outlined mr-3 text-xl {{ request()->routeIs('backoffice.dashboard') ? 'text-primary animate-sidebar-pulse' : 'text-white/75 group-hover:text-white' }}" style="font-variation-settings: 'FILL' 1;">dashboard</span>
                <span class="font-body-md font-bold">Dashboard</span>
            </a>

            <!-- Karyawan (Super Admin) -->
            <a class="{{ request()->routeIs('backoffice.super_admin.kelola_karyawan') ? 'bg-primary/10 text-primary border-l-4 border-primary font-bold' : 'text-white hover:bg-white/10' }} flex items-center px-4 py-3 transition-colors duration-200 group" href="{{ route('backoffice.super_admin.kelola_karyawan') }}">
                <span class="material-symbols-outlined mr-3 text-xl {{ request()->routeIs('backoffice.super_admin.kelola_karyawan') ? 'text-primary animate-sidebar-pulse' : 'text-white/75 group-hover:text-white' }}" style="font-variation-settings: 'FILL' 1;">groups</span>
                <span class="font-medium font-body-md">Karyawan</span>
            </a>

            <!-- Kelola HR Manager -->
            <a class="{{ request()->routeIs('backoffice.super_admin.kelola_hr') ? 'bg-primary/10 text-primary border-l-4 border-primary font-bold' : 'text-white hover:bg-white/10' }} flex items-center px-4 py-3 transition-colors duration-200 group" href="{{ route('backoffice.super_admin.kelola_hr') }}">
                <span class="material-symbols-outlined mr-3 text-xl {{ request()->routeIs('backoffice.super_admin.kelola_hr') ? 'text-primary animate-sidebar-pulse' : 'text-white/75 group-hover:text-white' }}">manage_accounts</span>
                <span class="font-medium font-body-md">Kelola HR Manager</span>
            </a>
            
            <!-- MENU UNTUK MANAGER DAN EMPLOYEE -->
            @else
            <!-- Dashboard Manager / Karyawan -->
            <a class="{{ request()->routeIs('backoffice.dashboard') ? 'bg-primary/10 text-primary border-l-4 border-primary font-bold' : 'text-white hover:bg-white/10' }} flex items-center px-4 py-3 transition-colors duration-200 group" href="{{ route('backoffice.dashboard') }}">
                <span class="material-symbols-outlined mr-3 text-xl {{ request()->routeIs('backoffice.dashboard') ? 'text-primary animate-sidebar-pulse' : 'text-white/75 group-hover:text-white' }}" style="font-variation-settings: 'FILL' 1;">dashboard</span>
                <span class="font-body-md font-bold">Dashboard</span>
            </a>

            @if($role === 'manager')
            <!-- Menu Karyawan -->
            <a class="{{ request()->routeIs('backoffice.karyawan') ? 'bg-primary/10 text-primary border-l-4 border-primary font-bold' : 'text-white hover:bg-white/10' }} flex items-center px-4 py-3 transition-colors duration-200 group" href="{{ route('backoffice.karyawan') }}">
                <span class="material-symbols-outlined mr-3 text-xl {{ request()->routeIs('backoffice.karyawan') ? 'text-primary animate-sidebar-pulse' : 'text-white/75 group-hover:text-white' }}">group</span>
                <span class="font-medium font-body-md">Karyawan</span>
            </a>
            @endif

            @if($role === 'manager')
            <!-- Menu Absensi -->
            <a class="{{ request()->routeIs('backoffice.absensi') ? 'bg-primary/10 text-primary border-l-4 border-primary font-bold' : 'text-white hover:bg-white/10' }} flex items-center px-4 py-3 transition-colors duration-200 group" href="{{ route('backoffice.absensi') }}">
                <span class="material-symbols-outlined mr-3 text-xl {{ request()->routeIs('backoffice.absensi') ? 'text-primary animate-sidebar-pulse' : 'text-white/75 group-hover:text-white' }}">date_range</span>
                <span class="font-medium font-body-md">Absensi</span>
            </a>
            @endif

            <!-- Menu Penggajian -->
            <a class="{{ request()->routeIs('backoffice.penggajian') ? 'bg-primary/10 text-primary border-l-4 border-primary font-bold' : 'text-white hover:bg-white/10' }} flex items-center px-4 py-3 transition-colors duration-200 group" href="{{ route('backoffice.penggajian') }}">
                <span class="material-symbols-outlined mr-3 text-xl {{ request()->routeIs('backoffice.penggajian') ? 'text-primary animate-sidebar-pulse' : 'text-white/75 group-hover:text-white' }}">payments</span>
                <span class="font-medium font-body-md">Penggajian</span>
            </a>

            @if($role === 'manager')
            <!-- Menu Laporan -->
            <a class="{{ request()->routeIs('backoffice.laporan') ? 'bg-primary/10 text-primary border-l-4 border-primary font-bold' : 'text-white hover:bg-white/10' }} flex items-center px-4 py-3 transition-colors duration-200 group" href="{{ route('backoffice.laporan') }}">
                <span class="material-symbols-outlined mr-3 text-xl {{ request()->routeIs('backoffice.laporan') ? 'text-primary animate-sidebar-pulse' : 'text-white/75 group-hover:text-white' }}">analytics</span>
                <span class="font-medium font-body-md">Laporan</span>
            </a>
            @endif

            <!-- Menu Pengaturan -->
            <a class="{{ request()->routeIs('backoffice.pengaturan') ? 'bg-primary/10 text-primary border-l-4 border-primary font-bold' : 'text-white hover:bg-white/10' }} flex items-center px-4 py-3 transition-colors duration-200 group" href="{{ route('backoffice.pengaturan') }}">
                <span class="material-symbols-outlined mr-3 text-xl {{ request()->routeIs('backoffice.pengaturan') ? 'text-primary animate-sidebar-pulse' : 'text-white/75 group-hover:text-white' }}">settings</span>
                <span class="font-medium font-body-md">Pengaturan</span>
            </a>
            @endif
        </nav>

        @php
            $userName = session('user_name', 'Budi Santoso');
            $userRole = session('user_role', 'manager');
            $userPhoto = session('user_photo', 'https://lh3.googleusercontent.com/aida/AP1WRLs3mGjsESMPhmv8tzbwL4Cv8eybl3L-pVFuT7bSZKkYATCbB3SohTtuQWEIJDs_lr89hffZfJdshr0JX6-tHTDP0Q5kvayq-J4PoHfMmI2WZkUVxA2N0VBZS0aU3saEKTTh3VmVA36ZoHnDvLB1iKE8iL_q31WiqrXHIprZpUQt_qGXEWo-wL-jx_CZkSqMHy8mg2AR9Lfxbyvc-04EzQfgbgVhuWhp89YgVQXF0zjbCgKQFyA8qEgUjHo');
            
            if ($userRole === 'super_admin') {
                $userTitle = 'Administrator';
            } elseif ($userRole === 'manager') {
                $userTitle = 'Manager HRD';
            } else {
                $userTitle = 'Employee ID: ' . session('employee_id', '00001221');
            }
        @endphp
        <div class="px-2 mt-auto mb-2">
            <!-- Profil Singkat User -->
            <div class="px-4 py-4 mb-2 mx-2 rounded-xl bg-white/5 border border-white/10 flex items-center gap-3">
                @if($userPhoto)
                    <img alt="{{ $userName }}" class="w-10 h-10 rounded-full border border-outline-variant object-cover" src="{{ $userPhoto }}">
                @else
                    <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold text-lg border border-white/10 shadow-sm">
                        {{ strtoupper(substr($userName, 0, 1)) }}
                    </div>
                @endif
                <div class="overflow-hidden">
                    <p class="text-sm font-bold text-white truncate">{{ $userName }}</p>
                    <p class="text-[10px] text-white/70 uppercase tracking-wider truncate">{{ $userTitle }}</p>
                </div>
            </div>
            
            <!-- Tombol Keluar -->
            <div class="px-2 mb-2">
                <a class="text-white hover:text-error hover:bg-error/10 flex items-center px-4 py-3 rounded-lg transition-colors duration-200 group" href="{{ route('logout') }}">
                    <span class="material-symbols-outlined mr-3 text-xl text-white/75 group-hover:text-error">logout</span>
                    <span class="font-medium font-body-md">Keluar</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="ml-sidebar-width flex flex-col min-h-screen">
        <!-- Top App Bar -->
        <header class="flex items-center justify-between px-8 h-16 bg-surface border-b border-outline-variant sticky top-0 z-40 shadow-sm bg-white">
            <div class="flex items-center gap-4">
                <h2 class="font-headline-md text-headline-md text-primary font-bold">@yield('page_title', 'Dashboard')</h2>
                <div class="relative hidden lg:block ml-4">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
                    <input class="bg-surface-container-low border border-outline-variant rounded-full pl-10 pr-5 py-2 text-body-md w-64 focus:ring-2 focus:ring-primary/20 transition-all outline-none text-on-surface" placeholder="Search data..." type="text">
                </div>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-4">
                    <button class="relative p-2 hover:bg-surface-container-low rounded-full transition-colors active:opacity-80">
                        <span class="material-symbols-outlined text-on-surface-variant">notifications</span>
                        <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-error rounded-full border-2 border-surface"></span>
                    </button>
                    <button class="relative p-2 hover:bg-surface-container-low rounded-full transition-colors active:opacity-80">
                        <span class="material-symbols-outlined text-on-surface-variant">help</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Canvas -->
        <div class="p-8 space-y-8 max-w-container-max mx-auto w-full animate-page-in">
            @yield('content')
        </div>
    </main>

    @stack('modals')
    @stack('scripts')
</body>
</html>
