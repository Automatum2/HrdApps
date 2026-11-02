@extends('layouts.admin')

@section('title', 'Rekap Absensi Karyawan - HRDApps')
@section('page_title', 'Rekap Absensi Karyawan')

@section('content')
<!-- Header & Breadcrumbs -->
<div class="flex flex-col md:flex-row md:items-end justify-between mb-6 gap-4">
    <div>
        <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-1">
            <a class="hover:text-primary transition-colors" href="{{ $back_route }}">Kelola Karyawan</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <a class="hover:text-primary transition-colors" href="{{ route('backoffice.absensi') }}">Absensi</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary font-semibold">Rekap {{ $employee->nama_lengkap }}</span>
        </nav>
        <h1 class="text-2xl font-bold text-on-surface">Riwayat Absensi Karyawan</h1>
        <p class="text-body-sm text-on-surface-variant">Seluruh riwayat kehadiran, izin, sakit, cuti, dan alpha karyawan secara lengkap.</p>
    </div>
    <a href="{{ $back_route }}" class="bg-surface-container-lowest border border-outline-variant text-on-surface font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl hover:bg-surface-container-low transition-colors flex items-center justify-center gap-2 cursor-pointer active:scale-95 shadow-sm">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        <span>Kembali</span>
    </a>
</div>

<!-- Profil Karyawan -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 sm:p-6 shadow-sm mb-6 flex flex-col sm:flex-row items-center gap-4">
    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-lg sm:text-xl shrink-0">
        {{ strtoupper(substr($employee->nama_lengkap ?? 'U', 0, 2)) }}
    </div>
    <div class="flex-1 text-center sm:text-left">
        <h2 class="font-bold text-on-surface text-base sm:text-lg">{{ $employee->nama_lengkap }}</h2>
        <p class="text-sm text-on-surface-variant font-mono">{{ $employee->nik }}</p>
        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-2">
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-secondary-container/30 text-secondary text-[11px] font-semibold uppercase">
                {{ $employee->department->nama_department ?? '-' }}
            </span>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant text-[11px] font-semibold uppercase">
                {{ $employee->position->nama_jabatan ?? 'Staff' }}
            </span>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-primary/10 text-primary text-[11px] font-semibold uppercase">
                {{ ucfirst($employee->status_kerja ?? 'tetap') }}
            </span>
        </div>
    </div>
</div>

<!-- Filter Rekap -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm mb-6">
    <form method="GET" action="{{ route('backoffice.absensi.rekap', $employee->id) }}" id="form-filter-rekap">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div class="space-y-1.5">
                <label class="text-xs uppercase font-bold text-on-surface-variant">Dari Tanggal</label>
                <input class="w-full border border-outline-variant rounded-lg text-sm px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white text-on-surface" type="date" name="dari_tanggal" value="{{ request('dari_tanggal', $dari) }}" id="filter-dari-tanggal"/>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs uppercase font-bold text-on-surface-variant">Sampai Tanggal</label>
                <input class="w-full border border-outline-variant rounded-lg text-sm px-3 py-2 focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white text-on-surface" type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal', $sampai) }}" id="filter-sampai-tanggal"/>
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
                <a href="{{ route('backoffice.absensi.rekap', $employee->id) }}" class="bg-surface-container-lowest border border-outline-variant text-on-surface font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl hover:bg-surface-container-low transition-colors flex items-center justify-center gap-2 cursor-pointer active:scale-95 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">filter_alt_off</span>
                    <span>Semua Riwayat</span>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Stats Rekap -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 mb-6">
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-sm">
        <p class="text-xs uppercase font-bold text-on-surface-variant tracking-wider">Total</p>
        <h4 class="text-headline-md font-bold text-on-surface mt-1">{{ $stats['total'] }}</h4>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-sm">
        <p class="text-xs uppercase font-bold text-on-surface-variant tracking-wider">Hadir</p>
        <h4 class="text-headline-md font-bold text-tertiary mt-1">{{ $stats['hadir'] }}</h4>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-sm">
        <p class="text-xs uppercase font-bold text-on-surface-variant tracking-wider">Izin</p>
        <h4 class="text-headline-md font-bold text-[#F59E0B] mt-1">{{ $stats['izin'] }}</h4>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-sm">
        <p class="text-xs uppercase font-bold text-on-surface-variant tracking-wider">Sakit</p>
        <h4 class="text-headline-md font-bold text-[#F97316] mt-1">{{ $stats['sakit'] }}</h4>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-sm">
        <p class="text-xs uppercase font-bold text-on-surface-variant tracking-wider">Cuti</p>
        <h4 class="text-headline-md font-bold text-primary mt-1">{{ $stats['cuti'] }}</h4>
    </div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 shadow-sm">
        <p class="text-xs uppercase font-bold text-on-surface-variant tracking-wider">Alpha</p>
        <h4 class="text-headline-md font-bold text-error mt-1">{{ $stats['alpha'] }}</h4>
    </div>
</div>

<!-- Tabel Riwayat -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col">
    <div class="p-6 border-b border-outline-variant bg-surface-container-low">
        <h2 class="font-bold text-sm text-on-surface">Riwayat Absensi {{ $employee->nama_lengkap }}</h2>
    </div>

    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left whitespace-nowrap border-collapse">
            <thead>
                <tr class="bg-surface-container-low text-on-surface-variant font-semibold text-xs border-b border-outline-variant/10">
                    <th class="px-6 py-4 uppercase">No</th>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Masuk</th>
                    <th class="px-6 py-4">Keluar</th>
                    <th class="px-6 py-4">Status Kerja</th>
                    <th class="px-6 py-4 text-center">Kehadiran</th>
                    <th class="px-6 py-4 text-center">Durasi</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10 font-body-sm text-body-sm">
                @forelse($attendances as $index => $att)
                <tr class="hover:bg-primary/5 transition-colors group">
                    <td class="px-6 py-4">{{ $attendances->firstItem() ? $attendances->firstItem() + $index : $index + 1 }}</td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ \Carbon\Carbon::parse($att->tanggal)->translatedFormat('j l F Y') }}</td>
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
                            <button class="p-1 hover:bg-surface-container-high rounded text-primary transition-colors cursor-pointer" title="Lihat Bukti Foto & Peta" onclick="showFotoModal('{{ $att->foto_masuk ? asset('storage/' . $att->foto_masuk) : '' }}', '{{ $att->foto_keluar ? asset('storage/' . $att->foto_keluar) : '' }}', '{{ addslashes($att->lokasi_masuk ?? '') }}', '{{ addslashes($att->lokasi_keluar ?? '') }}')">
                                <span class="material-symbols-outlined text-lg">image</span>
                            </button>
                            @if($att->dokumen_pendukung)
                            <a href="{{ asset('storage/' . $att->dokumen_pendukung) }}" target="_blank" class="p-1 hover:bg-surface-container-high rounded text-tertiary transition-colors cursor-pointer" title="Lihat Dokumen / Surat">
                                <span class="material-symbols-outlined text-lg">description</span>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-slate-500">
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
            <button onclick="closeFotoModal()" class="text-on-surface-variant hover:text-error transition-colors cursor-pointer p-1">
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
            <button onclick="closeFotoModal()" class="bg-surface-container border border-outline px-5 py-2 rounded-lg font-bold hover:bg-slate-100 transition-colors cursor-pointer text-sm">Tutup</button>
        </div>
    </div>
</div>
@endpush
@endsection

@push('scripts')
<script>
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

        imgIn.src = fotoMasuk;
        imgOut.src = fotoKeluar;

        if (fotoMasuk && fotoMasuk.trim() !== '') {
            imgIn.classList.remove('hidden');
            imgInNone.classList.add('hidden');
        } else {
            imgIn.classList.add('hidden');
            imgInNone.classList.remove('hidden');
        }

        if (fotoKeluar && fotoKeluar.trim() !== '') {
            imgOut.classList.remove('hidden');
            imgOutNone.classList.add('hidden');
        } else {
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

    function closeFotoModal() {
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
