@extends('layouts.admin')

@section('title', 'Pengaturan Sistem - HRDApps')
@section('page_title', 'Pengaturan Sistem')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <nav class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm mb-1">
        <a class="hover:text-primary transition-colors text-xs" href="{{ route('backoffice.dashboard') }}">Beranda</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-primary font-semibold text-xs">Pengaturan</span>
    </nav>
    <p class="text-body-sm text-on-surface-variant">Kelola profil akun administrator, preferensi pemberitahuan, konfigurasi HR perusahaan, serta riwayat keamanan.</p>
</div>

@if(session('success'))
<div class="mb-6 p-4 rounded-xl bg-success/10 border border-success/20 text-success flex items-center gap-3 animate-page-in">
    <span class="material-symbols-outlined">check_circle</span>
    <p class="font-medium text-sm">{{ session('success') }}</p>
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 rounded-xl bg-error/10 border border-error/20 text-error flex items-center gap-3 animate-page-in">
    <span class="material-symbols-outlined">error</span>
    <p class="font-medium text-sm">{{ session('error') }}</p>
</div>
@endif

@if ($errors->any())
<div class="mb-6 p-4 rounded-xl bg-error/10 border border-error/20 text-error flex flex-col gap-2 animate-page-in shadow-sm sticky top-4 z-50">
    <div class="flex items-center gap-3">
        <span class="material-symbols-outlined">error</span>
        <p class="font-bold text-sm">Terjadi Kesalahan Upload:</p>
    </div>
    <ul class="list-disc list-inside text-xs ml-8 font-medium">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Settings Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start animate-stagger animate-page-in">
    <!-- Navigation Tabs (Left Column) -->
    <div class="lg:col-span-3 flex flex-row lg:flex-col overflow-x-auto lg:overflow-visible gap-2 pb-4 lg:pb-0 custom-scrollbar whitespace-nowrap">
        <!-- Tab 1: Profil -->
        <button class="tab-btn flex-1 lg:flex-none flex items-center gap-3 p-4 rounded-xl border border-primary bg-white text-left shadow-sm transition-all cursor-pointer" id="tab-profil" onclick="switchTab('profil')">
            <span class="material-symbols-outlined p-2 rounded-lg text-primary bg-primary/10">person</span>
            <div>
                <span class="block font-bold text-xs text-on-surface">Profil Akun</span>
                <span class="block text-[10px] text-on-surface-variant font-medium">Informasi personal</span>
            </div>
        </button>
        <!-- Tab 2: Notifikasi -->
        <button class="tab-btn flex-1 lg:flex-none flex items-center gap-3 p-4 rounded-xl border border-transparent bg-transparent text-left hover:bg-surface-container-low transition-all cursor-pointer" id="tab-notifikasi" onclick="switchTab('notifikasi')">
            <span class="material-symbols-outlined p-2 rounded-lg text-on-surface-variant bg-surface-container-high">notifications_active</span>
            <div>
                <span class="block font-bold text-xs text-on-surface">Notifikasi</span>
                <span class="block text-[10px] text-on-surface-variant font-medium">Preferensi waspada</span>
            </div>
        </button>
        @if(session('user_role') !== 'employee')
        <!-- Tab 3: Konfigurasi HR -->
        <button class="tab-btn flex-1 lg:flex-none flex items-center gap-3 p-4 rounded-xl border border-transparent bg-transparent text-left hover:bg-surface-container-low transition-all cursor-pointer" id="tab-konfigurasi" onclick="switchTab('konfigurasi')">
            <span class="material-symbols-outlined p-2 rounded-lg text-on-surface-variant bg-surface-container-high">business_center</span>
            <div>
                <span class="block font-bold text-xs text-on-surface">Konfigurasi HR</span>
                <span class="block text-[10px] text-on-surface-variant font-medium">Parameter sistem</span>
            </div>
        </button>
        @endif
        <!-- Tab 4: Keamanan -->
        <button class="tab-btn flex-1 lg:flex-none flex items-center gap-3 p-4 rounded-xl border border-transparent bg-transparent text-left hover:bg-surface-container-low transition-all cursor-pointer" id="tab-keamanan" onclick="switchTab('keamanan')">
            <span class="material-symbols-outlined p-2 rounded-lg text-on-surface-variant bg-surface-container-high">security</span>
            <div>
                <span class="block font-bold text-xs text-on-surface">Keamanan</span>
                <span class="block text-[10px] text-on-surface-variant font-medium">Akses & Otentikasi</span>
            </div>
        </button>
    </div>

    <!-- Settings Panels (Right Column) -->
    <div class="lg:col-span-9 space-y-6">
        <!-- Panel 1: Profil Akun -->
        <div class="settings-panel space-y-6" id="panel-profil">
            <div class="bg-white border border-outline-variant rounded-xl p-6 shadow-sm">
                <h3 class="font-bold text-sm text-on-surface mb-6">Detail Administrasi Akun</h3>
                
                <div class="flex flex-col md:flex-row items-center gap-6 mb-6">
                    <div class="relative group">
                        @php
                            $photoUrl = session('user_photo');
                            if (!$photoUrl && isset($user) && $user->employee && $user->employee->foto) {
                                $photoUrl = asset('storage/' . $user->employee->foto);
                            }
                        @endphp
                        @if($photoUrl)
                        <div class="w-24 h-24 rounded-full shadow-sm border border-outline-variant overflow-hidden">
                            <img src="{{ $photoUrl }}" alt="Profile Photo" class="w-full h-full object-cover">
                        </div>
                        @else
                        <div class="w-24 h-24 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-3xl shadow-sm border border-outline-variant uppercase">
                            {{ substr($user->employee->nama_lengkap ?? $user->username, 0, 2) }}
                        </div>
                        @endif
                        <form action="{{ route('backoffice.pengaturan.upload.photo') }}" method="POST" enctype="multipart/form-data" id="photo-upload-form" class="absolute inset-0">
                            @csrf
                            <input type="file" name="photo" id="photo-input" accept="image/*" class="sr-only" onchange="handlePhotoSelect(this)">
                            <label for="photo-input" class="absolute bottom-0 right-0 bg-primary text-white p-2 rounded-full shadow-lg hover:scale-110 active:scale-95 transition-all cursor-pointer flex items-center justify-center m-0">
                                <span id="photo-camera-icon" class="material-symbols-outlined text-sm">photo_camera</span>
                            </label>
                        </form>
                    </div>
                    
                    <button type="submit" form="photo-upload-form" id="save-photo-btn" class="hidden mt-2 px-3 py-1 bg-success text-white text-[10px] font-bold rounded-full shadow-sm hover:brightness-110 flex items-center justify-center gap-1 active:scale-95">
                        <span class="material-symbols-outlined text-[12px]">check</span> Simpan Foto
                    </button>
                    
                    <div class="flex-1 grid grid-cols-1 gap-4 w-full mt-4 md:mt-0">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Nama Lengkap</label>
                            <input class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-low text-on-surface-variant outline-none" disabled type="text" value="{{ $user->employee->nama_lengkap ?? $user->username }}">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Alamat Email</label>
                            <input class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-low text-on-surface-variant outline-none" disabled type="text" value="{{ $user->employee->email ?? $user->email }}">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Jabatan</label>
                            <input class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-low text-on-surface-variant outline-none" disabled type="text" value="{{ $user->employee->position->nama ?? ($user->role == 'super_admin' ? 'Administrator' : 'Manajer HRD') }}">
                        </div>
                    </div>
                </div>

                <!-- Bagian Data Pribadi & CV dengan Rich Text Editor -->
                <div class="border-t border-outline-variant pt-6 mt-2">
                    <div class="mb-4">
                        <h4 class="font-bold text-sm text-on-surface">Data Pribadi & Riwayat Hidup (CV)</h4>
                        <p class="text-[11px] text-on-surface-variant font-medium mt-1">
                            Tuliskan ringkasan profesional Anda. Anda dapat menyeret (*drag & drop*) foto atau memasukkan *link* URL dokumen pribadi (KTP/NPWP/Kontrak).
                        </p>
                    </div>
                    
                    <!-- Editor Container -->
                    <div class="rounded-xl border border-outline-variant overflow-hidden hover:border-primary/50 transition-colors focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20">
                        <div id="cv-editor" style="height: 300px; font-family: 'Inter', sans-serif;">
                            <h2>Ringkasan Profesional</h2>
                            <p>Saya adalah seorang profesional HR yang berpengalaman...</p>
                        </div>
                    </div>
                    
                    <!-- Lampiran Dokumen Pribadi (Downloadable) -->
                    <div class="mt-6">
                        <h5 class="font-bold text-xs text-on-surface mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-primary">attachment</span>
                            Dokumen Pribadi Terlampir
                        </h5>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            @if(isset($user->employee) && $user->employee->documents->count() > 0)
                                @foreach($user->employee->documents as $doc)
                                <div class="flex items-center justify-between p-3 border border-outline-variant rounded-lg bg-surface-container-lowest hover:border-primary/50 transition-all group">
                                    <div class="flex items-center gap-3">
                                        @if($doc->file_type == 'pdf')
                                        <div class="p-2 bg-error/10 text-error rounded-md">
                                            <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                                        </div>
                                        @elseif(in_array($doc->file_type, ['jpg', 'jpeg', 'png']))
                                        <div class="p-2 bg-success/10 text-success rounded-md">
                                            <span class="material-symbols-outlined text-sm">image</span>
                                        </div>
                                        @else
                                        <div class="p-2 bg-tertiary/10 text-tertiary rounded-md">
                                            <span class="material-symbols-outlined text-sm">description</span>
                                        </div>
                                        @endif
                                        <div class="overflow-hidden">
                                            <p class="font-bold text-[10px] text-on-surface truncate w-32" title="{{ $doc->file_name }}">{{ $doc->file_name }}</p>
                                            <p class="text-[9px] text-on-surface-variant">{{ $doc->file_size < 1024 ? $doc->file_size . ' KB' : round($doc->file_size / 1024, 1) . ' MB' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="w-8 h-8 flex-shrink-0 flex items-center justify-center rounded-full bg-surface-container hover:bg-primary hover:text-white transition-colors cursor-pointer text-on-surface-variant group-hover:text-primary" title="Download">
                                            <span class="material-symbols-outlined text-[16px]">download</span>
                                        </a>
                                        <form action="{{ route('backoffice.pengaturan.delete.document', $doc->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 flex-shrink-0 flex items-center justify-center rounded-full bg-surface-container hover:bg-error hover:text-white transition-colors cursor-pointer text-on-surface-variant group-hover:text-error" title="Hapus">
                                                <span class="material-symbols-outlined text-[16px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="col-span-full p-4 border border-dashed border-outline-variant rounded-lg text-center text-on-surface-variant text-xs font-medium">
                                    Belum ada dokumen yang diunggah.
                                </div>
                            @endif
                        </div>
                        
                        <form action="{{ route('backoffice.pengaturan.upload.document') }}" method="POST" enctype="multipart/form-data" id="document-upload-form" class="mt-4 p-5 border border-outline-variant rounded-xl bg-surface-container-low flex flex-col items-center justify-center border-dashed text-center">
                            @csrf
                            <div class="w-12 h-12 bg-primary/10 text-primary rounded-full flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-2xl">upload_file</span>
                            </div>
                            <h6 class="text-sm font-bold text-on-surface mb-1">Upload Dokumen Baru</h6>
                            <p class="text-[10px] text-on-surface-variant mb-4">Pilih file dari perangkat Anda, lalu klik Simpan.</p>
                            
                            <input type="file" name="document[]" id="document-input" accept=".pdf,.jpg,.jpeg,.png" multiple class="sr-only" onchange="handleDocSelect(this)">
                            
                            <div class="flex flex-col sm:flex-row items-center gap-3 w-full justify-center">
                                <label for="document-input" class="px-6 py-2.5 bg-surface-container border border-outline-variant text-on-surface text-xs font-bold rounded-lg hover:bg-surface-container-high transition-all flex items-center justify-center gap-2 cursor-pointer shadow-sm active:scale-95">
                                    <span class="material-symbols-outlined text-[16px]">folder_open</span>
                                    <span id="upload-btn-text" class="max-w-[150px] truncate">Pilih File...</span>
                                </label>
                                
                                <button type="submit" id="submit-doc-btn" class="hidden px-6 py-2.5 bg-primary text-white text-xs font-bold rounded-lg hover:brightness-110 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-sm active:scale-95">
                                    <span class="material-symbols-outlined text-[16px]">cloud_upload</span>
                                    Mulai Upload
                                </button>
                            </div>
                            
                            <p class="text-[9px] text-on-surface-variant mt-4 font-medium px-4 py-1.5 bg-white rounded-full border border-outline-variant">Format didukung: PDF, JPG, PNG (Maks. 5 MB)</p>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel 2: Notifikasi (Hidden by default) -->
        <div class="settings-panel space-y-6 hidden" id="panel-notifikasi">
            <div class="bg-white border border-outline-variant rounded-xl p-6 shadow-sm">
                <h3 class="font-bold text-sm text-on-surface mb-6">Preferensi Pemberitahuan</h3>
                <div class="space-y-4">
                    <!-- Item 1 -->
                    <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-lg gap-4">
                        <div class="flex gap-4 items-center">
                            <span class="material-symbols-outlined text-primary text-xl">event_available</span>
                            <div>
                                <p class="font-bold text-xs text-on-surface">Laporan Absensi</p>
                                <p class="text-[10px] text-on-surface-variant font-medium">Terima rekap harian absensi karyawan.</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input checked class="sr-only peer" type="checkbox">
                            <div class="w-9 h-5 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                    <!-- Item 2 -->
                    <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-lg gap-4">
                        <div class="flex gap-4 items-center">
                            <span class="material-symbols-outlined text-primary text-xl">account_balance_wallet</span>
                            <div>
                                <p class="font-bold text-xs text-on-surface">Peringatan Penggajian</p>
                                <p class="text-[10px] text-on-surface-variant font-medium">Notifikasi saat periode payroll dimulai.</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input checked class="sr-only peer" type="checkbox">
                            <div class="w-9 h-5 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                    <!-- Item 3 -->
                    <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-lg gap-4">
                        <div class="flex gap-4 items-center">
                            <span class="material-symbols-outlined text-primary text-xl">time_to_leave</span>
                            <div>
                                <p class="font-bold text-xs text-on-surface">Permohonan Cuti</p>
                                <p class="text-[10px] text-on-surface-variant font-medium">Pemberitahuan real-time untuk pengajuan cuti.</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input class="sr-only peer" type="checkbox">
                            <div class="w-9 h-5 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        @if(session('user_role') !== 'employee')
        <!-- Panel 3: Konfigurasi HR (Hidden by default) -->
        <div class="settings-panel space-y-6 hidden" id="panel-konfigurasi">
            <div class="bg-white border border-outline-variant rounded-xl p-6 shadow-sm">
                <h3 class="font-bold text-sm text-on-surface mb-6">Konfigurasi Perusahaan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Tahun Fiskal Aktif</label>
                            <select class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-white focus:border-primary outline-none text-xs font-medium cursor-pointer">
                                <option>2024</option>
                                <option>2025</option>
                                <option selected>2026</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Mata Uang Utama</label>
                            <input class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-white focus:border-primary outline-none text-xs font-medium" type="text" value="IDR - Indonesian Rupiah">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Jam Kerja Perusahaan</label>
                            <div class="flex items-center gap-2">
                                <input class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-white focus:border-primary outline-none text-xs font-medium" type="time" value="08:00">
                                <span class="text-on-surface-variant text-xs font-bold">Ke</span>
                                <input class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-white focus:border-primary outline-none text-xs font-medium" type="time" value="17:00">
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Format Tanggal</label>
                            <select class="w-full px-4 py-2 rounded-lg border border-outline-variant bg-white focus:border-primary outline-none text-xs font-medium cursor-pointer">
                                <option>DD/MM/YYYY</option>
                                <option>MM/DD/YYYY</option>
                                <option>YYYY-MM-DD</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Panel 4: Keamanan (Hidden by default) -->
        <div class="settings-panel space-y-6 hidden" id="panel-keamanan">
            <div class="bg-white border border-outline-variant rounded-xl p-6 shadow-sm">
                <h3 class="font-bold text-sm text-on-surface mb-6">Otentikasi Dua Faktor (2FA)</h3>
                <div class="flex items-start gap-4 p-4 border-2 border-dashed border-outline-variant rounded-xl mb-6 bg-surface-container-low">
                    <div class="bg-primary/10 p-3 rounded-lg text-primary">
                        <span class="material-symbols-outlined text-3xl">vibration</span>
                    </div>
                    <div>
                        <p class="font-bold text-xs text-on-surface">Tingkatkan Keamanan Akun Anda</p>
                        <p class="text-[10px] text-on-surface-variant mb-3 font-medium">Gunakan aplikasi autentikator untuk mendapatkan kode verifikasi saat login.</p>
                        <button class="px-4 py-2 bg-primary text-white rounded-lg font-bold text-xs hover:brightness-110 active:scale-95 transition-all cursor-pointer" onclick="alert('Mengaktifkan verifikasi Dua Faktor...')">Aktifkan 2FA</button>
                    </div>
                </div>
                
                <h4 class="font-bold text-xs text-on-surface mb-4">Riwayat Login Terakhir</h4>
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-surface-container-low text-on-surface-variant font-bold text-[10px] border-b border-outline-variant">
                            <tr>
                                <th class="px-4 py-2">Perangkat</th>
                                <th class="px-4 py-2">Lokasi</th>
                                <th class="px-4 py-2">Waktu</th>
                                <th class="px-4 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
                            <tr>
                                <td class="px-4 py-3 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-on-surface-variant text-lg">laptop_mac</span>
                                    <span class="font-bold text-on-surface">MacBook Pro (Chrome)</span>
                                </td>
                                <td class="px-4 py-3 text-on-surface-variant">Jakarta, ID</td>
                                <td class="px-4 py-3 text-on-surface-variant font-mono text-xs">Hari ini, 09:42</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-0.5 bg-tertiary-fixed text-on-tertiary-fixed text-[9px] rounded-full uppercase font-bold tracking-wider">Aktif Sekarang</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-on-surface-variant text-lg">smartphone</span>
                                    <span class="font-bold text-on-surface">iPhone 14 Pro</span>
                                </td>
                                <td class="px-4 py-3 text-on-surface-variant">Bandung, ID</td>
                                <td class="px-4 py-3 text-on-surface-variant font-mono text-xs">12 Jan 2026, 20:15</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-0.5 bg-outline-variant text-on-surface-variant text-[9px] rounded-full uppercase font-bold tracking-wider">Berhasil</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-outline-variant">
            <button class="px-5 py-2.5 rounded-lg border border-outline-variant text-secondary font-bold text-xs hover:bg-surface-container-low transition-all cursor-pointer active:scale-95 bg-white shadow-sm btn-ripple" onclick="location.reload()">Batalkan</button>
            <button class="px-5 py-2.5 rounded-lg bg-primary text-white font-bold text-xs hover:brightness-110 transition-all flex items-center gap-2 shadow cursor-pointer active:scale-95 btn-ripple" id="btn-save-settings" onclick="savePulse(this)">
                <span>Simpan Perubahan</span>
                <span class="material-symbols-outlined text-sm">check_circle</span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Include Quill stylesheet -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<!-- Include the Quill library -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<style>
    /* 
     * PERBAIKAN BUG QUILL.JS vs TAILWIND CSS
     * Tailwind Preflight kadang membuat kursor/caret menghilang di dalam span contenteditable.
     * Solusi standar: memaksa span menjadi inline-block.
     */
    .ql-editor {
        background-color: #ffffff; /* Kembali ke putih bersih yang elegan */
        font-family: 'Inter', sans-serif;
        min-height: 250px;
        font-size: 15px;
        /* Custom Mouse Pointer yang sangat tebal (menggantikan kursor I-beam bawaan OS yang tipis) */
        cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 20 24"><path d="M5 2h10v3h-3.5v14H15v3H5v-3h3.5V5H5V2z" fill="%230050cb" stroke="%23ffffff" stroke-width="1"/></svg>') 10 12, auto !important;
    }
    .ql-editor * {
        cursor: inherit !important;
    }
    .ql-editor span {
        display: inline-block;
        min-width: 1px;
    }
    .ql-editor p {
        margin-bottom: 0.5rem;
    }
    .ql-editor.ql-blank::before {
        color: #94a3b8;
        font-style: italic;
    }
    .ql-toolbar.ql-snow {
        background-color: #f8fafc;
        border: 1px solid #cbd5e1 !important;
        border-bottom: none !important;
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
    }
    .ql-container.ql-snow {
        border: 1px solid #cbd5e1 !important;
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
    }
</style>

<script>
    // ==========================================
    // 0. Inisialisasi Quill Editor (CV & Data Pribadi)
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        if(document.getElementById('cv-editor')) {
            var quill = new Quill('#cv-editor', {
                theme: 'snow',
                placeholder: 'Tuliskan riwayat hidup dan letakkan link dokumen Anda di sini...',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
            });
            
            // Kustomisasi style toolbar bawaan Quill agar match dengan Tailwind
            document.querySelector('.ql-toolbar').classList.add('bg-surface-container-low', 'border-b', 'border-outline-variant', 'border-0');
            document.querySelector('.ql-container').classList.add('text-body-md', 'border-0');
        }
    });

    // ==========================================
    // Fungsi Handle Pemilihan File (UX)
    // ==========================================
    function handlePhotoSelect(input) {
        if (input.files && input.files[0]) {
            // Tampilkan tombol simpan
            document.getElementById('save-photo-btn').classList.remove('hidden');
            // Ubah warna ikon kamera
            document.getElementById('photo-camera-icon').classList.add('text-success');
        }
    }

    function handleDocSelect(input) {
        if (input.files && input.files.length > 0) {
            // Tampilkan nama file atau jumlah file
            if (input.files.length === 1) {
                document.getElementById('upload-btn-text').innerText = input.files[0].name;
            } else {
                document.getElementById('upload-btn-text').innerText = input.files.length + " file terpilih";
            }
            // Munculkan tombol submit
            document.getElementById('submit-doc-btn').classList.remove('hidden');
        } else {
            document.getElementById('upload-btn-text').innerText = "Pilih File...";
            document.getElementById('submit-doc-btn').classList.add('hidden');
        }
    }

    // ==========================================
    // 1. Logika Perpindahan Tab Panel
    // ==========================================
    function switchTab(tabId) {
        // Sembunyikan seluruh panel pengaturan
        document.querySelectorAll('.settings-panel').forEach(panel => {
            panel.classList.add('hidden');
            panel.classList.remove('animate-page-in'); // Reset animasi
        });
        
        // Tampilkan panel yang dituju dengan animasi masuk
        const targetPanel = document.getElementById('panel-' + tabId);
        targetPanel.classList.remove('hidden');
        
        // Memaksa browser me-render ulang (reflow) agar animasi diulang dari awal
        void targetPanel.offsetWidth; 
        targetPanel.classList.add('animate-page-in');

        // Reset semua gaya tombol tab
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.className = "tab-btn flex-1 lg:flex-none flex items-center gap-3 p-4 rounded-xl border border-transparent bg-transparent text-left hover:bg-surface-container-low transition-all cursor-pointer";
            
            // Ubah icon warna ke default
            const icon = btn.querySelector('.material-symbols-outlined');
            icon.className = "material-symbols-outlined p-2 rounded-lg text-on-surface-variant bg-surface-container-high";
        });

        // Set style aktif untuk tab yang diklik
        const activeBtn = document.getElementById('tab-' + tabId);
        activeBtn.className = "tab-btn flex-1 lg:flex-none flex items-center gap-3 p-4 rounded-xl border border-primary bg-white text-left shadow-sm transition-all cursor-pointer";
        
        const activeIcon = activeBtn.querySelector('.material-symbols-outlined');
        activeIcon.className = "material-symbols-outlined p-2 rounded-lg text-primary bg-primary/10";
    }

    // ==========================================
    // 2. Animasi Denyut Simpan Perubahan (Save pulse)
    // ==========================================
    function savePulse(btn) {
        const originalContent = btn.innerHTML;
        btn.disabled = true;
        btn.classList.add('opacity-80', 'cursor-not-allowed');
        btn.innerHTML = `
            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Menyimpan...</span>
        `;
        
        setTimeout(() => {
            btn.innerHTML = `
                <span>Berhasil Disimpan</span>
                <span class="material-symbols-outlined text-sm">done_all</span>
            `;
            btn.className = "px-5 py-2.5 rounded-lg bg-tertiary text-white font-bold text-xs flex items-center gap-2 shadow cursor-pointer";
            
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.className = "px-5 py-2.5 rounded-lg bg-primary text-white font-bold text-xs hover:brightness-110 transition-all flex items-center gap-2 shadow cursor-pointer active:scale-95";
                btn.disabled = false;
                btn.classList.remove('opacity-80', 'cursor-not-allowed');
            }, 2000);
        }, 1500);
    }
</script>
@endpush
