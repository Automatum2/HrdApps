@extends('layouts.admin')

@section('title', 'Dashboard Manager - HRDApps')
@section('page_title', 'Dashboard Manager')

@section('content')
<!-- Summary Grid -->
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-stagger">
    <!-- Card 1: Total Karyawan Departemen -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow hover-card-float hover:border-primary/50 transition-colors flex justify-between items-start">
        <div>
            <p class="text-on-surface-variant font-medium text-sm mb-1">Total Karyawan Departemen</p>
            <h3 class="text-display-lg font-display-lg text-on-background font-bold" id="stat-total-karyawan">{{ $total_karyawan_dept }}</h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-primary-container/10 flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">group</span>
        </div>
    </div>
    <!-- Card 2: Hadir Hari Ini -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow hover-card-float hover:border-primary/50 transition-colors flex justify-between items-start">
        <div>
            <p class="text-on-surface-variant font-medium text-sm mb-1">Hadir Hari Ini</p>
            <h3 class="text-display-lg font-display-lg text-tertiary font-bold" id="stat-hadir-karyawan">{{ $hadir_hari_ini_dept }}</h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-tertiary-container/10 flex items-center justify-center text-tertiary">
            <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
        </div>
    </div>
    <!-- Card 3: Belum Absen -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow hover-card-float hover:border-primary/50 transition-colors flex justify-between items-start">
        <div>
            <p class="text-on-surface-variant font-medium text-sm mb-1">Belum Absen</p>
            <h3 class="text-display-lg font-display-lg text-error font-bold" id="stat-belum-absen">{{ $belum_absen_dept }}</h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-error-container/20 flex items-center justify-center text-error">
            <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">pending_actions</span>
        </div>
    </div>
    <!-- Card 4: Total Karyawan Perusahaan -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow hover-card-float hover:border-primary/50 transition-colors flex justify-between items-start">
        <div>
            <p class="text-on-surface-variant font-medium text-sm mb-1">Total Karyawan Perusahaan</p>
            <h3 class="text-display-lg font-display-lg text-primary font-bold" id="stat-estimasi-gaji">{{ $total_karyawan_perusahaan }}</h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-primary-container/10 flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">corporate_fare</span>
        </div>
    </div>
</section>

<!-- Widget Absensi Manager -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow mb-6 mt-6 flex flex-col md:flex-row items-center justify-between gap-6 animate-stagger relative overflow-hidden">
    <!-- Ornamen Background -->
    <div class="absolute right-0 top-0 w-64 h-full bg-gradient-to-l from-primary/5 to-transparent pointer-events-none"></div>
    
    <div class="flex items-center gap-5 z-10">
        <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary relative shadow-sm border border-primary/20">
            <span class="material-symbols-outlined text-4xl" style="font-variation-settings: 'FILL' 1;">fingerprint</span>
            <span class="absolute top-0 right-0 w-4 h-4 bg-error rounded-full border-2 border-white animate-pulse" id="status-dot"></span>
        </div>
        <div>
            <h4 class="font-bold text-lg text-on-background">Absensi Kehadiran</h4>
            <p class="text-xs text-on-surface-variant font-medium mt-1">Waktu Server: <span class="font-bold text-primary font-mono bg-primary/10 px-1.5 py-0.5 rounded" id="realtime-clock">--:--:-- WIB</span></p>
            <p class="text-[11px] text-error font-bold mt-1" id="status-text">Anda belum melakukan Clock In hari ini.</p>
        </div>
    </div>
    
    <div class="flex items-center gap-3 w-full md:w-auto z-10">
        <a href="{{ route('attendance.index') }}" class="flex-1 md:flex-none px-6 py-3 rounded-xl bg-primary text-white font-bold text-sm shadow-md hover:brightness-110 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 cursor-pointer btn-ripple group">
            <span class="material-symbols-outlined text-[18px] group-hover:animate-bounce">location_on</span>
            Halaman Absensi
        </a>
        <button class="flex-1 md:flex-none px-6 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-bold text-sm bg-surface-container-low transition-all flex items-center justify-center gap-2 cursor-not-allowed opacity-50" disabled id="btn-clockout">
            <span class="material-symbols-outlined text-[18px]">logout</span>
            Clock Out
        </button>
    </div>
</section>

<!-- Charts Bento Grid -->
<section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Attendance Trend -->
    <div class="lg:col-span-2 bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow">
        <div class="flex items-center justify-between mb-8">
            <h4 class="font-title-sm text-title-sm text-on-background font-bold">Rekap Kehadiran Bulanan</h4>
            <div class="flex gap-2">
                <button class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-surface-container-low text-primary">Bulanan</button>
                <button class="text-xs font-semibold px-3 py-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors">Mingguan</button>
            </div>
        </div>
        <div class="h-[280px] w-full relative" id="chart-container">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>
    <!-- Employee Status -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow flex flex-col">
        <h4 class="font-title-sm text-title-sm text-on-background font-bold mb-8">Status Karyawan</h4>
        <div class="flex-1 flex flex-col items-center justify-center relative">
            <!-- Donut Chart -->
            @php
                $total = $total_karyawan_dept > 0 ? $total_karyawan_dept : 1; // Prevent division by zero
                $pct_tetap = round(($status_tetap / $total) * 100);
                $pct_kontrak = round(($status_kontrak / $total) * 100);
                $pct_magang = round(($status_magang / $total) * 100);
            @endphp
            <div class="w-48 h-48 relative flex items-center justify-center">
                <canvas id="statusChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-headline-md font-display-lg block leading-none font-bold text-on-background" id="donut-total">{{ $total_karyawan_dept }}</span>
                    <span class="text-[10px] text-on-surface-variant uppercase tracking-wider mt-1">Total</span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-8 w-full">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-primary"></span>
                    <span class="text-body-sm text-on-surface-variant">Tetap ({{ $pct_tetap }}%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-surface-container-highest"></span>
                    <span class="text-body-sm text-on-surface-variant">Kontrak ({{ $pct_kontrak }}%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-tertiary-fixed-dim"></span>
                    <span class="text-body-sm text-on-surface-variant">Magang ({{ $pct_magang }}%)</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Table Section -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-xl card-shadow overflow-hidden">
    <div class="px-8 py-6 flex items-center justify-between border-b border-outline-variant/10">
        <h4 class="font-title-sm text-title-sm text-on-background font-bold">Karyawan Terbaru Departemen</h4>
        <div class="flex gap-4">
            <div class="relative">
                <input class="text-sm px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary/20 outline-none w-48 bg-white" placeholder="Cari nama..." type="text" id="search-anggota">
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left whitespace-nowrap">
            <thead>
                <tr class="bg-surface-container-low text-on-secondary-container">
                    <th class="font-table-header text-table-header px-8 py-4 uppercase tracking-wider">Nama</th>
                    <th class="font-table-header text-table-header px-8 py-4 uppercase tracking-wider">NIK</th>
                    <th class="font-table-header text-table-header px-8 py-4 uppercase tracking-wider">Jabatan</th>
                    <th class="font-table-header text-table-header px-8 py-4 uppercase tracking-wider">Department</th>
                    <th class="font-table-header text-table-header px-8 py-4 uppercase tracking-wider text-center">Status</th>
                    <th class="font-table-header text-table-header px-8 py-4 uppercase tracking-wider text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10 font-body-sm text-body-sm" id="table-anggota-body">
                @forelse($latest_employees as $emp)
                <tr class="hover:bg-primary/5 transition-colors group" data-nik="{{ $emp->nik }}" data-status="{{ $emp->status_kerja ?? 'Tetap' }}">
                    <td class="px-8 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center text-xs font-bold text-primary">
                                {{ strtoupper(substr($emp->nama_lengkap ?? 'U', 0, 2)) }}
                            </div>
                            <span class="font-medium text-on-background">{{ $emp->nama_lengkap }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-4 text-on-surface-variant font-mono text-sm">{{ $emp->nik }}</td>
                    <td class="px-8 py-4 text-on-surface-variant">{{ $emp->jabatan ?? 'Karyawan' }}</td>
                    <td class="px-8 py-4">
                        <span class="px-2 py-1 rounded bg-secondary-container/30 text-secondary text-xs font-semibold uppercase">{{ $emp->department->nama_department ?? 'Umum' }}</span>
                    </td>
                    <td class="px-8 py-4 text-center">
                        <span class="px-3 py-1 rounded-full bg-primary-container/10 text-primary text-xs font-bold">{{ $emp->status_kerja ?? 'Tetap' }}</span>
                    </td>
                    <td class="px-8 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button class="w-8 h-8 rounded-lg bg-surface-container-low text-primary flex items-center justify-center hover:bg-primary hover:text-white transition-all cursor-pointer" title="Detail"><span class="material-symbols-outlined text-lg">search</span></button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-6 text-center text-slate-500">Belum ada karyawan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-8 py-4 border-t border-outline-variant/10 flex items-center justify-between">
        <p class="text-body-sm text-on-surface-variant" id="table-entries-info">Menampilkan terbaru dari {{ $total_karyawan_dept }} entri</p>
        <div class="flex gap-1">
        </div>
    </div>
</section>
@endsection

@push('modals')
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Initialize Chart.js Data
    const attendanceTrendLabels = @json($attendance_trend['labels'] ?? []);
    const attendanceTrendData = @json($attendance_trend['data'] ?? []);
    const statusData = [{{ $status_tetap }}, {{ $status_kontrak }}, {{ $status_magang }}];
    
    // Attendance Line Chart
    const ctxAttendance = document.getElementById('attendanceChart').getContext('2d');
    
    // Create Gradient for Line Chart
    let gradientLine = ctxAttendance.createLinearGradient(0, 0, 0, 400);
    gradientLine.addColorStop(0, 'rgba(0, 102, 255, 0.4)');
    gradientLine.addColorStop(1, 'rgba(0, 102, 255, 0)');

    new Chart(ctxAttendance, {
        type: 'line',
        data: {
            labels: attendanceTrendLabels,
            datasets: [{
                label: 'Kehadiran (Orang)',
                data: attendanceTrendData,
                borderColor: '#0066ff',
                backgroundColor: gradientLine,
                borderWidth: 3,
                pointBackgroundColor: '#0066ff',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 13, family: "'Inter', sans-serif" },
                    bodyFont: { size: 12, family: "'Inter', sans-serif" },
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)', borderDash: [5, 5] },
                    ticks: { precision: 0, font: { family: "'Inter', sans-serif", size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: "'Inter', sans-serif", size: 11 } }
                }
            }
        }
    });

    // Status Karyawan Doughnut Chart
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Tetap', 'Kontrak', 'Magang'],
            datasets: [{
                data: statusData,
                backgroundColor: ['#0066ff', '#f1f5f9', '#94a3b8'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    bodyFont: { size: 12, family: "'Inter', sans-serif" },
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: true
                }
            }
        }
    });



    // ==========================================
    // 6. Fungsi Update Widget Statistik
    // ==========================================
    function updateStatistics() {
        const totalRows = tableAnggotaBody.querySelectorAll('tr').length;
        statTotalKaryawan.innerText = totalRows;
        statHadirKaryawan.innerText = totalRows; // Semua hadir dalam simulasi
        donutTotal.innerText = totalRows;
        
        // Gaji simulasi (Tetap = 8.5jt, Kontrak = 7jt, Magang = 4jt)
        let totalGaji = 0;
        tableAnggotaBody.querySelectorAll('tr').forEach(row => {
            const status = row.getAttribute('data-status');
            if (status === 'Tetap') totalGaji += 8.5;
            else if (status === 'Kontrak') totalGaji += 7.0;
            else totalGaji += 4.0;
        });
        
        statHadirKaryawan.innerText = totalRows;
        document.getElementById('stat-belum-absen').innerText = "0";
        document.getElementById('table-entries-info').innerText = `Menampilkan 1 - ${totalRows} dari ${totalRows} entri`;
    }

    // ==========================================
    // 7. Logika Tooltip Interaktif untuk Chart SVG
    // ==========================================
    const chartTooltip = document.getElementById('chart-tooltip');
    const tooltipTitle = document.getElementById('tooltip-title');
    const tooltipValue = document.getElementById('tooltip-value');
    const chartContainer = document.getElementById('chart-container');

    document.querySelectorAll('.chart-point').forEach(point => {
        point.addEventListener('mouseenter', (e) => {
            const title = point.getAttribute('data-title');
            const value = point.getAttribute('data-value');
            
            tooltipTitle.innerText = title;
            tooltipValue.innerText = value;
            
            chartTooltip.classList.remove('hidden');
            
            // Dapatkan ukuran kontainer dan titik koordinat
            const rect = point.getBoundingClientRect();
            const containerRect = chartContainer.getBoundingClientRect();
            
            // Atur posisi tooltip melayang tepat di atas titik circle
            const x = rect.left - containerRect.left + (rect.width / 2) - (chartTooltip.offsetWidth / 2);
            const y = rect.top - containerRect.top - chartTooltip.offsetHeight - 10;
            
            chartTooltip.style.left = `${x}px`;
            chartTooltip.style.top = `${y}px`;
        });
        
        point.addEventListener('mouseleave', () => {
            chartTooltip.classList.add('hidden');
        });
    });

    // ==========================================
    // Real-time Clock untuk Widget Absensi
    // ==========================================
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        const clockElement = document.getElementById('realtime-clock');
        if(clockElement) {
            clockElement.textContent = `${hours}:${minutes}:${seconds} WIB`;
        }
    }
    
    // Update jam setiap detik
    setInterval(updateClock, 1000);
    updateClock(); // Initialize immediately
</script>
@endpush
