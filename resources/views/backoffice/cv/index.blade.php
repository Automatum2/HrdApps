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
                                class="btn-confirm-approve text-xs bg-green-600 text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-green-700 transition-colors cursor-pointer inline-flex items-center gap-1 shadow-sm active:scale-95"
                                data-nama="{{ $applicant->nama_lengkap }}"
                                data-action="{{ route('backoffice.cv.approve', $applicant->id) }}">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                <span>Terima</span>
                            </button>

                            <button type="button" 
                                class="btn-confirm-reject text-xs bg-red-600 text-white px-3 py-1.5 rounded-lg font-semibold hover:bg-red-700 transition-colors cursor-pointer inline-flex items-center gap-1 shadow-sm active:scale-95"
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
    
    @if($applicants->hasPages())
    <div class="px-6 py-4 border-t border-outline-variant bg-surface flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-xs text-on-surface-variant">
            Menampilkan <span class="font-bold text-on-surface">{{ $applicants->firstItem() ?? 0 }} - {{ $applicants->lastItem() ?? 0 }}</span> dari <span class="font-bold text-on-surface">{{ $applicants->total() }}</span> pelamar
        </p>
        <div>
            {{ $applicants->links() }}
        </div>
    </div>
    @endif
</div>
@endsection

@push('modals')
<!-- Modal Lihat CV -->
<div class="bg-slate-900/60 backdrop-blur-sm" id="modal-cv" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 16px;">
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant flex flex-col max-h-[90vh] overflow-hidden animate-modal-pop" style="width: 100%; max-width: 650px; min-width: 280px; display: flex; flex-direction: column;">
        <!-- Header Modal -->
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface">
            <div>
                <h3 class="font-title-sm text-title-sm text-on-surface font-bold flex items-center gap-2">
                    <span id="cv-nama">Nama Pelamar</span>
                    <span id="cv-status-kerja" class="text-[11px] font-semibold uppercase px-2 py-0.5 rounded bg-blue-100 text-blue-700">Tetap</span>
                </h3>
                <p id="cv-email" class="text-xs text-slate-500 font-mono mt-0.5">email@example.com</p>
            </div>
            <button type="button" onclick="closeModal('modal-cv')" class="p-1 hover:bg-slate-200 rounded-full text-slate-400 cursor-pointer material-symbols-outlined">close</button>
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
        <div class="px-6 py-4 border-t border-outline-variant bg-surface flex justify-between items-center">
            <button type="button" onclick="closeModal('modal-cv')" class="border border-slate-300 hover:bg-slate-50 text-slate-600 px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-all">
                Tutup
            </button>
            <div class="flex items-center gap-2">
                <button type="button" id="cv-modal-reject-btn" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-all inline-flex items-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-base">cancel</span>
                    <span>Tolak Pelamar</span>
                </button>
                <button type="button" id="cv-modal-approve-btn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer active:scale-95 transition-all inline-flex items-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-base">check_circle</span>
                    <span>Terima Pelamar</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Dialog Konfirmasi Terima (Approve) -->
<div class="bg-slate-900/60 backdrop-blur-sm" id="modal-confirm-approve" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 16px;">
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant p-6 text-center animate-modal-pop" style="width: 100%; max-width: 420px; min-width: 280px; display: flex; flex-direction: column; align-items: center;">
        <div class="w-14 h-14 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-3xl">check_circle</span>
        </div>
        <h3 class="font-bold text-slate-800 text-lg mb-2">Terima Pelamar?</h3>
        <p class="text-sm text-slate-500 mb-6 leading-relaxed">
            Apakah Anda yakin ingin menerima <span class="font-bold text-slate-800" id="approve-applicant-name">Nama</span> sebagai Karyawan Magang? Notifikasi penerimaan akan dikirimkan.
        </p>
        <form id="form-confirm-approve" method="POST" action="" class="w-full">
            @csrf
            <div class="flex gap-3 justify-center w-full">
                <button type="button" class="flex-1 border border-slate-300 hover:bg-slate-50 text-slate-600 py-2.5 rounded-lg text-sm font-semibold cursor-pointer transition-all" onclick="closeModal('modal-confirm-approve')">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2.5 rounded-lg text-sm font-semibold cursor-pointer transition-all">
                    Ya, Terima
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Dialog Konfirmasi Tolak (Reject) -->
<div class="bg-slate-900/60 backdrop-blur-sm" id="modal-confirm-reject" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 9999; display: none; align-items: center; justify-content: center; padding: 16px;">
    <div class="bg-white rounded-xl shadow-xl border border-outline-variant p-6 text-center animate-modal-pop" style="width: 100%; max-width: 420px; min-width: 280px; display: flex; flex-direction: column; align-items: center;">
        <div class="w-14 h-14 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-3xl">warning</span>
        </div>
        <h3 class="font-bold text-slate-800 text-lg mb-2">Tolak Lamaran?</h3>
        <p class="text-sm text-slate-500 mb-6 leading-relaxed">
            Apakah Anda yakin ingin menolak pelamar <span class="font-bold text-slate-800" id="reject-applicant-name">Nama</span>? Data pelamar akan dihapus dan email penolakan akan dikirim.
        </p>
        <form id="form-confirm-reject" method="POST" action="" class="w-full">
            @csrf
            <div class="flex gap-3 justify-center w-full">
                <button type="button" class="flex-1 border border-slate-300 hover:bg-slate-50 text-slate-600 py-2.5 rounded-lg text-sm font-semibold cursor-pointer transition-all" onclick="closeModal('modal-confirm-reject')">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-lg text-sm font-semibold cursor-pointer transition-all">
                    Ya, Tolak
                </button>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.style.display = 'flex';
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
        let activeApplicantName = '';
        let activeApproveUrl = '';
        let activeRejectUrl = '';

        window.showApproveModal = (nama, actionUrl) => {
            document.getElementById('approve-applicant-name').innerText = nama;
            document.getElementById('form-confirm-approve').action = actionUrl;
            openModal('modal-confirm-approve');
        };

        window.showRejectModal = (nama, actionUrl) => {
            document.getElementById('reject-applicant-name').innerText = nama;
            document.getElementById('form-confirm-reject').action = actionUrl;
            openModal('modal-confirm-reject');
        };

        document.addEventListener('click', (e) => {
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

                const cvContentEl = document.getElementById('cv-content');
                if (cvHtml && cvHtml !== '') {
                    cvContentEl.innerHTML = cvHtml;
                } else {
                    cvContentEl.innerHTML = '<span class="text-slate-400 italic">Tidak ada ringkasan teks profil yang dituliskan pelamar.</span>';
                }

                openModal('modal-cv');
                return;
            }

            const btnApprove = e.target.closest('.btn-confirm-approve');
            if (btnApprove) {
                showApproveModal(btnApprove.dataset.nama, btnApprove.dataset.action);
                return;
            }

            const btnReject = e.target.closest('.btn-confirm-reject');
            if (btnReject) {
                showRejectModal(btnReject.dataset.nama, btnReject.dataset.action);
                return;
            }
        });

        document.getElementById('cv-modal-approve-btn').addEventListener('click', () => {
            closeModal('modal-cv');
            showApproveModal(activeApplicantName, activeApproveUrl);
        });

        document.getElementById('cv-modal-reject-btn').addEventListener('click', () => {
            closeModal('modal-cv');
            showRejectModal(activeApplicantName, activeRejectUrl);
        });
    });
</script>
@endpush
