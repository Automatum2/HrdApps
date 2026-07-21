@extends('layouts.admin')

@section('title', 'Rekap Absensi - HRDApps')
@section('page_title', 'Rekap Absensi')

@section('content')
<!-- Header & Breadcrumbs -->
<div class="flex flex-col md:flex-row md:items-end justify-between mb-6">
    <div>
        <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-1">
            <a class="hover:text-primary transition-colors" href="{{ route('backoffice.dashboard') }}">Beranda</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary font-semibold">Rekap Absensi</span>
        </nav>
        <p class="text-body-sm text-on-surface-variant">Pantau dan kelola kehadiran harian, ketidakhadiran, cuti, serta status kerja karyawan.</p>
    </div>
</div>

<!-- Filters & Calendar Section -->
<div class="grid grid-cols-12 gap-6">
    <!-- Filters Card -->
    <div class="col-span-12 lg:col-span-9 bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
            <div class="space-y-1.5">
                <label class="text-xs uppercase font-bold text-on-surface-variant">Dari Tanggal</label>
                <input class="w-full border border-outline-variant rounded-lg text-sm px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white text-on-surface" type="date" value="2026-06-01" id="filter-dari-tanggal"/>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs uppercase font-bold text-on-surface-variant">Sampai Tanggal</label>
                <input class="w-full border border-outline-variant rounded-lg text-sm px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white text-on-surface" type="date" value="2026-06-19" id="filter-sampai-tanggal"/>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs uppercase font-bold text-on-surface-variant">Departemen</label>
                <select class="w-full border border-outline-variant rounded-lg text-sm px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white text-on-surface" id="filter-departemen">
                    <option value="">Semua Departemen</option>
                    <option value="IT">IT Support / Developer</option>
                    <option value="HRD">HRD / HR Specialist</option>
                    <option value="Finance">Finance</option>
                    <option value="Sales">Sales</option>
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs uppercase font-bold text-on-surface-variant">Status</label>
                <select class="w-full border border-outline-variant rounded-lg text-sm px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white text-on-surface" id="filter-status">
                    <option value="">Semua Status</option>
                    <option value="Hadir">Hadir</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Cuti">Cuti</option>
                    <option value="Alpha">Alpha</option>
                </select>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <button class="bg-surface-container-lowest border border-outline-variant text-on-surface font-semibold text-sm px-5 py-2 rounded-lg hover:bg-surface-container-low transition-colors flex items-center cursor-pointer active:scale-95 shadow-sm" id="btn-reset-filter">
                <span class="material-symbols-outlined mr-2 text-lg">filter_alt_off</span>
                Reset Filter
            </button>
            <button class="bg-primary text-white font-semibold text-sm px-5 py-2 rounded-lg hover:brightness-110 active:scale-95 transition-all flex items-center cursor-pointer shadow" onclick="alert('Mengekspor data absensi ke Excel...')">
                <span class="material-symbols-outlined mr-2 text-lg">download</span>
                Export Excel
            </button>
        </div>
    </div>
    
    <!-- Calendar Widget -->
    <div class="col-span-12 lg:col-span-3 bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <button class="text-on-surface-variant hover:bg-surface-container-low p-1 rounded transition-colors cursor-pointer"><span class="material-symbols-outlined text-lg">chevron_left</span></button>
            <h3 class="font-bold text-sm text-on-surface">Juni 2026</h3>
            <button class="text-on-surface-variant hover:bg-surface-container-low p-1 rounded transition-colors cursor-pointer"><span class="material-symbols-outlined text-lg">chevron_right</span></button>
        </div>
        <div class="grid grid-cols-7 text-center text-[10px] font-bold text-on-surface-variant mb-2 tracking-wider">
            <span>SEN</span><span>SEL</span><span>RAB</span><span>KAM</span><span>JUM</span><span>SAB</span><span>MIN</span>
        </div>
        <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium">
            <span class="py-1 text-on-surface-variant/30 font-mono">26</span>
            <span class="py-1 text-on-surface-variant/30 font-mono">27</span>
            <span class="py-1 text-on-surface-variant/30 font-mono">28</span>
            <span class="py-1 text-on-surface-variant/30 font-mono">29</span>
            <span class="py-1 text-on-surface-variant/30 font-mono">30</span>
            <span class="py-1 text-on-surface-variant/30 font-mono">31</span>
            <span class="py-1 font-mono">1</span>
            
            <span class="py-1 font-mono">2</span>
            <span class="py-1 font-mono">3</span>
            <span class="py-1 font-mono">4</span>
            <span class="py-1 font-mono">5</span>
            <span class="py-1 font-mono">6</span>
            <span class="py-1 text-error font-mono font-bold">7</span>
            <span class="py-1 font-mono">8</span>
            
            <span class="py-1 font-mono">9</span>
            <span class="py-1 font-mono">10</span>
            <span class="py-1 font-mono">11</span>
            <span class="py-1 font-mono">12</span>
            <span class="py-1 font-mono">13</span>
            <span class="py-1 text-error font-mono font-bold">14</span>
            <span class="py-1 font-mono">15</span>
            
            <span class="py-1 font-mono">16</span>
            <span class="py-1 font-mono">17</span>
            <span class="py-1 font-mono">18</span>
            <span class="py-1 bg-primary text-white rounded-full font-bold font-mono shadow-sm">19</span>
            <span class="py-1 font-mono">20</span>
            <span class="py-1 text-error font-mono font-bold">21</span>
            <span class="py-1 font-mono">22</span>
            
            <span class="py-1 font-mono">23</span>
            <span class="py-1 font-mono">24</span>
            <span class="py-1 font-mono">25</span>
            <span class="py-1 font-mono">26</span>
            <span class="py-1 font-mono">27</span>
            <span class="py-1 text-error font-mono font-bold">28</span>
            <span class="py-1 font-mono">29</span>
            
            <span class="py-1 font-mono">30</span>
            <span class="py-1 text-on-surface-variant/30 font-mono">1</span>
            <span class="py-1 text-on-surface-variant/30 font-mono">2</span>
            <span class="py-1 text-on-surface-variant/30 font-mono">3</span>
            <span class="py-1 text-on-surface-variant/30 font-mono">4</span>
            <span class="py-1 text-on-surface-variant/30 font-mono">5</span>
            <span class="py-1 text-on-surface-variant/30 font-mono">6</span>
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
        <h2 class="font-bold text-sm text-on-surface">Data Kehadiran Harian</h2>
        <div class="flex gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64 group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
                <input class="w-full bg-white border border-outline-variant rounded-lg pl-10 pr-4 py-1.5 text-xs outline-none focus:ring-2 focus:ring-primary/20 transition-all text-on-surface" placeholder="Cari nama atau NIK karyawan..." type="text" id="search-karyawan">
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
                    <th class="px-6 py-4">Nama Karyawan</th>
                    <th class="px-6 py-4">NIK</th>
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
                <tr class="hover:bg-primary/5 transition-colors group" data-nik="{{ $att->employee->nik ?? '' }}" data-dept="{{ $att->employee->department->nama_departemen ?? 'Umum' }}" data-status="{{ ucfirst($att->status_kehadiran) }}" data-kerja="{{ $att->status_kerja }}">
                    <td class="px-6 py-4">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr($att->employee->nama_lengkap ?? 'U', 0, 2)) }}
                            </div>
                            <span class="font-bold text-on-surface">{{ $att->employee->nama_lengkap ?? 'Unknown' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-mono text-on-surface-variant">{{ $att->employee->nik ?? '-' }}</td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ \Carbon\Carbon::parse($att->tanggal)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 font-bold text-on-surface">{{ $att->jam_masuk ? substr($att->jam_masuk, 0, 5) : '--:--' }}</td>
                    <td class="px-6 py-4 font-bold text-on-surface">{{ $att->jam_keluar ? substr($att->jam_keluar, 0, 5) : '--:--' }}</td>
                    <td class="px-6 py-4">
                        <span class="bg-primary-fixed text-on-primary-fixed-variant px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider">{{ $att->status_kerja }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-tertiary-container text-on-tertiary-container px-3 py-1 rounded-full text-[10px] font-bold">{{ ucfirst($att->status_kehadiran) }}</span>
                    </td>
                    <td class="px-6 py-4 text-center font-mono text-on-surface">{{ $att->total_jam_kerja ? $att->total_jam_kerja . ' Jam' : '--' }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="p-1 hover:bg-surface-container-high rounded text-primary transition-colors cursor-pointer" title="Lihat Foto" onclick="showFotoModal('{{ $att->foto_masuk ? asset('storage/' . $att->foto_masuk) : '' }}', '{{ $att->foto_keluar ? asset('storage/' . $att->foto_keluar) : '' }}', '{{ $att->lokasi_masuk }}', '{{ $att->lokasi_keluar }}')">
                                <span class="material-symbols-outlined text-lg">image</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-6 py-8 text-center text-slate-500">Belum ada riwayat absensi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-6 bg-surface-container-low border-t border-outline-variant flex flex-col sm:flex-row justify-between items-center gap-4">
        <p class="text-xs text-on-surface-variant">Menampilkan <span class="font-bold text-on-surface" id="showing-count-footer">4</span> dari <span class="font-bold text-on-surface" id="total-count-footer">4</span> entri</p>
        <div class="flex gap-1">
            <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-surface transition-colors disabled:opacity-30 cursor-pointer" disabled>
                <span class="material-symbols-outlined text-lg">chevron_left</span>
            </button>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-primary text-white font-bold shadow-sm text-xs">1</button>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-surface transition-colors text-xs cursor-pointer">2</button>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-surface transition-colors text-xs cursor-pointer">3</button>
            <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-surface transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-lg">chevron_right</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal Foto Absensi -->
<div id="modal-foto" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <div class="bg-surface border border-outline-variant rounded-xl shadow-2xl p-6 w-full max-w-2xl transform scale-95 transition-transform duration-300">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-title-md text-title-md text-on-surface font-bold">Bukti Foto & Lokasi Absensi</h3>
            <button onclick="closeModal()" class="text-on-surface-variant hover:text-error transition-colors cursor-pointer p-1">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
                <p class="text-sm font-bold text-primary text-center">Foto Masuk</p>
                <div class="w-full h-48 bg-slate-100 rounded-lg overflow-hidden flex items-center justify-center">
                    <img id="img-masuk" src="" class="w-full h-full object-cover hidden" alt="Foto Masuk" />
                    <p id="img-masuk-none" class="text-xs text-slate-400">Tidak ada foto</p>
                </div>
                <p id="lokasi-masuk" class="text-xs text-on-surface-variant text-center font-mono break-all"></p>
                <a id="maps-masuk" href="#" target="_blank" class="block text-center text-xs text-primary hover:underline hidden mt-1">Lihat di Google Maps</a>
            </div>
            
            <div class="space-y-2">
                <p class="text-sm font-bold text-primary text-center">Foto Keluar</p>
                <div class="w-full h-48 bg-slate-100 rounded-lg overflow-hidden flex items-center justify-center">
                    <img id="img-keluar" src="" class="w-full h-full object-cover hidden" alt="Foto Keluar" />
                    <p id="img-keluar-none" class="text-xs text-slate-400">Tidak ada foto</p>
                </div>
                <p id="lokasi-keluar" class="text-xs text-on-surface-variant text-center font-mono break-all"></p>
                <a id="maps-keluar" href="#" target="_blank" class="block text-center text-xs text-primary hover:underline hidden mt-1">Lihat di Google Maps</a>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button onclick="closeModal()" class="bg-surface-container border border-outline px-4 py-2 rounded-lg font-bold hover:bg-slate-100 transition-colors cursor-pointer text-sm">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inisialisasi Elemen Filter
    const filterDari = document.getElementById('filter-dari-tanggal');
    const filterSampai = document.getElementById('filter-sampai-tanggal');
    const filterDept = document.getElementById('filter-departemen');
    const filterStatus = document.getElementById('filter-status');
    const searchKaryawan = document.getElementById('search-karyawan');
    
    const tableBody = document.getElementById('table-absensi-body');
    const btnReset = document.getElementById('btn-reset-filter');
    const btnWfo = document.getElementById('btn-filter-wfo');
    const btnAll = document.getElementById('btn-filter-all');
    
    // Label Teks
    const showingRange = document.getElementById('showing-range');
    const totalEntriesTop = document.getElementById('total-entries-top');
    const showingCountFooter = document.getElementById('showing-count-footer');
    const totalCountFooter = document.getElementById('total-count-footer');
    
    // Default values
    const defaultDari = "2026-06-01";
    const defaultSampai = "2026-06-19";

    // ==========================================
    // Fungsi Filter Utama
    // ==========================================
    const filterAbsensi = (filterKerja = '') => {
        const query = searchKaryawan.value.toLowerCase().trim();
        const deptVal = filterDept.value;
        const statusVal = filterStatus.value;
        const rows = tableBody.querySelectorAll('tr');
        
        let visibleCount = 0;
        
        rows.forEach(row => {
            const nama = row.querySelector('td:nth-child(2) span').innerText.toLowerCase();
            const nik = row.getAttribute('data-nik').toLowerCase();
            const dept = row.getAttribute('data-dept');
            const status = row.getAttribute('data-status');
            const kerja = row.getAttribute('data-kerja');
            
            const matchQuery = nama.includes(query) || nik.includes(query);
            const matchDept = !deptVal || dept === deptVal;
            const matchStatus = !statusVal || status === statusVal;
            const matchKerja = !filterKerja || kerja === filterKerja;
            
            if (matchQuery && matchDept && matchStatus && matchKerja) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });
        
        // Sesuaikan penomoran visual baris yang tampil
        let idx = 1;
        rows.forEach(row => {
            if (!row.classList.contains('hidden')) {
                row.querySelector('td:first-child').innerText = idx++;
            }
        });
        
        // Update label jumlah entri
        showingRange.innerText = visibleCount === 0 ? '0' : `1-${visibleCount}`;
        showingCountFooter.innerText = visibleCount;
    };

    // Event Listeners untuk Filter
    searchKaryawan.addEventListener('input', () => filterAbsensi());
    filterDept.addEventListener('change', () => filterAbsensi());
    filterStatus.addEventListener('change', () => filterAbsensi());
    filterDari.addEventListener('change', () => filterAbsensi());
    filterSampai.addEventListener('change', () => filterAbsensi());
    
    // Filter WFO Only dan Semua
    btnWfo.addEventListener('click', () => {
        btnWfo.className = "bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-lg hover:brightness-110 transition-colors cursor-pointer active:scale-95";
        btnAll.className = "bg-white border border-outline-variant text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-surface-container-low transition-colors cursor-pointer active:scale-95";
        filterAbsensi('WFO');
    });
    
    btnAll.addEventListener('click', () => {
        btnAll.className = "bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-lg hover:brightness-110 transition-colors cursor-pointer active:scale-95";
        btnWfo.className = "bg-white border border-outline-variant text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-surface-container-low transition-colors cursor-pointer active:scale-95";
        filterAbsensi();
    });
    
    // Reset Filter
    btnReset.addEventListener('click', () => {
        filterDari.value = defaultDari;
        filterSampai.value = defaultSampai;
        filterDept.value = '';
        filterStatus.value = '';
        searchKaryawan.value = '';
        
        btnAll.click(); // Reset ke Semua
    });
    
    
    // Inisialisasi visual counter awal
    const initRowsCount = tableBody.querySelectorAll('tr:not(.hidden)').length;
    showingCountFooter.innerText = initRowsCount;
    totalCountFooter.innerText = tableBody.querySelectorAll('tr').length;
    
    // ==========================================
    // Modal Foto Absensi
    // ==========================================
    window.showFotoModal = (fotoMasuk, fotoKeluar, lokasiMasuk, lokasiKeluar) => {
        const modal = document.getElementById('modal-foto');
        const imgMasuk = document.getElementById('img-masuk');
        const imgMasukNone = document.getElementById('img-masuk-none');
        const imgKeluar = document.getElementById('img-keluar');
        const imgKeluarNone = document.getElementById('img-keluar-none');
        
        const textLokasiMasuk = document.getElementById('lokasi-masuk');
        const textLokasiKeluar = document.getElementById('lokasi-keluar');
        const mapsMasuk = document.getElementById('maps-masuk');
        const mapsKeluar = document.getElementById('maps-keluar');
        
        if (fotoMasuk) {
            imgMasuk.src = fotoMasuk;
            imgMasuk.classList.remove('hidden');
            imgMasukNone.classList.add('hidden');
        } else {
            imgMasuk.src = '';
            imgMasuk.classList.add('hidden');
            imgMasukNone.classList.remove('hidden');
        }
        
        if (fotoKeluar) {
            imgKeluar.src = fotoKeluar;
            imgKeluar.classList.remove('hidden');
            imgKeluarNone.classList.add('hidden');
        } else {
            imgKeluar.src = '';
            imgKeluar.classList.add('hidden');
            imgKeluarNone.classList.remove('hidden');
        }
        
        textLokasiMasuk.innerText = lokasiMasuk || 'Tidak ada lokasi';
        if (lokasiMasuk) {
            mapsMasuk.href = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(lokasiMasuk)}`;
            mapsMasuk.classList.remove('hidden');
        } else {
            mapsMasuk.classList.add('hidden');
        }
        
        textLokasiKeluar.innerText = lokasiKeluar || 'Tidak ada lokasi';
        if (lokasiKeluar) {
            mapsKeluar.href = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(lokasiKeluar)}`;
            mapsKeluar.classList.remove('hidden');
        } else {
            mapsKeluar.classList.add('hidden');
        }
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.children[0].classList.remove('scale-95');
        }, 10);
    };
    
    window.closeModal = () => {
        const modal = document.getElementById('modal-foto');
        modal.classList.add('opacity-0');
        modal.children[0].classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    };
</script>
@endpush
