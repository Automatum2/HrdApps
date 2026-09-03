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
                            <button type="button" 
                                class="btn-lihat-cv text-xs bg-primary/10 text-primary px-3 py-1.5 rounded-lg font-semibold hover:bg-primary/20 transition-colors cursor-pointer inline-flex items-center gap-1"
                                data-id="{{ $applicant->id }}"
                                data-nama="{{ $applicant->nama_lengkap }}"
                                data-email="{{ $applicant->email }}"
                                data-status="{{ $applicant->status_kerja }}"
                                data-url="{{ $applicant->cv_url }}"
                                data-file="{{ $applicant->cv_file }}"
                                data-approve-url="{{ route('backoffice.cv.approve', $applicant->id) }}"
                                data-reject-url="{{ route('backoffice.cv.reject', $applicant->id) }}">
                                <div class="hidden cv-text-data">{!! $applicant->cv_text !!}</div>
                                <span class="material-symbols-outlined text-sm">visibility</span>
                                <span>Lihat CV</span>
                            </button>

                            <button type="button" 
                                class="btn-confirm-approve text-xs bg-emerald-600 text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-emerald-700 transition-colors cursor-pointer inline-flex items-center gap-1 shadow-sm active:scale-95"
                                data-nama="{{ $applicant->nama_lengkap }}"
                                data-action="{{ route('backoffice.cv.approve', $applicant->id) }}">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                <span>Terima</span>
                            </button>

                            <button type="button" 
                                class="btn-confirm-reject text-xs bg-rose-600 text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-rose-700 transition-colors cursor-pointer inline-flex items-center gap-1 shadow-sm active:scale-95"
                                data-nama="{{ $applicant->nama_lengkap }}"
                                data-action="{{ route('backoffice.cv.reject', $applicant->id) }}">
                                <span class="material-symbols-outlined text-sm">cancel</span>
                                <span>Tolak</span>
                            </button>
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
    <div class="bg-white rounded-xl shadow-2xl border border-slate-200 flex flex-col max-h-[85vh] overflow-hidden animate-modal-pop" style="width: 100%; max-width: 650px; min-width: 280px;">
        <!-- Header Modal -->
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
            <div>
                <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                    <span id="cv-nama">Nama Pelamar</span>
                    <span id="cv-status-kerja" class="text-[11px] font-semibold uppercase px-2 py-0.5 rounded bg-blue-100 text-blue-700">Tetap</span>
                </h3>
                <p id="cv-email" class="text-xs text-slate-500 font-mono mt-0.5">email@example.com</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-cv').classList.add('hidden')" class="p-1.5 hover:bg-slate-200 rounded-full text-slate-500 cursor-pointer material-symbols-outlined transition-colors">close</button>
        </div>

        <!-- Body Content -->
        <div class="p-6 overflow-y-auto space-y-5">
            <!-- Lampiran File & Tautan URL -->
            <div id="cv-attachments" class="hidden flex-wrap gap-3 p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                <!-- Tautan Portofolio / Medsos -->
                <a id="cv-url-btn" href="#" target="_blank" class="hidden items-center gap-2 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition-all shadow-sm">
                    <span class="material-symbols-outlined text-base">link</span>
                    <span>Buka LinkedIn / Portofolio</span>
                    <span class="material-symbols-outlined text-xs">open_in_new</span>
                </a>

                <!-- Download File CV -->
                <a id="cv-file-btn" href="#" target="_blank" class="hidden items-center gap-2 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition-all shadow-sm">
                    <span class="material-symbols-outlined text-base">download</span>
                    <span>Unduh Dokumen File CV</span>
                </a>
            </div>

            <!-- Konten Teks Profil CV -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Profil & Riwayat Hidup</h4>
                <div id="cv-content" class="text-sm text-slate-800 leading-relaxed font-sans prose max-w-none p-1">
                    <!-- CV Text Content Rendered Here -->
                </div>
            </div>
        </div>

        <!-- Footer Modal dengan Aksi Langsung -->
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex flex-wrap items-center justify-between gap-3">
            <button type="button" onclick="document.getElementById('modal-cv').classList.add('hidden')" class="border border-slate-300 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-all">
                Tutup
            </button>
            <div class="flex items-center gap-2">
                <button type="button" id="cv-modal-reject-btn" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-all inline-flex items-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-base">cancel</span>
                    <span>Tolak Pelamar</span>
                </button>
                <button type="button" id="cv-modal-approve-btn" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-all inline-flex items-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-base">check_circle</span>
                    <span>Terima Pelamar</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Terima (Approve) -->
<div id="modal-confirm-approve" class="hidden fixed inset-0 z-[10000] flex items-center justify-center bg-[#0b1c30]/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 p-6 max-w-md w-full text-center space-y-4 animate-modal-pop">
        <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto shadow-inner">
            <span class="material-symbols-outlined text-3xl">check_circle</span>
        </div>
        <div>
            <h3 class="text-lg font-bold text-slate-800">Terima Pelamar?</h3>
            <p class="text-sm text-slate-600 mt-1">
                Apakah Anda yakin ingin menerima <strong id="approve-applicant-name" class="text-slate-800"></strong> sebagai Karyawan Magang? Notifikasi penerimaan akan dikirimkan.
            </p>
        </div>
        <form id="form-confirm-approve" method="POST" action="">
            @csrf
            <div class="flex items-center justify-center gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-confirm-approve').classList.add('hidden')" class="w-1/2 py-2.5 border border-slate-300 rounded-xl text-slate-700 font-semibold text-sm hover:bg-slate-100 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="w-1/2 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold text-sm transition-all shadow-md active:scale-95 cursor-pointer">
                    Ya, Terima
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Tolak (Reject) -->
<div id="modal-confirm-reject" class="hidden fixed inset-0 z-[10000] flex items-center justify-center bg-[#0b1c30]/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 p-6 max-w-md w-full text-center space-y-4 animate-modal-pop">
        <div class="w-14 h-14 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto shadow-inner">
            <span class="material-symbols-outlined text-3xl">warning</span>
        </div>
        <div>
            <h3 class="text-lg font-bold text-slate-800">Tolak Lamaran?</h3>
            <p class="text-sm text-slate-600 mt-1">
                Apakah Anda yakin ingin menolak pelamar <strong id="reject-applicant-name" class="text-slate-800"></strong>? Data pelamar akan dihapus dan email penolakan akan dikirim.
            </p>
        </div>
        <form id="form-confirm-reject" method="POST" action="">
            @csrf
            <div class="flex items-center justify-center gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-confirm-reject').classList.add('hidden')" class="w-1/2 py-2.5 border border-slate-300 rounded-xl text-slate-700 font-semibold text-sm hover:bg-slate-100 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="w-1/2 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-semibold text-sm transition-all shadow-md active:scale-95 cursor-pointer">
                    Ya, Tolak
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let activeApplicantName = '';
        let activeApproveUrl = '';
        let activeRejectUrl = '';

        // Helper untuk membuka modal konfirmasi Terima
        window.showApproveModal = (nama, actionUrl) => {
            document.getElementById('approve-applicant-name').innerText = nama;
            document.getElementById('form-confirm-approve').action = actionUrl;
            document.getElementById('modal-confirm-approve').classList.remove('hidden');
        };

        // Helper untuk membuka modal konfirmasi Tolak
        window.showRejectModal = (nama, actionUrl) => {
            document.getElementById('reject-applicant-name').innerText = nama;
            document.getElementById('form-confirm-reject').action = actionUrl;
            document.getElementById('modal-confirm-reject').classList.remove('hidden');
        };

        // Event listener klik untuk tombol Lihat CV, Terima, dan Tolak
        document.addEventListener('click', (e) => {
            // 1. Tombol Lihat CV
            const btnCv = e.target.closest('.btn-lihat-cv');
            if (btnCv) {
                activeApplicantName = btnCv.dataset.nama || 'Pelamar';
                const email = btnCv.dataset.email || '-';
                const statusKerja = btnCv.dataset.status || 'tetap';
                const cvUrl = btnCv.dataset.url;
                const cvFile = btnCv.dataset.file;
                activeApproveUrl = btnCv.dataset.approveUrl;
                activeRejectUrl = btnCv.dataset.rejectUrl;
                
                const cvTextHolder = btnCv.querySelector('.cv-text-data');
                const cvHtml = cvTextHolder ? cvTextHolder.innerHTML.trim() : '';

                document.getElementById('cv-nama').innerText = activeApplicantName;
                document.getElementById('cv-email').innerText = email;

                const statusLabelMap = {
                    'tetap': 'Karyawan Tetap',
                    'kontrak': 'Karyawan Kontrak',
                    'harian': 'Karyawan Harian',
                    'tenaga_lepas': 'Tenaga Lepas'
                };
                document.getElementById('cv-status-kerja').innerText = statusLabelMap[statusKerja] || statusKerja;

                // Handling Tautan URL & Dokumen File
                const attachmentsContainer = document.getElementById('cv-attachments');
                const urlBtn = document.getElementById('cv-url-btn');
                const fileBtn = document.getElementById('cv-file-btn');
                
                let hasAttachments = false;

                if (cvUrl && cvUrl.trim() !== '') {
                    urlBtn.href = cvUrl;
                    urlBtn.classList.remove('hidden');
                    urlBtn.classList.add('inline-flex');
                    hasAttachments = true;
                } else {
                    urlBtn.classList.add('hidden');
                    urlBtn.classList.remove('inline-flex');
                }

                if (cvFile && cvFile.trim() !== '') {
                    fileBtn.href = '/storage/' + cvFile;
                    fileBtn.classList.remove('hidden');
                    fileBtn.classList.add('inline-flex');
                    hasAttachments = true;
                } else {
                    fileBtn.classList.add('hidden');
                    fileBtn.classList.remove('inline-flex');
                }

                if (hasAttachments) {
                    attachmentsContainer.classList.remove('hidden');
                    attachmentsContainer.classList.add('flex');
                } else {
                    attachmentsContainer.classList.add('hidden');
                    attachmentsContainer.classList.remove('flex');
                }

                // Render HTML Profil secara bersih
                const cvContentEl = document.getElementById('cv-content');
                if (cvHtml && cvHtml !== '') {
                    cvContentEl.innerHTML = cvHtml;
                } else {
                    cvContentEl.innerHTML = '<span class="text-slate-400 italic">Tidak ada ringkasan teks profil yang dituliskan pelamar.</span>';
                }

                document.getElementById('modal-cv').classList.remove('hidden');
                return;
            }

            // 2. Tombol Terima di Tabel
            const btnApprove = e.target.closest('.btn-confirm-approve');
            if (btnApprove) {
                showApproveModal(btnApprove.dataset.nama, btnApprove.dataset.action);
                return;
            }

            // 3. Tombol Tolak di Tabel
            const btnReject = e.target.closest('.btn-confirm-reject');
            if (btnReject) {
                showRejectModal(btnReject.dataset.nama, btnReject.dataset.action);
                return;
            }
        });

        // Event Listener tombol Terima / Tolak dari dalam Modal CV
        document.getElementById('cv-modal-approve-btn').addEventListener('click', () => {
            document.getElementById('modal-cv').classList.add('hidden');
            showApproveModal(activeApplicantName, activeApproveUrl);
        });

        document.getElementById('cv-modal-reject-btn').addEventListener('click', () => {
            document.getElementById('modal-cv').classList.add('hidden');
            showRejectModal(activeApplicantName, activeRejectUrl);
        });
    });
</script>
@endpush
