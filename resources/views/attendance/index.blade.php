@extends('layouts.admin')

@section('title', 'Absensi Kehadiran - HRDApps')
@section('page_title', 'Absensi Kehadiran')

@section('content')
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
                            <option value="WFO">WFO - Work From Office</option>
                            <option value="WFH">WFH - Work From Home</option>
                            <option value="WFF">WFF - Work From Field</option>
                            <option value="WOD">WOD - Work On Duty</option>
                            <option value="WEH">WEH - Work Extra Hours</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-on-surface-variant mb-1">Laporan Singkat / Keterangan (Opsional)</label>
                        <textarea name="keterangan" rows="3" class="w-full p-2 border border-outline-variant rounded-lg bg-surface text-on-surface"></textarea>
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
                    <li>Klik tombol Clock Out saat mengakhiri kerja.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: Kamera & Peta GPS -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 card-shadow h-fit order-1 lg:order-2">
        <h4 class="font-title-sm text-title-sm text-on-background font-bold mb-4">Bukti Visual & Lokasi</h4>
        
        <div class="mb-4 relative group">
            <!-- Placeholder (Dashed Box) -->
            <div id="camera-placeholder" class="w-full h-64 border-2 border-dashed border-outline-variant rounded-xl flex flex-col items-center justify-center text-on-surface-variant cursor-pointer hover:bg-surface-container hover:border-primary transition-colors">
                <span class="material-symbols-outlined text-4xl mb-2 text-outline">photo_camera</span>
                <span class="font-bold">Ketuk untuk membuka kamera</span>
                <span class="text-xs mt-1">Kamera depan, mode selfie</span>
            </div>

            <!-- Video & Canvas (Hidden Initially) -->
            <video id="camera-feed" class="w-full rounded-lg bg-black hidden" autoplay playsinline></video>
            <img id="photo-preview" class="w-full rounded-lg bg-black hidden" />
            <canvas id="camera-canvas" class="hidden"></canvas>
            
            <button type="button" id="btn-retake-photo" class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-white text-error p-3 rounded-full shadow-lg hover:scale-105 transition hidden" title="Ulangi Foto">
                <span class="material-symbols-outlined text-2xl">refresh</span>
            </button>
        </div>

        <div class="mb-4">
            <div id="map-container" class="w-full h-40 bg-slate-100 rounded-lg overflow-hidden hidden mb-2 border border-outline-variant">
                <iframe id="map-iframe" width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src=""></iframe>
            </div>
            <p id="location-text" class="text-sm text-on-surface-variant mt-2 font-mono font-bold">Mendapatkan lokasi...</p>
            <p id="address-text" class="text-xs text-on-surface-variant mt-1 leading-relaxed"></p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const cameraPlaceholder = document.getElementById('camera-placeholder');
    const video = document.getElementById('camera-feed');
    const photoPreview = document.getElementById('photo-preview');
    const canvas = document.getElementById('camera-canvas');
    const btnRetakePhoto = document.getElementById('btn-retake-photo');
    const locationText = document.getElementById('location-text');
    const addressText = document.getElementById('address-text');
    const mapContainer = document.getElementById('map-container');
    const mapIframe = document.getElementById('map-iframe');
    
    let currentLocation = '';
    let isPhotoTaken = false;
    let photoData = null;
    let streamActive = null;

    // Start Camera when Placeholder is clicked
    cameraPlaceholder.addEventListener('click', () => {
        if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    streamActive = stream;
                    video.srcObject = stream;
                    cameraPlaceholder.classList.add('hidden');
                    video.classList.remove('hidden');
                })
                .catch(function(err) {
                    console.error("Camera error:", err);
                    alert("Tidak dapat mengakses kamera. Pastikan Anda telah memberikan izin akses kamera.");
                });
        }
    });

    // Retake Logic
    btnRetakePhoto.addEventListener('click', () => {
        photoPreview.classList.add('hidden');
        video.classList.remove('hidden');
        btnRetakePhoto.classList.add('hidden');
        isPhotoTaken = false;
        photoData = null;
        
        // Kembalikan teks tombol submit
        const btnIn = document.getElementById('btn-submit-in');
        if(btnIn) btnIn.innerText = "Jepret Foto & Clock In";
        const btnOut = document.getElementById('btn-submit-out');
        if(btnOut) btnOut.innerText = "Jepret Foto & Clock Out";
    });

    // Initialize GPS
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                currentLocation = lat + ',' + lon;
                locationText.innerText = 'Koordinat: ' + currentLocation;
                
                // Show Map
                mapIframe.src = `https://maps.google.com/maps?q=${lat},${lon}&hl=id&z=15&output=embed`;
                mapContainer.classList.remove('hidden');
                
                // Fetch Address (Reverse Geocoding OpenStreetMap)
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
                locationText.innerText = 'Gagal mendapatkan lokasi. Pastikan izin lokasi diaktifkan.';
            },
            { enableHighAccuracy: true }
        );
    } else {
        locationText.innerText = 'Browser Anda tidak mendukung Geolokasi.';
    }

    // Submit Logic for Clock In
    const btnSubmitIn = document.getElementById('btn-submit-in');
    if(btnSubmitIn) {
        btnSubmitIn.addEventListener('click', function() {
            if(!currentLocation) {
                alert("Menunggu data lokasi... Pastikan GPS Anda aktif.");
                return;
            }
            if(!streamActive && !isPhotoTaken) {
                alert("Silakan ketuk area kamera untuk mengaktifkan kamera terlebih dahulu!");
                return;
            }

            if(!isPhotoTaken) {
                // Jepret Foto (Fase 1) dengan Kompresi Resolusi & Kualitas
                const MAX_WIDTH = 640;
                let width = video.videoWidth;
                let height = video.videoHeight;

                // Perkecil resolusi jika terlalu besar (terutama kamera HP modern)
                if (width > MAX_WIDTH) {
                    height = Math.round((height * MAX_WIDTH) / width);
                    width = MAX_WIDTH;
                }

                canvas.width = width;
                canvas.height = height;
                canvas.getContext('2d').drawImage(video, 0, 0, width, height);
                
                // Konversi ke base64 dengan format JPEG dan kualitas 60% (0.6)
                photoData = canvas.toDataURL('image/jpeg', 0.6);
                
                photoPreview.src = photoData;
                photoPreview.classList.remove('hidden');
                video.classList.add('hidden');
                btnRetakePhoto.classList.remove('hidden');
                
                isPhotoTaken = true;
                btnSubmitIn.innerText = "Kirim Absensi Sekarang";
            } else {
                // Submit Form (Fase 2)
                document.getElementById('foto-in').value = photoData;
                document.getElementById('lokasi-in').value = currentLocation + (addressText.innerText ? ' | ' + addressText.innerText : '');
                
                btnSubmitIn.disabled = true;
                btnSubmitIn.innerText = "Mengirim Data...";
                document.getElementById('form-clockin').submit();
            }
        });
    }

    // Submit Logic for Clock Out
    const btnSubmitOut = document.getElementById('btn-submit-out');
    if(btnSubmitOut) {
        btnSubmitOut.addEventListener('click', function() {
            if(!currentLocation) {
                alert("Menunggu data lokasi... Pastikan GPS Anda aktif.");
                return;
            }
            if(!streamActive && !isPhotoTaken) {
                alert("Silakan ketuk area kamera untuk mengaktifkan kamera terlebih dahulu!");
                return;
            }

            if(!isPhotoTaken) {
                // Jepret Foto (Fase 1) dengan Kompresi Resolusi & Kualitas
                const MAX_WIDTH = 640;
                let width = video.videoWidth;
                let height = video.videoHeight;

                // Perkecil resolusi jika terlalu besar (terutama kamera HP modern)
                if (width > MAX_WIDTH) {
                    height = Math.round((height * MAX_WIDTH) / width);
                    width = MAX_WIDTH;
                }

                canvas.width = width;
                canvas.height = height;
                canvas.getContext('2d').drawImage(video, 0, 0, width, height);
                
                // Konversi ke base64 dengan format JPEG dan kualitas 60% (0.6)
                photoData = canvas.toDataURL('image/jpeg', 0.6);
                
                photoPreview.src = photoData;
                photoPreview.classList.remove('hidden');
                video.classList.add('hidden');
                btnRetakePhoto.classList.remove('hidden');
                
                isPhotoTaken = true;
                btnSubmitOut.innerText = "Kirim Absensi Keluar Sekarang";
            } else {
                // Submit Form (Fase 2)
                document.getElementById('foto-out').value = photoData;
                document.getElementById('lokasi-out').value = currentLocation + (addressText.innerText ? ' | ' + addressText.innerText : '');
                
                btnSubmitOut.disabled = true;
                btnSubmitOut.innerText = "Mengirim Data...";
                document.getElementById('form-clockout').submit();
            }
        });
    }
</script>
@endpush
