<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'HRDApps Management Portal')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">

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
        @media (min-width: 1024px) {
            .main-content-desktop {
                margin-left: 260px !important;
                width: calc(100% - 260px) !important;
            }
            .sidebar-mobile-hidden {
                transform: translateX(0) !important;
            }
            #mobile-menu-btn, #close-sidebar-btn {
                display: none !important;
            }
        }
        @media (max-width: 1023px) {
            .sidebar-mobile-hidden {
                transform: translateX(-100%);
            }
            .sidebar-mobile-visible {
                transform: translateX(0) !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-background text-on-background font-body-md min-h-screen">
    <!-- Overlay untuk Sidebar di Mobile -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden transition-opacity opacity-0 cursor-pointer"></div>

    <!-- Side Navigation Shell -->
    <aside id="sidebar" class="fixed left-0 top-0 h-screen w-[260px] bg-[#1e293b] border-r border-outline-variant flex flex-col py-4 z-50 justify-between transition-transform duration-300 sidebar-mobile-hidden">
        <div class="px-6 mb-10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img alt="HRDApps Logo" class="w-10 h-10 rounded-lg shadow-lg" src="{{ asset('images/logo.svg') }}">
                <div>
                    <h1 class="font-headline-md text-headline-md font-extrabold text-white leading-none">HRDApps</h1>
                    <p class="text-[10px] text-white/70 uppercase tracking-widest mt-1">Management Portal</p>
                </div>
            </div>
            <button id="close-sidebar-btn" class="lg:hidden text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors flex items-center justify-center">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
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

            <!-- Jabatan (Posisi) -->
            <a class="{{ request()->routeIs('backoffice.posisi.*') ? 'bg-primary/10 text-primary border-l-4 border-primary font-bold' : 'text-white hover:bg-white/10' }} flex items-center px-4 py-3 transition-colors duration-200 group" href="{{ route('backoffice.posisi.index') }}">
                <span class="material-symbols-outlined mr-3 text-xl {{ request()->routeIs('backoffice.posisi.*') ? 'text-primary animate-sidebar-pulse' : 'text-white/75 group-hover:text-white' }}">badge</span>
                <span class="font-medium font-body-md">Jabatan</span>
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
            $user = \Illuminate\Support\Facades\Auth::user();
            $userRole = session('user_role', 'manager');
            
            // Dapatkan nama lengkap asli dari database atau fallback ke username/session
            $userName = ($user && $user->employee) ? $user->employee->nama_lengkap : session('user_name', 'Budi Santoso');
            
            // Dapatkan foto
            if ($user && $user->employee && $user->employee->foto) {
                $userPhoto = asset('storage/' . $user->employee->foto);
            } else {
                $userPhoto = null;
            }
            
            // Dapatkan ID
            if ($userRole === 'super_admin') {
                $userTitle = 'Administrator';
            } elseif ($userRole === 'manager') {
                $userTitle = 'Manager HRD';
            } else {
                $employeeId = ($user && $user->employee) ? $user->employee->nik : session('employee_id', '00001221');
                $userTitle = 'Employee ID: ' . $employeeId;
            }
        @endphp
        <div class="px-2 mt-auto mb-2">
            <!-- Profil Singkat User -->
            <div class="px-4 py-4 mb-2 mx-2 rounded-xl bg-white/5 border border-white/10 flex items-center gap-3">
                @if($userPhoto)
                    <img alt="{{ $userName }}" class="w-10 h-10 shrink-0 rounded-full border border-outline-variant object-cover" src="{{ $userPhoto }}">
                @else
                    <div class="w-10 h-10 shrink-0 rounded-full bg-primary text-white flex items-center justify-center font-bold text-lg border border-white/10 shadow-sm">
                        {{ strtoupper(substr($userName, 0, 1)) }}
                    </div>
                @endif
                <div class="overflow-hidden">
                    <p class="text-sm font-bold text-white truncate">{{ $userName }}</p>
                    <p class="text-[10px] text-white/70 uppercase tracking-wider truncate">{{ $userTitle }}</p>
                    <!-- debug: {{ $userPhoto }} | user: {{ $user ? $user->username : 'none' }} | has_employee: {{ $user && $user->employee ? 'yes' : 'no' }} | foto: {{ $user && $user->employee ? $user->employee->foto : 'none' }} -->
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
    <main id="main-content" class="flex flex-col min-h-screen transition-all duration-300 w-full main-content-desktop relative">
        <!-- Top App Bar -->
        <header class="flex items-center justify-between px-4 lg:px-8 h-16 bg-surface border-b border-outline-variant sticky top-0 z-40 shadow-sm bg-white">
            <div class="flex items-center gap-4">
                <button id="mobile-menu-btn" class="lg:hidden p-2 text-on-surface hover:bg-surface-container-low rounded-lg transition-colors flex items-center">
                    <span class="material-symbols-outlined text-2xl">menu</span>
                </button>
                <h2 class="font-headline-md text-headline-md text-primary font-bold">@yield('page_title', 'Dashboard')</h2>
                {{-- Fitur Search (Dimatikan sementara) 
                <div class="relative hidden lg:block ml-4">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
                    <input class="bg-surface-container-low border border-outline-variant rounded-full pl-10 pr-5 py-2 text-body-md w-64 focus:ring-2 focus:ring-primary/20 transition-all outline-none text-on-surface" placeholder="Search data..." type="text">
                </div>
                --}}
            </div>
            
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2 lg:gap-4">
                    <!-- Notification System -->
                    <div class="relative">
                        @php
                            $user = \Illuminate\Support\Facades\Auth::user();
                            $unreadNotifications = $user ? $user->unreadNotifications : collect();
                            $unreadCount = $unreadNotifications->count();
                            $allNotifications = $user ? $user->notifications()->take(5)->get() : collect();
                        @endphp
                        
                        <button id="notification-btn" class="relative p-2 hover:bg-surface-container-low rounded-full transition-colors active:opacity-80 {{ $unreadCount > 0 ? 'animate-bell-swing' : '' }}">
                            <span class="material-symbols-outlined text-on-surface-variant">notifications</span>
                            @if($unreadCount > 0)
                                <span id="notification-badge" class="absolute top-1 right-1 w-4 h-4 bg-error text-white text-[9px] font-bold rounded-full flex items-center justify-center border-2 border-surface shadow-sm">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                            @endif
                        </button>

                        <!-- Notification Dropdown -->
                        <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-[320px] bg-white border border-outline-variant rounded-xl shadow-lg z-50 overflow-hidden flex flex-col origin-top-right transform transition-all duration-200 scale-95 opacity-0">
                            <!-- Header -->
                            <div class="px-4 py-3 border-b border-outline-variant flex items-center justify-between bg-surface">
                                <h3 class="font-bold text-sm text-on-surface">Notifikasi</h3>
                                @if($unreadCount > 0)
                                    <button id="mark-all-read-btn" class="text-primary text-[10px] font-bold hover:underline bg-primary/5 px-2 py-1 rounded-md transition-colors">Tandai semua dibaca</button>
                                @endif
                            </div>
                            
                            <!-- List -->
                            <div class="overflow-y-auto max-h-[350px] custom-scrollbar bg-surface-container-low/30">
                                @forelse($allNotifications as $notification)
                                    <div class="notification-item p-4 border-b border-outline-variant/50 hover:bg-surface-container-low transition-colors cursor-pointer {{ is_null($notification->read_at) ? 'bg-primary/5' : 'bg-white' }}" data-id="{{ $notification->id }}" data-read="{{ is_null($notification->read_at) ? 'false' : 'true' }}">
                                        <div class="flex gap-3">
                                            <div class="mt-0.5">
                                                <div class="w-8 h-8 rounded-full {{ is_null($notification->read_at) ? 'bg-primary text-white' : 'bg-surface-container-high text-on-surface-variant' }} flex items-center justify-center shadow-sm">
                                                    <span class="material-symbols-outlined text-[16px]">{{ $notification->data['icon'] ?? 'notifications' }}</span>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-start justify-between gap-2">
                                                    <p class="text-xs font-bold text-on-surface {{ is_null($notification->read_at) ? 'text-primary' : '' }}">{{ $notification->data['title'] ?? 'Notifikasi Sistem' }}</p>
                                                    @if(is_null($notification->read_at))
                                                        <div class="w-1.5 h-1.5 bg-error rounded-full mt-1 shrink-0 unread-dot"></div>
                                                    @endif
                                                </div>
                                                <p class="text-[10px] text-on-surface-variant mt-0.5 leading-relaxed">{{ $notification->data['message'] ?? '' }}</p>
                                                <p class="text-[9px] text-outline mt-1.5 font-medium flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[10px]">schedule</span>
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-4 py-10 text-center flex flex-col items-center justify-center bg-white">
                                        <div class="w-12 h-12 bg-surface-container-low rounded-full flex items-center justify-center mb-3">
                                            <span class="material-symbols-outlined text-outline text-2xl">notifications_paused</span>
                                        </div>
                                        <p class="text-xs font-bold text-on-surface mb-1">Belum ada notifikasi</p>
                                        <p class="text-[10px] text-on-surface-variant">Semua pemberitahuan akan muncul di sini.</p>
                                    </div>
                                @endforelse
                            </div>
                            
                            @if($allNotifications->count() > 0)
                            <div class="p-2 text-center border-t border-outline-variant bg-surface">
                                <a href="#" class="text-[10px] font-bold text-primary hover:underline">Lihat semua notifikasi</a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <button class="relative p-2 hover:bg-surface-container-low rounded-full transition-colors active:opacity-80 hidden sm:block">
                        <span class="material-symbols-outlined text-on-surface-variant">help</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Canvas -->
        <div class="p-8 space-y-8 max-w-container-max mx-auto w-full animate-page-in animate-stagger">
            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            @yield('content')
        </div>

        @stack('modals')
    </main>

    @stack('scripts')
    
    <script>
        // Toggle Sidebar for Mobile
        const btnMenu = document.getElementById('mobile-menu-btn');
        const btnClose = document.getElementById('close-sidebar-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('sidebar-mobile-visible');
            if (overlay.classList.contains('hidden')) {
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            } else {
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }

        if (btnMenu) btnMenu.addEventListener('click', toggleSidebar);
        if (btnClose) btnClose.addEventListener('click', toggleSidebar);
        if (overlay) overlay.addEventListener('click', toggleSidebar);

        // Notification System Logic
        const notifBtn = document.getElementById('notification-btn');
        const notifDropdown = document.getElementById('notification-dropdown');
        const notifBadge = document.getElementById('notification-badge');
        
        if (notifBtn && notifDropdown) {
            // Toggle dropdown
            notifBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (notifDropdown.classList.contains('hidden')) {
                    notifDropdown.classList.remove('hidden');
                    setTimeout(() => {
                        notifDropdown.classList.remove('scale-95', 'opacity-0');
                    }, 10);
                } else {
                    notifDropdown.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => {
                        notifDropdown.classList.add('hidden');
                    }, 200);
                }
            });

            // Close when clicking outside
            document.addEventListener('click', function(e) {
                if (!notifDropdown.contains(e.target) && e.target !== notifBtn && !notifBtn.contains(e.target)) {
                    if (!notifDropdown.classList.contains('hidden')) {
                        notifDropdown.classList.add('scale-95', 'opacity-0');
                        setTimeout(() => {
                            notifDropdown.classList.add('hidden');
                        }, 200);
                    }
                }
            });

            // Handle clicking a notification
            document.querySelectorAll('.notification-item').forEach(item => {
                item.addEventListener('click', function() {
                    if (this.getAttribute('data-read') === 'false') {
                        const notifId = this.getAttribute('data-id');
                        
                        // Optimistic UI update
                        this.classList.remove('bg-primary/5');
                        this.classList.add('bg-white');
                        this.setAttribute('data-read', 'true');
                        
                        const titleEl = this.querySelector('.text-primary');
                        if(titleEl) titleEl.classList.remove('text-primary');
                        
                        const dotEl = this.querySelector('.unread-dot');
                        if(dotEl) dotEl.remove();
                        
                        const iconContainer = this.querySelector('.bg-primary.text-white');
                        if(iconContainer) {
                            iconContainer.classList.remove('bg-primary', 'text-white');
                            iconContainer.classList.add('bg-surface-container-high', 'text-on-surface-variant');
                        }

                        // Update Badge Count
                        if (notifBadge) {
                            let count = parseInt(notifBadge.innerText.replace('+', ''));
                            if (!isNaN(count) && count > 0) {
                                count--;
                                if (count === 0) {
                                    notifBadge.remove();
                                    notifBtn.classList.remove('animate-bell-swing');
                                    const markAllBtn = document.getElementById('mark-all-read-btn');
                                    if(markAllBtn) markAllBtn.remove();
                                } else {
                                    notifBadge.innerText = count;
                                }
                            }
                        }

                        // AJAX Call
                        fetch(`/backoffice/notifications/${notifId}/read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        }).catch(err => console.error(err));
                    }
                });
            });

            // Handle mark all as read
            const markAllBtn = document.getElementById('mark-all-read-btn');
            if (markAllBtn) {
                markAllBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                    
                    // Optimistic update
                    document.querySelectorAll('.notification-item[data-read="false"]').forEach(item => {
                        item.classList.remove('bg-primary/5');
                        item.classList.add('bg-white');
                        item.setAttribute('data-read', 'true');
                        
                        const titleEl = item.querySelector('.text-primary');
                        if(titleEl) titleEl.classList.remove('text-primary');
                        
                        const dotEl = item.querySelector('.unread-dot');
                        if(dotEl) dotEl.remove();
                        
                        const iconContainer = item.querySelector('.bg-primary.text-white');
                        if(iconContainer) {
                            iconContainer.classList.remove('bg-primary', 'text-white');
                            iconContainer.classList.add('bg-surface-container-high', 'text-on-surface-variant');
                        }
                    });

                    if (notifBadge) {
                        notifBadge.remove();
                        notifBtn.classList.remove('animate-bell-swing');
                    }
                    this.remove();

                    // AJAX Call
                    fetch('/backoffice/notifications/read-all', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    }).catch(err => console.error(err));
                });
            }
        }
    </script>
</body>
</html>
