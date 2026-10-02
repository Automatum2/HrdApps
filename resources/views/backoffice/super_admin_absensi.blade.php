@extends('layouts.admin')

@section('title', 'Monitoring Absensi Global - HRDApps')
@section('page_title', 'Monitoring Absensi Global')

@section('content')
<!-- Header & Breadcrumbs -->
<div class="flex flex-col md:flex-row md:items-end justify-between mb-6">
    <div>
        <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-1">
            <a class="hover:text-primary transition-colors" href="{{ route('backoffice.dashboard') }}">Beranda</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary font-semibold">Monitoring Absensi Global</span>
        </nav>
        <h1 class="text-2xl font-bold text-on-surface">Monitoring Absensi Global</h1>
        <p class="text-body-sm text-on-surface-variant">Pantau absensi seluruh karyawan, manager departemen, dan HR manager di perusahaan secara menyeluruh.</p>
    </div>
</div>

<!-- Filters & Calendar Section -->
<div class="grid grid-cols-12 gap-6">
    <!-- Filters Card -->
    <div class="col-span-12 lg:col-span-9 bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">
        <form method="GET" action="{{ route('backoffice.super_admin.absensi') }}" id="form-filter-absensi">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                <div class="space-y-1.5">
                    <label class="text-xs uppercase font-bold text-on-surface-variant">Dari Tanggal</label>
                    <input class="w-full border border-outline-variant rounded-lg text-sm px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white text-on-surface" type="date" name="dari_tanggal" value="{{ request('dari_tanggal', $dari) }}" id="filter-dari-tanggal"/>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs uppercase font-bold text-on-surface-variant">Sampai Tanggal</label>
                    <input class="w-full border border-outline-variant rounded-lg text-sm px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white text-on-surface" type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal', $sampai) }}" id="filter-sampai-tanggal"/>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs uppercase font-bold text-on-surface-variant">Departemen</label>
                    <select class="w-full border border-outline-variant rounded-lg text-sm px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white text-on-surface" name="departemen_id" id="filter-departemen">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ (string)request('departemen_id', $departmentId) === (string)$dept->id ? 'selected' : '' }}>{{ $dept->nama_department }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs uppercase font-bold text-on-surface-variant">Status</label>
                    <select class="w-full border border-outline-variant rounded-lg text-sm px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white text-on-surface" name="status" id="filter-status">
                        <option value="">Semua Status</option>
                        <option value="hadir" {{ request('status', $status) == 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="izin" {{ request('status', $status) == 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ request('status', $status) == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="cuti" {{ request('status', $status) == 'cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="alpha" {{ request('status', $status) == 'alpha' ? 'selected' : '' }}>Alpha</option>
                    </select>
                </div>
            </div>
            <div class="mt-5 pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 flex-wrap">
                <div class="flex flex-wrap items-center gap-2.5">
                    <button type="submit" class="bg-blue-50 border border-blue-200 text-blue-700 font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl hover:bg-blue-100 transition-colors flex items-center justify-center gap-2 cursor-pointer shadow-sm active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">search</span>
                        <span>Terapkan Filter</span>
                    </button>
                    <a href="{{ route('backoffice.super_admin.absensi') }}" class="bg-surface-container-lowest border border-outline-variant text-on-surface font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl hover:bg-surface-container-low transition-colors flex items-center justify-center gap-2 cursor-pointer active:scale-95 shadow-sm" id="btn-reset-filter">
                        <span class="material-symbols-outlined text-[18px]">filter_alt_off</span>
                        <span>Reset</span>
                    </a>
                </div>
                <div class="flex items-center">
                    @php
                        $exportUrl = route('backoffice.absensi.export', request()->query());
                    @endphp
                    <a href="{{ $exportUrl }}" class="w-full sm:w-auto bg-primary text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer shadow">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span>Export Excel</span>
                    </a>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Calendar Widget -->
    <div class="col-span-12 lg:col-span-3 bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-sm">
        @php
            $currentDate = \Carbon\Carbon::now();
            $daysInMonth = $currentDate->daysInMonth;
            $firstDayOfMonth = $currentDate->copy()->startOfMonth()->dayOfWeekIso; 
            $monthName = $currentDate->translatedFormat('F Y');
        @endphp
        <div class="flex items-center justify-between mb-4">
            <button class="text-on-surface-variant hover:bg-surface-container-low p-1 rounded transition-colors cursor-pointer"><span class="material-symbols-outlined text-lg">chevron_left</span></button>
            <h3 class="font-bold text-sm text-on-surface">{{ $monthName }}</h3>
            <button class="text-on-surface-variant hover:bg-surface-container-low p-1 rounded transition-colors cursor-pointer"><span class="material-symbols-outlined text-lg">chevron_right</span></button>
        </div>
        <div class="grid grid-cols-7 text-center text-[10px] font-bold text-on-surface-variant mb-2 tracking-wider">
            <span>SEN</span><span>SEL</span><span>RAB</span><span>KAM</span><span>JUM</span><span>SAB</span><span>MIN</span>
        </div>
        <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium">
            @php
                $previousMonthDays = $currentDate->copy()->subMonth()->daysInMonth;
                $startDay = $firstDayOfMonth - 1; 
            @endphp
            
            @for ($i = $startDay - 1; $i >= 0; $i--)
                <span class="py-1 text-on-surface-variant/30 font-mono">{{ $previousMonthDays - $i }}</span>
            @endfor
            
            @for ($i = 1; $i <= $daysInMonth; $i++)
                @if ($i == $currentDate->day)
                    <span class="py-1 bg-primary text-white rounded-full font-bold font-mono shadow-sm">{{ $i }}</span>
                @else
                    @php
                        $dayOfWeek = $currentDate->copy()->startOfMonth()->addDays($i - 1)->dayOfWeekIso;
                    @endphp
                    @if ($dayOfWeek == 7)
                        <span class="py-1 text-error font-mono font-bold">{{ $i }}</span>
                    @else
                        <span class="py-1 font-mono">{{ $i }}</span>
                    @endif
                @endif
            @endfor
            
            @php
                $totalCells = $startDay + $daysInMonth;
                $remainingCells = 42 - $totalCells;
                if ($remainingCells >= 7) $remainingCells -= 7;
            @endphp
            
            @for ($i = 1; $i <= $remainingCells; $i++)
                <span class="py-1 text-on-surface-variant/30 font-mono">{{ $i }}</span>
            @endfor
        </div>
    </div>
</div>

<!-- Stats Bento Grid -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-6 mt-6">
    <!-- Card 1: Hadir -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 flex items-center justify-between shadow-sm overflow-hidden relative">
        <div class="z-10">
            <p class="text-xs uppercase font-bold text-on-surface-variant tracking-wider">Hadir</p>
            <h4 class="text-headline-md font-bold text-tertiary-container mt-1" id="stat-hadir">{{ $stats['hadir'] }}</h4>
        </div>
        <div class="bg-tertiary-container/10 p-3 rounded-full z-10 text-tertiary">
            <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
        </div>
        <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-tertiary-container/5 rounded-full"></div>
    </div>
    <!-- Card 2: Izin -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 flex items-center justify-between shadow-sm overflow-hidden relative">
        <div class="z-10">
            <p class="text-xs uppercase font-bold text-on-surface-variant tracking-wider">Izin</p>
            <h4 class="text-headline-md font-bold text-[#F59E0B] mt-1" id="stat-izin">{{ $stats['izin'] }}</h4>
        </div>
        <div class="bg-[#F59E0B]/10 p-3 rounded-full z-10 text-[#F59E0B]">
            <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">description</span>
        </div>
        <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-[#F59E0B]/5 rounded-full"></div>
    </div>
    <!-- Card 3: Sakit -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 flex items-center justify-between shadow-sm overflow-hidden relative">
        <div class="z-10">
            <p class="text-xs uppercase font-bold text-on-surface-variant tracking-wider">Sakit</p>
            <h4 class="text-headline-md font-bold text-[#F97316] mt-1" id="stat-sakit">{{ $stats['sakit'] }}</h4>
        </div>
        <div class="bg-[#F97316]/10 p-3 rounded-full z-10 text-[#F97316]">
            <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">medical_services</span>
        </div>
        <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-[#F97316]/5 rounded-full"></div>
    </div>
    <!-- Card 4: Alpha -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 flex items-center justify-between shadow-sm overflow-hidden relative">
        <div class="z-10">
            <p class="text-xs uppercase font-bold text-on-surface-variant tracking-wider">Alpha</p>
            <h4 class="text-headline-md font-bold text-error mt-1" id="stat-alpha">{{ $stats['alpha'] }}</h4>
        </div>
        <div class="bg-error/10 p-3 rounded-full z-10 text-error">
            <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">cancel</span>
        </div>
        <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-error/5 rounded-full"></div>
    </div>
    <!-- Card 5: Cuti -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 flex items-center justify-between shadow-sm overflow-hidden relative">
        <div class="z-10">
            <p class="text-xs uppercase font-bold text-on-surface-variant tracking-wider">Cuti</p>
            <h4 class="text-headline-md font-bold text-primary mt-1" id="stat-cuti">{{ $stats['cuti'] }}</h4>
        </div>
        <div class="bg-primary/10 p-3 rounded-full z-10 text-primary">
            <span class="material-symbols-outlined text-2xl" style="font-variation-settings: 'FILL' 1;">beach_access</span>
        </div>
        <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-primary/5 rounded-full"></div>
    </div>
</div>

<!-- Attendance Table -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col mt-6">
    <div class="p-6 border-b border-outline-variant flex flex-col sm:flex-row justify-between items-center bg-surface-container-low gap-4">
        <h2 class="font-bold text-sm text-on-surface">Data Presensi Seluruh Karyawan & Pimpinan</h2>
        <div class="flex gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64 group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
                <input class="w-full bg-white border border-outline-variant rounded-lg pl-10 pr-4 py-1.5 text-xs outline-none focus:ring-2 focus:ring-primary/20 transition-all text-on-surface" placeholder="Cari nama atau NIK..." type="text" id="search-karyawan">
            </div>
            <button class="bg-white border border-outline-variant text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-surface-container-low transition-colors cursor-pointer active:scale-95" id="btn-filter-wfo">WFO Only</button>
            <button class="bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-lg hover:brightness-110 transition-colors cursor-pointer active:scale-95" id="btn-filter-all">Semua</button>
        </div>
    </div>
    
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left whitespace-nowrap border-collapse">
            <thead>
                <tr class="bg-surface-container-low text-on-surface-variant font-semibold text-xs border-b border-outline-variant/10">
                    <th class="px-6 py-4 uppercase">No</th>
                    <th class="px-6 py-4">Karyawan</th>
                    <th class="px-6 py-4">Role & Departemen</th>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Masuk</th>
                    <th class="px-6 py-4">Keluar</th>
                    <th class="px-6 py-4">Status Kerja</th>
                    <th class="px-6 py-4 text-center">Kehadiran</th>
                    <th class="px-6 py-4 text-center">Durasi</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10 font-body-sm text-body-sm" id="table-absensi-body">
                @forelse($attendances as $index => $att)
                <tr class="hover:bg-primary/5 transition-colors group" data-nik="{{ $att->employee->nik ?? '' }}" data-nama="{{ strtolower($att->employee->nama_lengkap ?? '') }}" data-dept="{{ $att->employee->department->nama_department ?? 'Umum' }}" data-status="{{ ucfirst($att->status_kehadiran) }}" data-kerja="{{ $att->status_kerja }}">
                    <td class="px-6 py-4">{{ $attendances->firstItem() ? $attendances->firstItem() + $index : $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr($att->employee->nama_lengkap ?? 'U', 0, 2)) }}
                            </div>
                            <div>
                                <span class="font-bold text-on-surface block">{{ $att->employee->nama_lengkap ?? 'Unknown' }}</span>
                                <span class="text-xs text-on-surface-variant font-mono">{{ $att->employee->nik ?? '-' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider mb-1
                            {{ ($att->employee->user->role ?? '') === 'super_admin' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ ($att->employee->user->role ?? '') === 'hr_manager' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ ($att->employee->user->role ?? '') === 'manager_departemen' ? 'bg-indigo-100 text-indigo-700' : '' }}
                            {{ ($att->employee->user->role ?? '') === 'karyawan' ? 'bg-slate-100 text-slate-700' : '' }}">
                            {{ str_replace('_', ' ', $att->employee->user->role ?? 'Karyawan') }}
                        </span>
                        <div class="text-xs text-slate-500 font-medium">{{ $att->employee->department->nama_department ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ \Carbon\Carbon::parse($att->tanggal)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 font-bold text-on-surface font-mono">{{ $att->jam_masuk ? substr($att->jam_masuk, 0, 5) : '--:--' }}</td>
                    <td class="px-6 py-4 font-bold text-on-surface font-mono">{{ $att->jam_keluar ? substr($att->jam_keluar, 0, 5) : '--:--' }}</td>
                    <td class="px-6 py-4">
                        <span class="bg-primary-fixed text-on-primary-fixed-variant px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider">{{ $att->status_kerja ?? 'WFO' }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold inline-block
                            {{ in_array(strtolower($att->status_kehadiran), ['hadir', 'wfo', 'wfh']) ? 'bg-tertiary-container text-on-tertiary-container' : '' }}
                            {{ in_array(strtolower($att->status_kehadiran), ['izin', 'cuti']) ? 'bg-primary/15 text-primary' : '' }}
                            {{ strtolower($att->status_kehadiran) === 'sakit' ? 'bg-[#F97316]/15 text-[#F97316]' : '' }}
                            {{ strtolower($att->status_kehadiran) === 'alpha' ? 'bg-error/15 text-error' : '' }}">
                            {{ ucfirst($att->status_kehadiran) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center font-mono text-on-surface">{{ $att->total_jam_kerja ? $att->total_jam_kerja . ' Jam' : '--' }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2 transition-opacity">
                            <button class="p-1 hover:bg-surface-container-high rounded text-primary transition-colors cursor-pointer" title="Lihat Bukti Foto & Peta" onclick="showFotoModal('{{ $att->foto_masuk ? asset('storage/' . $att->foto_masuk) : '' }}', '{{ $att->foto_keluar ? asset('storage/' . $att->foto_keluar) : '' }}', '{{ addslashes($att->lokasi_masuk ?? '') }}', '{{ addslashes($att->lokasi_keluar ?? '') }}', '{{ addslashes($att->employee->nama_lengkap ?? 'Karyawan') }}', '{{ $att->tanggal }}')">
                                <span class="material-symbols-outlined text-lg">image</span>
                            </button>
                            @if($att->dokumen_pendukung)
                            <a href="{{ asset('storage/' . $att->dokumen_pendukung) }}" target="_blank" class="p-1 hover:bg-surface-container-high rounded text-tertiary transition-colors cursor-pointer" title="Lihat Dokumen / Surat Dokter">
                                <span class="material-symbols-outlined text-lg">description</span>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-6 py-8 text-center text-slate-500">
                        <span class="material-symbols-outlined text-4xl block mb-2 text-slate-300">event_busy</span>
                        Belum ada riwayat absensi untuk filter ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-6 bg-surface-container-low border-t border-outline-variant flex flex-col sm:flex-row justify-between items-center gap-4">
        <p class="text-xs text-on-surface-variant">Menampilkan <span class="font-bold text-on-surface">{{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }}</span> dari <span class="font-bold text-on-surface">{{ $attendances->total() }}</span> entri</p>
        <div>
            {{ $attendances->links() }}
        </div>
    </div>
</div>

@push('modals')
<!-- Modal Bukti Foto & Lokasi Absensi -->
<div id="modal-foto" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <div class="bg-surface border border-outline-variant rounded-xl shadow-2xl p-6 w-[95%] md:w-[650px] transform scale-95 transition-transform duration-300 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="font-title-md text-title-md text-on-surface font-bold">Bukti Presensi & Titik Lokasi</h3>
                <p class="text-xs text-slate-500" id="modal-employee-info"></p>
            </div>
            <button onclick="closeModal()" class="text-on-surface-variant hover:text-error transition-colors cursor-pointer p-1">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
                <p class="text-sm font-bold text-primary text-center">Foto Masuk</p>
                <div class="w-full h-48 bg-slate-100 rounded-lg overflow-hidden flex items-center justify-center border border-slate-200">
                    <img id="img-masuk" src="" class="w-full h-full object-cover hidden" alt="Foto Masuk" />
                    <p id="img-masuk-none" class="text-xs text-slate-400">Tidak ada foto</p>
                </div>
                <p id="lokasi-masuk" class="text-xs text-on-surface-variant text-center font-mono break-all"></p>
                <a id="maps-masuk" href="#" target="_blank" class="block text-center text-xs text-primary hover:underline hidden mt-1">Buka di Google Maps</a>
            </div>
            
            <div class="space-y-2">
                <p class="text-sm font-bold text-primary text-center">Foto Keluar</p>
                <div class="w-full h-48 bg-slate-100 rounded-lg overflow-hidden flex items-center justify-center border border-slate-200">
                    <img id="img-keluar" src="" class="w-full h-full object-cover hidden" alt="Foto Keluar" />
                    <p id="img-keluar-none" class="text-xs text-slate-400">Tidak ada foto</p>
                </div>
                <p id="lokasi-keluar" class="text-xs text-on-surface-variant text-center font-mono break-all"></p>
                <a id="maps-keluar" href="#" target="_blank" class="block text-center text-xs text-primary hover:underline hidden mt-1">Buka di Google Maps</a>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button onclick="closeModal()" class="bg-surface-container border border-outline px-5 py-2 rounded-lg font-bold hover:bg-slate-100 transition-colors cursor-pointer text-sm">Tutup</button>
        </div>
    </div>
</div>
@endpush
@endsection

@push('scripts')
<script>
    // Search instant pada tabel
    const searchKaryawan = document.getElementById('search-karyawan');
    const tableBody = document.getElementById('table-absensi-body');
    const btnWfo = document.getElementById('btn-filter-wfo');
    const btnAll = document.getElementById('btn-filter-all');

    if (searchKaryawan && tableBody) {
        searchKaryawan.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            const rows = tableBody.querySelectorAll('tr');
            rows.forEach(row => {
                const nama = (row.getAttribute('data-nama') || '').toLowerCase();
                const nik = (row.getAttribute('data-nik') || '').toLowerCase();
                const dept = (row.getAttribute('data-dept') || '').toLowerCase();
                if (nama.includes(query) || nik.includes(query) || dept.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    if (btnWfo && tableBody) {
        btnWfo.addEventListener('click', () => {
            btnWfo.classList.remove('bg-white', 'text-on-surface');
            btnWfo.classList.add('bg-primary', 'text-white');
            btnAll.classList.remove('bg-primary', 'text-white');
            btnAll.classList.add('bg-white', 'text-on-surface');

            const rows = tableBody.querySelectorAll('tr');
            rows.forEach(row => {
                const kerja = (row.getAttribute('data-kerja') || '').toUpperCase();
                if (kerja === 'WFO') {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    if (btnAll && tableBody) {
        btnAll.addEventListener('click', () => {
            btnAll.classList.remove('bg-white', 'text-on-surface');
            btnAll.classList.add('bg-primary', 'text-white');
            btnWfo.classList.remove('bg-primary', 'text-white');
            btnWfo.classList.add('bg-white', 'text-on-surface');

            const rows = tableBody.querySelectorAll('tr');
            rows.forEach(row => {
                row.style.display = '';
            });
        });
    }

    function showFotoModal(fotoMasuk, fotoKeluar, locMasuk, locKeluar, namaKaryawan = '', tanggal = '') {
        const modal = document.getElementById('modal-foto');
        const imgIn = document.getElementById('img-masuk');
        const imgInNone = document.getElementById('img-masuk-none');
        const imgOut = document.getElementById('img-keluar');
        const imgOutNone = document.getElementById('img-keluar-none');
        const textLocIn = document.getElementById('lokasi-masuk');
        const textLocOut = document.getElementById('lokasi-keluar');
        const mapsIn = document.getElementById('maps-masuk');
        const mapsOut = document.getElementById('maps-keluar');
        const empInfo = document.getElementById('modal-employee-info');

        if (empInfo) {
            empInfo.innerText = namaKaryawan ? `${namaKaryawan} • ${tanggal}` : '';
        }

        if (fotoMasuk && fotoMasuk.trim() !== '') {
            imgIn.src = fotoMasuk;
            imgIn.classList.remove('hidden');
            imgInNone.classList.add('hidden');
        } else {
            imgIn.src = '';
            imgIn.classList.add('hidden');
            imgInNone.classList.remove('hidden');
        }

        if (fotoKeluar && fotoKeluar.trim() !== '') {
            imgOut.src = fotoKeluar;
            imgOut.classList.remove('hidden');
            imgOutNone.classList.add('hidden');
        } else {
            imgOut.src = '';
            imgOut.classList.add('hidden');
            imgOutNone.classList.remove('hidden');
        }

        textLocIn.innerText = locMasuk || 'Lokasi tidak tercatat';
        textLocOut.innerText = locKeluar || 'Lokasi tidak tercatat';

        if (locMasuk && locMasuk.includes(',')) {
            mapsIn.href = `https://www.google.com/maps?q=${locMasuk.trim()}`;
            mapsIn.classList.remove('hidden');
        } else {
            mapsIn.classList.add('hidden');
        }

        if (locKeluar && locKeluar.includes(',')) {
            mapsOut.href = `https://www.google.com/maps?q=${locKeluar.trim()}`;
            mapsOut.classList.remove('hidden');
        } else {
            mapsOut.classList.add('hidden');
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
            modal.querySelector('div').classList.add('scale-100');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('modal-foto');
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.remove('scale-100');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>
@endpush
