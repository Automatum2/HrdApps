@extends('layouts.admin')

@section('title', 'Kelola Lamaran CV - HRDApps')
@section('page_title', 'Daftar Lamaran Masuk')

@section('content')
<div class="flex flex-col md:flex-row md:items-end justify-between mb-6">
    <div>
        <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-1">
            <a class="hover:text-primary transition-colors" href="{{ route('backoffice.dashboard') }}">Beranda</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary font-semibold">Kelola Lamaran (CV)</span>
        </nav>
        <p class="text-body-sm text-on-surface-variant">Review dan terima kandidat yang mengirimkan CV melalui portal karir.</p>
    </div>
</div>

<div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden flex flex-col">
    <div class="p-6 border-b border-outline-variant bg-surface-bright flex justify-between items-center">
        <h2 class="text-lg font-bold text-on-surface">Daftar Pelamar Baru</h2>
    </div>

    @if(session('success'))
        <div class="m-4 bg-green-50 text-green-700 border border-green-200 rounded-lg p-4 flex items-center gap-3">
            <span class="material-symbols-outlined">check_circle</span>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="m-4 bg-red-50 text-red-700 border border-red-200 rounded-lg p-4 flex items-center gap-3">
            <span class="material-symbols-outlined">error</span>
            <p class="text-sm font-semibold">{{ session('error') }}</p>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-surface-container text-on-surface uppercase tracking-wider font-semibold text-xs border-b border-outline-variant">
                    <th class="px-6 py-4 w-12 text-center">No</th>
                    <th class="px-6 py-4">Nama Pelamar</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Tanggal Melamar</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10 font-body-sm text-body-sm">
                @forelse($applicants as $index => $applicant)
                <tr class="hover:bg-primary/5 transition-colors group">
                    <td class="px-6 py-4 text-center text-on-surface font-semibold font-mono">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 font-bold text-on-surface">{{ $applicant->nama_lengkap }}</td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ $applicant->email }}</td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ $applicant->created_at->format('d M Y, H:i') }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" onclick="bukaModalCV('{{ $applicant->nama_lengkap }}', `{{ htmlspecialchars($applicant->cv_text, ENT_QUOTES) }}`)" class="text-xs bg-primary/10 text-primary px-3 py-1.5 rounded font-semibold hover:bg-primary/20 transition-colors">
                                Lihat CV
                            </button>
                            <form action="{{ route('backoffice.cv.approve', $applicant->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin Menerima {{ $applicant->nama_lengkap }} sebagai Karyawan Magang?');">
                                @csrf
                                <button type="submit" class="text-xs bg-green-600 text-white px-3 py-1.5 rounded font-semibold hover:bg-green-700 transition-colors">
                                    Terima
                                </button>
                            </form>
                            <form action="{{ route('backoffice.cv.reject', $applicant->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin Menolak pelamar {{ $applicant->nama_lengkap }}? Pelamar akan dihapus dan email penolakan akan dikirim.');">
                                @csrf
                                <button type="submit" class="text-xs bg-red-600 text-white px-3 py-1.5 rounded font-semibold hover:bg-red-700 transition-colors">
                                    Tolak
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-on-surface-variant italic">Belum ada pelamar baru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Lihat CV -->
<div id="modal-cv" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-[#0b1c30]/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant flex flex-col max-h-[85vh] overflow-hidden animate-modal-pop" style="width: 100%; max-width: 600px; min-width: 280px;">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface">
            <h3 class="font-title-sm text-title-sm text-on-surface font-bold">CV: <span id="cv-nama">Nama</span></h3>
            <button type="button" onclick="document.getElementById('modal-cv').classList.add('hidden')" class="p-1 hover:bg-surface-container rounded-full text-on-surface-variant cursor-pointer material-symbols-outlined transition-colors">close</button>
        </div>
        <div class="p-6 overflow-y-auto">
            <div id="cv-content" class="text-sm text-slate-700 whitespace-pre-wrap font-mono bg-slate-50 p-4 rounded-lg border border-slate-200">
                <!-- CV content goes here -->
            </div>
        </div>
        <div class="px-6 py-4 border-t border-outline-variant flex justify-end">
            <button type="button" onclick="document.getElementById('modal-cv').classList.add('hidden')" class="border border-outline-variant hover:bg-surface-container text-on-surface-variant px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-colors">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function bukaModalCV(nama, cvText) {
        document.getElementById('cv-nama').innerText = nama;
        document.getElementById('cv-content').innerText = cvText;
        document.getElementById('modal-cv').classList.remove('hidden');
    }
</script>
@endpush
