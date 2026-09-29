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
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-stagger">
    
    <!-- KOLOM KIRI: Form Input & Informasi -->
    <div class="flex flex-col gap-6 order-2 lg:order-1">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow">
            <h4 class="font-title-sm text-title-sm text-on-background font-bold mb-4">Form Kehadiran</h4>
            
            @if(session('success'))
                <div class="bg-primary/20 text-primary p-3 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-error/20 text-error p-3 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if(!$attendance || !$attendance->jam_masuk)
                <!-- Form Clock In -->
                <form action="{{ route('attendance.clock_in') }}" method="POST" id="form-clockin">
                    @csrf
                    <input type="hidden" name="foto" id="foto-in">
                    <input type="hidden" name="lokasi" id="lokasi-in">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-on-surface-variant mb-1">Status Kerja</label>
                        <select name="status_kerja" class="w-full p-2 border border-outline-variant rounded-lg bg-surface text-on-surface">
                            <option value="WFO">WFO - Work From Office (Kantor Pusat)</option>
                            <option value="WFD">WFD - Work From Desk (Radius 100m)</option>
                            <option value="WFH">WFH - Work From Home</option>
                            <option value="WFF">WFF - Work From Field</option>
                            <option value="WOD">WOD - Work On Duty</option>
                            <option value="WEH">WEH - Work Extra Hours</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-on-surface-variant mb-1">Laporan Singkat / Keterangan (Opsional)</label>
                        <div class="bg-surface rounded-lg overflow-hidden">
                            <div id="editor-in"></div>
                        </div>
                        <input type="hidden" name="keterangan" id="keterangan-in">
                    </div>

                    <button type="button" id="btn-submit-in" class="w-full bg-primary text-white font-bold py-3 rounded-lg shadow-md hover:brightness-110 transition cursor-pointer">
                        Jepret Foto & Clock In
                    </button>
                </form>
            @elseif(!$attendance->jam_keluar)
                <!-- Form Clock Out -->
                <form action="{{ route('attendance.clock_out') }}" method="POST" id="form-clockout">
                    @csrf
                    <input type="hidden" name="foto" id="foto-out">
                    <input type="hidden" name="lokasi" id="lokasi-out">

                    <div class="mb-4">
                        <p class="text-on-background font-bold">Waktu Clock In: <span class="text-primary">{{ $attendance->jam_masuk }}</span></p>
                        <p class="text-on-background font-bold">Status: <span class="text-primary">{{ $attendance->status_kerja }}</span></p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-on-surface-variant mb-1">
                            Update Laporan Harian <span class="text-error font-bold">*</span> <span class="text-xs font-semibold text-error">(Wajib Diisi)</span>
                        </label>
                        <div class="bg-surface rounded-lg overflow-hidden border border-outline-variant focus-within:border-primary transition-colors">
                            <div id="editor-out">{!! $attendance->keterangan ?? '' !!}</div>
                        </div>
                        <p class="text-[11px] text-on-surface-variant mt-1">Tuliskan ringkasan hasil kerja atau aktivitas yang telah Anda selesaikan hari ini.</p>
                        <input type="hidden" name="keterangan" id="keterangan-out">
                    </div>

                    <button type="button" id="btn-submit-out" class="w-full border border-error text-error bg-error/10 font-bold py-3 rounded-lg shadow-md hover:bg-error hover:text-white transition cursor-pointer">
                        Jepret Foto & Clock Out
                    </button>
                </form>
            @else
                <!-- Selesai Absen -->
                <div class="text-center p-6 bg-primary-container/20 border border-primary/30 rounded-xl">
                    <span class="material-symbols-outlined text-4xl text-primary mb-2">check_circle</span>
                    <h3 class="text-lg font-bold text-on-background">Anda sudah menyelesaikan absensi hari ini.</h3>
                    <p class="text-on-surface-variant">Total Jam Kerja: {{ $attendance->total_jam_kerja }} Jam</p>
                </div>
            @endif
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow">
            <h4 class="font-title-sm text-title-sm text-on-background font-bold mb-4">Informasi Tambahan</h4>
            <p class="text-sm text-on-surface-variant mb-4">Pastikan wajah terlihat jelas dan lokasi sudah sesuai sebelum melakukan absensi.</p>
            <div class="bg-surface-container-low p-4 rounded-lg">
                <p class="font-bold mb-1">Panduan Absensi:</p>
                <ul class="list-disc pl-5 text-sm text-on-surface-variant">
                    <li>Izinkan akses kamera dan lokasi di browser Anda.</li>
                    <li>Pilih status kerja yang sesuai.</li>
                    <li>Ketuk area kamera untuk mengaktifkan video.</li>
                    <li>Klik tombol Clock In saat memulai kerja.</li>
                    <li>Isi laporan harian dan klik tombol Clock Out saat mengakhiri kerja.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: Kamera & Peta GPS (Mobile: Prioritas di atas) -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow h-fit order-1 lg:order-2">
        <div class="flex items-center justify-between mb-4 gap-2 flex-wrap">
            <h4 class="font-title-sm text-title-sm text-on-background font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">photo_camera</span>
                Bukti Visual & Lokasi
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
            <div id="camera-placeholder" class="w-full h-72 border-2 border-dashed border-outline-variant rounded-xl flex flex-col items-center justify-center text-on-surface-variant cursor-pointer bg-surface-container-low hover:bg-surface-container hover:border-primary transition-all p-4 text-center">
                <div class="w-16 h-16 rounded-full bg-primary/10 text-primary flex items-center justify-center mb-3 shadow-inner group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-3xl">add_a_photo</span>
                </div>
                <span class="font-bold text-base text-on-surface">Buka Kamera Selfie</span>
                <span class="text-xs text-on-surface-variant mt-1 max-w-[240px]">Ketuk di sini untuk menyalakan kamera depan dan ambil foto langsung</span>
            </div>

            <!-- Video Live Feed -->
            <div id="video-wrapper" class="relative hidden w-full h-72 bg-black flex items-center justify-center">
                <video id="camera-feed" class="w-full h-full object-cover rounded-xl" autoplay playsinline></video>
            </div>

            <!-- Photo Preview -->
            <div id="preview-wrapper" class="relative hidden w-full h-72 bg-black flex items-center justify-center">
                <img id="photo-preview" class="w-full h-full object-cover rounded-xl" alt="Preview Absensi" />
            </div>
            
            <canvas id="camera-canvas" class="hidden"></canvas>
        </div>

        <!-- Tombol Aksi Langsung Kamera (Mobile Friendly Action Bar) -->
        <div id="camera-action-bar" class="mb-5 space-y-2">
            @if(!$attendance || !$attendance->jam_masuk)
                <!-- Tombol Cepat Clock In saat Kamera Aktif / Foto Selesai -->
                <button type="button" id="btn-cam-clockin" class="w-full bg-primary text-white font-bold py-3.5 px-4 rounded-xl shadow-md hover:brightness-110 active:scale-98 transition flex items-center justify-center gap-2 cursor-pointer text-sm sm:text-base">
                    <span class="material-symbols-outlined text-xl" id="btn-cam-icon-in">photo_camera</span>
                    <span id="btn-cam-text-in">Buka Kamera & Clock In</span>
                </button>
            @elseif(!$attendance->jam_keluar)
                <!-- Tombol Ambil Foto saat Clock Out -->
                <button type="button" id="btn-cam-takephoto-out" class="w-full bg-slate-800 text-white font-bold py-3 px-4 rounded-xl shadow-md hover:bg-slate-700 active:scale-98 transition flex items-center justify-center gap-2 cursor-pointer text-sm">
                    <span class="material-symbols-outlined text-xl" id="btn-cam-icon-take-out">photo_camera</span>
                    <span id="btn-cam-text-take-out">Buka Kamera Selfie</span>
                </button>
            @endif

            <button type="button" id="btn-retake-photo" class="w-full py-2.5 px-4 bg-surface-container border border-outline-variant text-error font-semibold rounded-xl hover:bg-error/10 transition items-center justify-center gap-2 hidden cursor-pointer text-sm">
                <span class="material-symbols-outlined text-lg">refresh</span>
                <span>Foto Ulang (Retake)</span>
            </button>
        </div>

        <div class="border-t border-outline-variant/60 pt-4">
            <div id="map-container" class="w-full h-36 bg-slate-100 rounded-lg overflow-hidden hidden mb-2 border border-outline-variant">
                <iframe id="map-iframe" width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src=""></iframe>
            </div>
            <div class="flex items-start gap-2 mt-2">
                <span class="material-symbols-outlined text-primary text-lg mt-0.5 shrink-0">location_on</span>
                <div>
                    <p id="location-text" class="text-xs text-on-surface font-mono font-bold">Mendapatkan lokasi GPS...</p>
                    <p id="address-text" class="text-xs text-on-surface-variant mt-0.5 leading-relaxed"></p>
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
    
    const btnCamClockIn = document.getElementById('btn-cam-clockin');
    const btnCamTextIn = document.getElementById('btn-cam-text-in');
    const btnCamIconIn = document.getElementById('btn-cam-icon-in');
    
    const btnCamClockOut = document.getElementById('btn-cam-clockout');
    const btnCamTextOut = document.getElementById('btn-cam-text-out');
    const btnCamIconOut = document.getElementById('btn-cam-icon-out');
    
    const btnSubmitIn = document.getElementById('btn-submit-in');
    const btnSubmitOut = document.getElementById('btn-submit-out');

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
                    btnCamIconIn.innerText = "camera";
                }
                if (btnCamTextOut) {
                    btnCamTextOut.innerText = "Jepret Foto Selfie";
                    btnCamIconOut.innerText = "camera";
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

        if (btnCamTextIn) {
            btnCamTextIn.innerText = "Kirim Absensi Masuk (Clock In)";
            btnCamIconIn.innerText = "send";
        }
        
        const btnCamTakeOut = document.getElementById('btn-cam-takephoto-out');
        const btnCamTextTakeOut = document.getElementById('btn-cam-text-take-out');
        const btnCamIconTakeOut = document.getElementById('btn-cam-icon-take-out');
        if (btnCamTakeOut && btnCamTextTakeOut) {
            btnCamTextTakeOut.innerText = "Foto Selesai (Isi Laporan di Bawah)";
            if (btnCamIconTakeOut) btnCamIconTakeOut.innerText = "check_circle";
            btnCamTakeOut.classList.remove('bg-slate-800', 'hover:bg-slate-700');
            btnCamTakeOut.classList.add('bg-green-600', 'hover:bg-green-700');
        }

        if (btnSubmitIn) btnSubmitIn.innerText = "Kirim Absensi Sekarang";
        if (btnSubmitOut) btnSubmitOut.innerText = "Kirim Absensi Keluar Sekarang";
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

        if (btnCamTextIn) {
            btnCamTextIn.innerText = "Jepret Foto Selfie";
            btnCamIconIn.innerText = "camera";
        }

        const btnCamTakeOut = document.getElementById('btn-cam-takephoto-out');
        const btnCamTextTakeOut = document.getElementById('btn-cam-text-take-out');
        const btnCamIconTakeOut = document.getElementById('btn-cam-icon-take-out');
        if (btnCamTakeOut && btnCamTextTakeOut) {
            btnCamTextTakeOut.innerText = "Jepret Foto Selfie";
            if (btnCamIconTakeOut) btnCamIconTakeOut.innerText = "camera";
            btnCamTakeOut.classList.add('bg-slate-800', 'hover:bg-slate-700');
            btnCamTakeOut.classList.remove('bg-green-600', 'hover:bg-green-700');
        }

        if (btnSubmitIn) btnSubmitIn.innerText = "Jepret Foto & Clock In";
        if (btnSubmitOut) btnSubmitOut.innerText = "Jepret Foto & Clock Out";
    };

    // Event Trigger Buka Kamera & Shutter
    if (cameraPlaceholder) cameraPlaceholder.addEventListener('click', startCamera);
    if (btnShutter) btnShutter.addEventListener('click', takePhoto);
    if (btnRetakePhoto) btnRetakePhoto.addEventListener('click', resetPhoto);

    const btnCamTakeOut = document.getElementById('btn-cam-takephoto-out');
    if (btnCamTakeOut) {
        btnCamTakeOut.addEventListener('click', () => {
            if (!streamActive && !isPhotoTaken) {
                startCamera();
            } else if (!isPhotoTaken) {
                takePhoto();
            } else {
                // Scroll smoothly to form laporan
                if (editorOut) {
                    editorOut.focus();
                    document.getElementById('form-clockout').scrollIntoView({ behavior: 'smooth' });
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

    // Submit Handler Clock In
    const submitClockIn = () => {
        if (!currentLocation) {
            alert("Menunggu data lokasi GPS... Pastikan izin GPS aktif.");
            return;
        }
        if (!streamActive && !isPhotoTaken) {
            startCamera();
            return;
        }
        if (!isPhotoTaken) {
            takePhoto();
            return;
        }

        document.getElementById('foto-in').value = photoData;
        document.getElementById('lokasi-in').value = currentLocation + (addressText.innerText ? ' | ' + addressText.innerText : '');
        
        if (editorIn) {
            const content = editorIn.root.innerHTML;
            document.getElementById('keterangan-in').value = content === '<p><br></p>' ? '' : content;
        }
        
        if (btnCamClockIn) {
            btnCamClockIn.disabled = true;
            btnCamTextIn.innerText = "Mengirim Absensi...";
        }
        if (btnSubmitIn) {
            btnSubmitIn.disabled = true;
            btnSubmitIn.innerText = "Mengirim Data...";
        }
        document.getElementById('form-clockin').submit();
    };

    // Submit Handler Clock Out
    const submitClockOut = () => {
        if (!currentLocation) {
            alert("Menunggu data lokasi GPS... Pastikan izin GPS aktif.");
            return;
        }
        if (!streamActive && !isPhotoTaken) {
            startCamera();
            return;
        }
        if (!isPhotoTaken) {
            takePhoto();
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
            btnSubmitOut.innerText = "Mengirim Data...";
        }
        document.getElementById('form-clockout').submit();
    };

    // Pasang Event Listener ke Tombol Kamera & Form
    if (btnCamClockIn) btnCamClockIn.addEventListener('click', submitClockIn);
    if (btnSubmitIn) btnSubmitIn.addEventListener('click', submitClockIn);

    if (btnSubmitOut) btnSubmitOut.addEventListener('click', submitClockOut);
</script>
@endpush
