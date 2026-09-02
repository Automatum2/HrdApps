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
        <span class="text-xs bg-primary/10 text-primary font-bold px-3 py-1 rounded-full">{{ $applicants->count() }} Pelamar</span>
    </div>

    @if(session('success'))
        <div class="m-4 bg-green-50 text-green-700 border border-green-200 rounded-lg p-4 flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="m-4 bg-red-50 text-red-700 border border-red-200 rounded-lg p-4 flex items-center gap-3">
            <span class="material-symbols-outlined text-red-600">error</span>
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
                    <th class="px-6 py-4">Status Dilamar</th>
                    <th class="px-6 py-4">Lampiran / Link</th>
                    <th class="px-6 py-4">Tanggal Melamar</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10 font-body-sm text-body-sm">
                @forelse($applicants as $index => $applicant)
                @php
                    $statusLabel = [
                        'tetap' => ['Tetap', 'bg-blue-50 text-blue-700 border-blue-200'],
                        'kontrak' => ['Kontrak', 'bg-amber-50 text-amber-700 border-amber-200'],
                        'harian' => ['Harian', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                        'tenaga_lepas' => ['Tenaga Lepas', 'bg-purple-50 text-purple-700 border-purple-200'],
                    ][$applicant->status_kerja] ?? [ucfirst($applicant->status_kerja ?? 'Tetap'), 'bg-slate-50 text-slate-700 border-slate-200'];
                @endphp
                <tr class="hover:bg-primary/5 transition-colors group">
                    <td class="px-6 py-4 text-center text-on-surface font-semibold font-mono">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 font-bold text-on-surface">{{ $applicant->nama_lengkap }}</td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ $applicant->email }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusLabel[1] }}">
                            {{ $statusLabel[0] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs">
                        <div class="flex items-center gap-2">
                            @if($applicant->cv_file)
                                <a href="{{ asset('storage/' . $applicant->cv_file) }}" target="_blank" class="inline-flex items-center gap-1 text-primary hover:underline font-semibold" title="Unduh File CV">
                                    <span class="material-symbols-outlined text-[16px]">description</span>
                                    <span>File CV</span>
                                </a>
                            @endif
                            @if($applicant->cv_url)
                                <a href="{{ $applicant->cv_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-blue-600 hover:underline font-semibold" title="Buka URL / Medsos">
                                    <span class="material-symbols-outlined text-[16px]">link</span>
                                    <span>Link/Medsos</span>
                                </a>
                            @endif
                            @if(!$applicant->cv_file && !$applicant->cv_url)
                                <span class="text-slate-400 italic">-</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ $applicant->created_at->format('d M Y, H:i') }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" onclick="bukaModalCV('{{ addslashes($applicant->nama_lengkap) }}', {{ json_encode($applicant->cv_text ?? '') }}, '{{ $applicant->cv_file ? asset('storage/' . $applicant->cv_file) : '' }}', '{{ $applicant->cv_url ?? '' }}')" class="text-xs bg-primary/10 text-primary px-3 py-1.5 rounded font-semibold hover:bg-primary/20 transition-colors cursor-pointer">
                                Lihat CV
                            </button>
                            <form action="{{ route('backoffice.cv.approve', $applicant->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin Menerima {{ addslashes($applicant->nama_lengkap) }} (Status: {{ $statusLabel[0] }})? Akun karyawan akan dibuat dan email aktivasi + OTP akan dikirim.');">
                                @csrf
                                <button type="submit" class="text-xs bg-green-600 text-white px-3 py-1.5 rounded font-semibold hover:bg-green-700 transition-colors cursor-pointer">
                                    Terima
                                </button>
                            </form>
                            <form action="{{ route('backoffice.cv.reject', $applicant->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin Menolak pelamar {{ addslashes($applicant->nama_lengkap) }}? Pelamar akan dihapus dan email penolakan akan dikirim.');">
                                @csrf
                                <button type="submit" class="text-xs bg-red-600 text-white px-3 py-1.5 rounded font-semibold hover:bg-red-700 transition-colors cursor-pointer">
                                    Tolak
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-on-surface-variant italic">Belum ada pelamar baru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Lihat CV -->
<div id="modal-cv" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-[#0b1c30]/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant flex flex-col max-h-[85vh] overflow-hidden animate-modal-pop" style="width: 100%; max-width: 680px; min-width: 280px;">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface">
            <div>
                <h3 class="font-title-sm text-title-sm text-on-surface font-bold">Detail CV Pelamar</h3>
                <p class="text-xs text-slate-500 mt-0.5" id="cv-nama">Nama Pelamar</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-cv').classList.add('hidden')" class="p-1 hover:bg-surface-container rounded-full text-on-surface-variant cursor-pointer material-symbols-outlined transition-colors">close</button>
        </div>
        <div class="p-6 overflow-y-auto space-y-4">
            <!-- Link & File Section -->
            <div id="cv-attachments" class="flex flex-wrap gap-2 hidden">
                <a id="modal-file-link" href="#" target="_blank" class="hidden inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary/10 text-primary rounded-lg text-xs font-semibold hover:bg-primary/20">
                    <span class="material-symbols-outlined text-sm">download</span>
                    <span>Unduh File CV Terlampir</span>
                </a>
                <a id="modal-url-link" href="#" target="_blank" rel="noopener noreferrer" class="hidden inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-semibold hover:bg-blue-100">
                    <span class="material-symbols-outlined text-sm">open_in_new</span>
                    <span>Buka URL / Medsos</span>
                </a>
            </div>

            <!-- CV HTML Content from Summernote -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Riwayat Hidup & Profil</label>
                <div id="cv-content" class="text-sm text-slate-700 bg-slate-50 p-4 rounded-lg border border-slate-200 prose prose-sm max-w-none min-h-[120px]">
                    <!-- CV content goes here -->
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-outline-variant flex justify-end bg-slate-50">
            <button type="button" onclick="document.getElementById('modal-cv').classList.add('hidden')" class="border border-outline-variant hover:bg-surface-container text-on-surface-variant px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-colors">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function bukaModalCV(nama, cvText, fileUrl, externalUrl) {
        document.getElementById('cv-nama').innerText = nama;
        
        const contentEl = document.getElementById('cv-content');
        if (cvText && cvText.trim()) {
            contentEl.innerHTML = cvText;
        } else {
            contentEl.innerHTML = '<p class="text-slate-400 italic">Pelamar tidak mengisi teks profil / riwayat hidup secara manual.</p>';
        }

        const attachBox = document.getElementById('cv-attachments');
        const fileLink = document.getElementById('modal-file-link');
        const urlLink = document.getElementById('modal-url-link');
        let hasAttachment = false;

        if (fileUrl) {
            fileLink.href = fileUrl;
            fileLink.classList.remove('hidden');
            hasAttachment = true;
        } else {
            fileLink.classList.add('hidden');
        }

        if (externalUrl) {
            urlLink.href = externalUrl;
            urlLink.classList.remove('hidden');
            hasAttachment = true;
        } else {
            urlLink.classList.add('hidden');
        }

        attachBox.classList.toggle('hidden', !hasAttachment);

        document.getElementById('modal-cv').classList.remove('hidden');
    }
</script>
@endpush
