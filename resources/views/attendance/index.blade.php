@extends('layouts.admin')

@section('title', 'Absensi Kehadiran - HRDApps')
@section('page_title', 'Absensi Kehadiran')

@section('content')
@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor {
        min-height: 120px;
        background-color: white;
    }
    .ql-toolbar {
        background-color: #f8fafc;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }
    .ql-container {
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
    }
</style>
@endpush
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 animate-stagger">
    
    <!-- KOLOM KIRI: Form Input & Informasi -->
    <div class="flex flex-col gap-4 sm:gap-6 order-2 lg:order-1">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-4 sm:p-6 card-shadow">
            <h4 class="font-title-sm text-title-sm text-on-background font-bold mb-4">Form Kehadiran</h4>
            
            @if(session('success'))
                <div class="bg-primary/20 text-primary p-3 rounded-xl mb-4 text-xs sm:text-sm font-semibold">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-error/20 text-error p-3 rounded-xl mb-4 text-xs sm:text-sm font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            @if($attendance && in_array($attendance->status_kehadiran, ['cuti', 'izin', 'sakit']))
                <!-- Status Cuti / Izin / Sakit Terverifikasi Hari Ini (Absen Terkunci) -->
                <div class="text-center p-6 sm:p-8 bg-emerald-50 border border-emerald-200 rounded-2xl">
                    <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3 shadow-inner">
                        <span class="material-symbols-outlined text-4xl">event_available</span>
                    </div>
                    <span class="inline-block px-3 py-1 bg-emerald-600 text-white rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                        Status Hari Ini: {{ strtoupper($attendance->status_kehadiran) }} (DISETUJUI)
                    </span>
                    <h3 class="text-base sm:text-lg font-bold text-slate-800">Presensi Hari Ini Telah Terjadwal</h3>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-md mx-auto">
                        Pengajuan {{ ucfirst($attendance->status_kehadiran) }} Anda telah disetujui resmi oleh manajemen. Anda tidak perlu melakukan presensi Clock In / Clock Out.
                    </p>
                    @if($attendance->keterangan)
                        <div class="mt-4 p-3 bg-white border border-emerald-200 rounded-xl text-xs text-slate-700 max-w-md mx-auto text-left">
                            <strong>Keterangan:</strong> {{ strip_tags($attendance->keterangan) }}
                        </div>
                    @endif
                </div>
            @elseif(!$attendance || !$attendance->jam_masuk)
                <!-- Form Clock In -->
                <form action="{{ route('attendance.clock_in') }}" method="POST" id="form-clockin">
                    @csrf
                    <input type="hidden" name="foto" id="foto-in">
                    <input type="hidden" name="lokasi" id="lokasi-in">
                    
                    <div class="mb-4">
                        <label class="block text-xs sm:text-sm font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider">Status Kerja</label>
                        <select name="status_kerja" class="w-full p-3 border border-outline-variant rounded-xl bg-surface text-on-surface font-medium text-xs sm:text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
                            <option value="WFO">WFO - Work From Office (Kantor)</option>
                            <option value="WFD">WFD - Work From Destination (Penugasan Luar)</option>
                            <option value="WFH">WFH - Work From Home</option>
                            <option value="WFF">WFF - Work From Field</option>
                            <option value="WOD">WOD - Work On Duty</option>
                            <option value="WEH">WEH - Work Extra Hours</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs sm:text-sm font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider">Laporan Singkat / Rencana Kerja <span class="text-outline text-xs lowercase font-normal">(opsional)</span></label>
                        <div class="bg-surface rounded-xl overflow-hidden border border-outline-variant focus-within:border-primary transition-all">
                            <div id="editor-in"></div>
                        </div>
                        <input type="hidden" name="keterangan" id="keterangan-in">
                    </div>

                    <button type="button" id="btn-submit-in" class="w-full bg-primary hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-xl shadow hover:shadow-md active:scale-98 transition-all cursor-pointer flex items-center justify-center gap-2 text-sm sm:text-base">
                        <span class="material-symbols-outlined">send</span>
                        <span id="btn-submit-in-text">Kirim Absensi Masuk (Clock In)</span>
                    </button>
                </form>
            @elseif(!$attendance->jam_keluar)
                <!-- Form Clock Out -->
                <form action="{{ route('attendance.clock_out') }}" method="POST" id="form-clockout">
                    @csrf
                    <input type="hidden" name="foto" id="foto-out">
                    <input type="hidden" name="lokasi" id="lokasi-out">

                    <div class="mb-4 p-3.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Clock In Pukul</p>
                            <p class="text-base font-bold text-primary font-mono mt-0.5">{{ substr($attendance->jam_masuk, 0, 5) }} WIB</p>
                        </div>
                        <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-lg uppercase tracking-wider">
                            {{ $attendance->status_kerja }}
                        </span>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-xs sm:text-sm font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                            Update Laporan Harian <span class="text-error font-bold">*</span> <span class="text-xs font-semibold text-error lowercase">(wajib diisi)</span>
                        </label>
                        <div class="bg-surface rounded-xl overflow-hidden border border-outline-variant focus-within:border-primary transition-colors">
                            <div id="editor-out">{!! $attendance->keterangan ?? '' !!}</div>
                        </div>
                        <p class="text-[11px] text-on-surface-variant mt-1.5 leading-relaxed">Tuliskan ringkasan hasil kerja atau aktivitas yang telah Anda selesaikan hari ini.</p>
                        <input type="hidden" name="keterangan" id="keterangan-out">
                    </div>

                    <button type="button" id="btn-submit-out" class="w-full border border-error text-error bg-error/10 font-bold py-3.5 px-4 rounded-xl shadow-md hover:bg-error hover:text-white active:scale-98 transition-all cursor-pointer flex items-center justify-center gap-2 text-sm sm:text-base">
                        <span class="material-symbols-outlined">logout</span>
                        <span id="btn-submit-out-text">Kirim Absensi Keluar (Clock Out)</span>
                    </button>
                </form>
            @else
                <!-- Selesai Absen -->
                <div class="text-center p-6 sm:p-8 bg-primary/5 border border-primary/20 rounded-2xl">
                    <div class="w-16 h-16 rounded-full bg-primary/10 text-primary flex items-center justify-center mx-auto mb-3 shadow-inner">
                        <span class="material-symbols-outlined text-4xl">check_circle</span>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-on-background">Absensi Hari Ini Selesai</h3>
                    <p class="text-xs sm:text-sm text-on-surface-variant mt-1">Total Durasi Kerja: <strong class="text-primary font-mono">{{ $attendance->total_jam_kerja }} Jam</strong></p>
                </div>
            @endif
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-4 sm:p-6 card-shadow">
            <h4 class="font-title-sm text-title-sm text-on-background font-bold mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-lg">info</span>
                Panduan Presensi
            </h4>
            <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl">
                <ul class="list-disc pl-4 space-y-1.5 text-xs text-slate-600 leading-relaxed">
                    <li>Izinkan akses kamera dan sensor lokasi GPS browser Anda.</li>
                    <li>Ambil foto selfie di area yang cukup terang.</li>
                    <li>Klik <strong>Clock In</strong> saat memulai hari kerja.</li>
                    <li>Isi laporan aktivitas kerja sebelum menekan tombol <strong>Clock Out</strong> saat jam pulang.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: Kamera & Peta GPS (Mobile: Prioritas di atas) -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-4 sm:p-6 card-shadow h-fit order-1 lg:order-2">
        <div class="flex items-center justify-between mb-4 gap-2 flex-wrap">
            <h4 class="font-title-sm text-title-sm text-on-background font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">photo_camera</span>
                Bukti Foto & Lokasi
            </h4>
            <div class="flex items-center gap-2">
                <button type="button" id="btn-toggle-mirror" class="text-xs px-2.5 py-1 rounded-full font-semibold bg-surface-container-low hover:bg-surface-container border border-outline-variant text-on-surface flex items-center gap-1 transition cursor-pointer" title="Klik untuk membalik orientasi cermin kamera">
                    <span class="material-symbols-outlined text-[15px]">flip</span>
                    <span id="mirror-label">Mirror: Nonaktif</span>
                </button>
                <span id="camera-status-badge" class="text-xs px-2.5 py-1 rounded-full font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                    Kamera Siap
                </span>
            </div>
        </div>
        
        <div class="mb-4 relative group overflow-hidden rounded-xl border border-outline-variant bg-black">
            <!-- Placeholder (Dashed Box) -->
            <div id="camera-placeholder" class="w-full h-64 sm:h-72 border-2 border-dashed border-outline-variant rounded-xl flex flex-col items-center justify-center text-on-surface-variant cursor-pointer bg-surface-container-low hover:bg-surface-container hover:border-primary transition-all p-4 text-center">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-primary/10 text-primary flex items-center justify-center mb-3 shadow-inner group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl sm:text-3xl">add_a_photo</span>
                </div>
                <span class="font-bold text-sm sm:text-base text-on-surface">Buka Kamera Selfie</span>
                <span class="text-xs text-on-surface-variant mt-1 max-w-[240px]">Ketuk di sini untuk menyalakan kamera depan dan ambil foto langsung</span>
            </div>

            <!-- Video Live Feed -->
            <div id="video-wrapper" class="relative hidden w-full h-64 sm:h-72 bg-black flex items-center justify-center">
                <video id="camera-feed" class="w-full h-full object-cover rounded-xl" autoplay playsinline></video>
            </div>

            <!-- Photo Preview -->
            <div id="preview-wrapper" class="relative hidden w-full h-64 sm:h-72 bg-black flex items-center justify-center">
                <img id="photo-preview" class="w-full h-full object-cover rounded-xl" alt="Preview Absensi" />
            </div>
            
            <canvas id="camera-canvas" class="hidden"></canvas>
        </div>

        <!-- Tombol Aksi Kamera -->
        <div id="camera-action-bar" class="mb-4 space-y-2">
            @if(!$attendance || !$attendance->jam_masuk)
                <!-- Tombol Kamera saat Clock In -->
                <button type="button" id="btn-cam-takephoto-in" class="w-full bg-slate-800 text-white font-bold py-3 px-4 rounded-xl shadow hover:bg-slate-700 active:scale-98 transition flex items-center justify-center gap-2 cursor-pointer text-xs sm:text-sm">
                    <span class="material-symbols-outlined text-lg sm:text-xl" id="btn-cam-icon-in">photo_camera</span>
                    <span id="btn-cam-text-in">Buka Kamera Selfie</span>
                </button>
            @elseif(!$attendance->jam_keluar)
                <!-- Tombol Kamera saat Clock Out -->
                <button type="button" id="btn-cam-takephoto-out" class="w-full bg-slate-800 text-white font-bold py-3 px-4 rounded-xl shadow hover:bg-slate-700 active:scale-98 transition flex items-center justify-center gap-2 cursor-pointer text-xs sm:text-sm">
                    <span class="material-symbols-outlined text-lg sm:text-xl" id="btn-cam-icon-out">photo_camera</span>
                    <span id="btn-cam-text-out">Buka Kamera Selfie</span>
                </button>
            @endif

            <button type="button" id="btn-retake-photo" class="w-full py-2.5 px-4 bg-slate-100 border border-slate-300 text-red-600 font-bold rounded-xl hover:bg-red-50 transition items-center justify-center gap-2 hidden cursor-pointer text-xs sm:text-sm">
                <span class="material-symbols-outlined text-base">refresh</span>
                <span>Foto Ulang (Retake)</span>
            </button>
        </div>

        <div class="border-t border-outline-variant/60 pt-4">
            <div id="map-container" class="w-full h-32 sm:h-36 bg-slate-100 rounded-xl overflow-hidden hidden mb-2 border border-outline-variant">
                <iframe id="map-iframe" width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src=""></iframe>
            </div>
            <div class="flex items-start gap-2 mt-2">
                <span class="material-symbols-outlined text-primary text-base mt-0.5 shrink-0">location_on</span>
                <div class="min-w-0">
                    <p id="location-text" class="text-[11px] sm:text-xs text-on-surface font-mono font-bold truncate">Mendapatkan lokasi GPS...</p>
                    <p id="address-text" class="text-[11px] sm:text-xs text-on-surface-variant mt-0.5 leading-relaxed break-words"></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    // Inisialisasi Quill Editor
    const quillOptions = {
        theme: 'snow',
        placeholder: 'Tuliskan laporan harian Anda di sini...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['clean']
            ]
        }
    };
    
    const editorIn = document.getElementById('editor-in') ? new Quill('#editor-in', quillOptions) : null;
    const editorOut = document.getElementById('editor-out') ? new Quill('#editor-out', quillOptions) : null;

    const cameraPlaceholder = document.getElementById('camera-placeholder');
    const videoWrapper = document.getElementById('video-wrapper');
    const previewWrapper = document.getElementById('preview-wrapper');
    const video = document.getElementById('camera-feed');
    const photoPreview = document.getElementById('photo-preview');
    const canvas = document.getElementById('camera-canvas');
    const btnShutter = document.getElementById('btn-shutter');
    const btnRetakePhoto = document.getElementById('btn-retake-photo');
    const cameraBadge = document.getElementById('camera-status-badge');
    
    const btnCamTakeIn = document.getElementById('btn-cam-takephoto-in');
    const btnCamTextIn = document.getElementById('btn-cam-text-in');
    const btnCamIconIn = document.getElementById('btn-cam-icon-in');
    
    const btnCamTakeOut = document.getElementById('btn-cam-takephoto-out');
    const btnCamTextOut = document.getElementById('btn-cam-text-out');
    const btnCamIconOut = document.getElementById('btn-cam-icon-out');
    
    const btnSubmitIn = document.getElementById('btn-submit-in');
    const btnSubmitInText = document.getElementById('btn-submit-in-text');
    const btnSubmitOut = document.getElementById('btn-submit-out');
    const btnSubmitOutText = document.getElementById('btn-submit-out-text');

    const locationText = document.getElementById('location-text');
    const addressText = document.getElementById('address-text');
    const mapContainer = document.getElementById('map-container');
    const mapIframe = document.getElementById('map-iframe');
    
    let currentLocation = '';
    let isPhotoTaken = false;
    let photoData = null;
    let streamActive = null;
    let isMirror = false; // Default non-mirror sesuai permintaan

    const btnToggleMirror = document.getElementById('btn-toggle-mirror');
    const mirrorLabel = document.getElementById('mirror-label');

    const updateMirrorUI = () => {
        if (isMirror) {
            video.style.transform = 'scaleX(-1)';
            video.style.webkitTransform = 'scaleX(-1)';
            if (mirrorLabel) mirrorLabel.innerText = 'Mirror: Aktif';
            if (btnToggleMirror) {
                btnToggleMirror.classList.add('bg-primary/10', 'text-primary', 'border-primary/30');
                btnToggleMirror.classList.remove('bg-surface-container-low');
            }
        } else {
            video.style.transform = 'none';
            video.style.webkitTransform = 'none';
            if (mirrorLabel) mirrorLabel.innerText = 'Mirror: Nonaktif';
            if (btnToggleMirror) {
                btnToggleMirror.classList.remove('bg-primary/10', 'text-primary', 'border-primary/30');
                btnToggleMirror.classList.add('bg-surface-container-low');
            }
        }
    };

    if (btnToggleMirror) {
        btnToggleMirror.addEventListener('click', () => {
            isMirror = !isMirror;
            updateMirrorUI();
        });
    }

    // Fungsi Mulai Kamera (Mode Depan / Selfie)
    const startCamera = () => {
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            cameraBadge.innerText = 'Menyalakan Kamera...';
            navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            })
            .then(function(stream) {
                streamActive = stream;
                video.srcObject = stream;
                updateMirrorUI();
                cameraPlaceholder.classList.add('hidden');
                videoWrapper.classList.remove('hidden');
                previewWrapper.classList.add('hidden');
                cameraBadge.innerText = 'Kamera Aktif';
                cameraBadge.className = 'text-xs px-2.5 py-1 rounded-full font-semibold bg-green-100 text-green-700 border border-green-200';
                
                if (btnCamTextIn) {
                    btnCamTextIn.innerText = "Jepret Foto Selfie";
                    if (btnCamIconIn) btnCamIconIn.innerText = "camera";
                }
                if (btnCamTextOut) {
                    btnCamTextOut.innerText = "Jepret Foto Selfie";
                    if (btnCamIconOut) btnCamIconOut.innerText = "camera";
                }
            })
            .catch(function(err) {
                console.error("Camera error:", err);
                cameraBadge.innerText = 'Kamera Gagal';
                cameraBadge.className = 'text-xs px-2.5 py-1 rounded-full font-semibold bg-red-100 text-red-700 border border-red-200';
                alert("Tidak dapat mengakses kamera. Pastikan Anda telah memberikan izin kamera pada browser.");
            });
        }
    };

    // Jepret Foto dari Video Stream
    const takePhoto = () => {
        if (!streamActive) {
            startCamera();
            return;
        }

        const MAX_WIDTH = 640;
        let width = video.videoWidth || 640;
        let height = video.videoHeight || 480;

        if (width > MAX_WIDTH) {
            height = Math.round((height * MAX_WIDTH) / width);
            width = MAX_WIDTH;
        }

        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');
        
        if (isMirror) {
            ctx.save();
            ctx.translate(width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0, width, height);
            ctx.restore();
        } else {
            ctx.drawImage(video, 0, 0, width, height);
        }
        
        photoData = canvas.toDataURL('image/jpeg', 0.6);
        photoPreview.src = photoData;
        
        videoWrapper.classList.add('hidden');
        previewWrapper.classList.remove('hidden');
        btnRetakePhoto.classList.remove('hidden');
        btnRetakePhoto.classList.add('flex');
        
        isPhotoTaken = true;
        cameraBadge.innerText = 'Foto Siap Dikirim';
        cameraBadge.className = 'text-xs px-2.5 py-1 rounded-full font-semibold bg-blue-100 text-blue-700 border border-blue-200';

        // Sembunyikan tombol jepret foto agar user tidak bingung
        if (btnCamTakeIn) btnCamTakeIn.classList.add('hidden');
        if (btnCamTakeOut) btnCamTakeOut.classList.add('hidden');
    };

    // Tombol Ulangi Foto (Retake)
    const resetPhoto = () => {
        previewWrapper.classList.add('hidden');
        videoWrapper.classList.remove('hidden');
        btnRetakePhoto.classList.add('hidden');
        btnRetakePhoto.classList.remove('flex');
        isPhotoTaken = false;
        photoData = null;
        cameraBadge.innerText = 'Kamera Aktif';
        cameraBadge.className = 'text-xs px-2.5 py-1 rounded-full font-semibold bg-green-100 text-green-700 border border-green-200';

        // Tampilkan kembali tombol jepret foto
        if (btnCamTakeIn) {
            btnCamTakeIn.classList.remove('hidden');
            btnCamTextIn.innerText = "Jepret Foto Selfie";
            if (btnCamIconIn) btnCamIconIn.innerText = "camera";
        }

        if (btnCamTakeOut) {
            btnCamTakeOut.classList.remove('hidden');
            btnCamTextOut.innerText = "Jepret Foto Selfie";
            if (btnCamIconOut) btnCamIconOut.innerText = "camera";
        }
    };

    // Event Trigger Buka Kamera & Shutter
    if (cameraPlaceholder) cameraPlaceholder.addEventListener('click', startCamera);
    if (btnShutter) btnShutter.addEventListener('click', takePhoto);
    if (btnRetakePhoto) btnRetakePhoto.addEventListener('click', resetPhoto);

    // Click handler tombol di bawah kamera (Clock In)
    if (btnCamTakeIn) {
        btnCamTakeIn.addEventListener('click', () => {
            if (!streamActive && !isPhotoTaken) {
                startCamera();
            } else if (!isPhotoTaken) {
                takePhoto();
            } else {
                const formClockIn = document.getElementById('form-clockin');
                if (formClockIn) {
                    formClockIn.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    }

    // Click handler tombol di bawah kamera (Clock Out)
    if (btnCamTakeOut) {
        btnCamTakeOut.addEventListener('click', () => {
            if (!streamActive && !isPhotoTaken) {
                startCamera();
            } else if (!isPhotoTaken) {
                takePhoto();
            } else {
                if (editorOut) {
                    editorOut.focus();
                }
                const formClockOut = document.getElementById('form-clockout');
                if (formClockOut) {
                    formClockOut.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    }

    // Initialize GPS
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                currentLocation = lat + ',' + lon;
                locationText.innerText = 'Koordinat: ' + currentLocation;
                
                mapIframe.src = `https://maps.google.com/maps?q=${lat},${lon}&hl=id&z=15&output=embed`;
                mapContainer.classList.remove('hidden');
                
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                    .then(response => response.json())
                    .then(data => {
                        if(data.display_name) {
                            addressText.innerText = data.display_name;
                        }
                    })
                    .catch(err => {
                        console.log("Geocoding error", err);
                        addressText.innerText = 'Gagal memuat alamat lengkap.';
                    });
            },
            function(error) {
                console.error("Location error:", error);
                locationText.innerText = 'Gagal mendapatkan lokasi. Pastikan GPS aktif.';
            },
            { enableHighAccuracy: true }
        );
    } else {
        locationText.innerText = 'Browser tidak mendukung Geolokasi.';
    }

    // Submit Handler Clock In (HANYA dipanggil dari tombol form)
    const submitClockIn = () => {
        if (!currentLocation) {
            alert("Menunggu data lokasi GPS... Pastikan izin GPS aktif.");
            return;
        }
        if (!isPhotoTaken) {
            if (!streamActive) {
                startCamera();
                alert("Silakan ambil foto selfie terlebih dahulu sebelum mengirim absensi.");
            } else {
                alert("Silakan klik 'Jepret Foto Selfie' terlebih dahulu sebelum mengirim absensi.");
            }
            return;
        }

        document.getElementById('foto-in').value = photoData;
        document.getElementById('lokasi-in').value = currentLocation + (addressText.innerText ? ' | ' + addressText.innerText : '');
        
        if (editorIn) {
            const content = editorIn.root.innerHTML;
            document.getElementById('keterangan-in').value = content === '<p><br></p>' ? '' : content;
        }
        
        if (btnSubmitIn) {
            btnSubmitIn.disabled = true;
            if (btnSubmitInText) btnSubmitInText.innerText = "Mengirim Data...";
        }
        document.getElementById('form-clockin').submit();
    };

    // Submit Handler Clock Out (HANYA dipanggil dari tombol form)
    const submitClockOut = () => {
        if (!currentLocation) {
            alert("Menunggu data lokasi GPS... Pastikan izin GPS aktif.");
            return;
        }
        if (!isPhotoTaken) {
            if (!streamActive) {
                startCamera();
                alert("Silakan ambil foto selfie terlebih dahulu sebelum mengirim absensi.");
            } else {
                alert("Silakan klik 'Jepret Foto Selfie' terlebih dahulu sebelum mengirim absensi.");
            }
            return;
        }

        // Validasi Wajib Laporan Harian
        let reportText = '';
        if (editorOut) {
            reportText = editorOut.getText().trim();
        }
        if (!reportText || reportText.length === 0) {
            alert("Laporan harian wajib diisi sebelum melakukan absensi keluar!");
            if (editorOut) editorOut.focus();
            return;
        }

        document.getElementById('foto-out').value = photoData;
        document.getElementById('lokasi-out').value = currentLocation + (addressText.innerText ? ' | ' + addressText.innerText : '');
        
        const content = editorOut.root.innerHTML;
        document.getElementById('keterangan-out').value = content;
        
        if (btnSubmitOut) {
            btnSubmitOut.disabled = true;
            if (btnSubmitOutText) btnSubmitOutText.innerText = "Mengirim Data...";
        }
        document.getElementById('form-clockout').submit();
    };

    // Pasang Event Listener HANYA ke Tombol Form Submit
    if (btnSubmitIn) btnSubmitIn.addEventListener('click', submitClockIn);
    if (btnSubmitOut) btnSubmitOut.addEventListener('click', submitClockOut);
</script>
@endpush
