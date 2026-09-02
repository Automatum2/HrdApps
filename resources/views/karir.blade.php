@extends('layouts.guest')

@section('title', 'Karir - HRDApps')

@push('styles')
<!-- Summernote Lite CDN (tanpa dependensi Bootstrap berat) -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .note-editor.note-frame {
        border: 1px solid #cbd5e1 !important;
        border-radius: 0.5rem !important;
        overflow: hidden;
        background: #f8fafc !important;
    }
    .note-toolbar {
        background: #f1f5f9 !important;
        border-bottom: 1px solid #cbd5e1 !important;
    }
    .note-editable {
        background: #ffffff !important;
        font-family: 'Inter', sans-serif !important;
        font-size: 0.875rem !important;
        min-height: 220px !important;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen w-full py-10 px-4 flex flex-col items-center justify-center relative z-10">
    <div class="w-full max-w-3xl bg-white/95 rounded-2xl shadow-xl border border-slate-200 overflow-hidden backdrop-blur-md">
        
        <!-- Header -->
        <div class="bg-primary p-8 text-white text-center relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="absolute -left-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="w-14 h-14 mx-auto mb-3 drop-shadow">
            <h1 class="text-3xl font-bold font-title-large">Portal Karir HRDApps</h1>
            <p class="mt-2 text-sm max-w-[550px] mx-auto text-white/90">Bergabunglah bersama kami. Lengkapi formulir pendaftaran dan riwayat profesional Anda di bawah ini.</p>
        </div>

        <div class="p-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-50 text-green-700 border border-green-200 rounded-xl p-4 flex items-start gap-3 shadow-sm">
                    <span class="material-symbols-outlined text-green-600 mt-0.5">check_circle</span>
                    <div>
                        <p class="font-bold text-base">Lamaran Terkirim!</p>
                        <p class="text-sm mt-1 leading-relaxed">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 text-red-700 border border-red-200 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <span class="material-symbols-outlined text-red-600">error</span>
                        <span>Harap periksa kembali isian form Anda:</span>
                    </div>
                    <ul class="list-disc list-inside text-sm pl-4 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('karir.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Info Banner -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center gap-3 text-blue-800 text-xs">
                    <span class="material-symbols-outlined text-primary text-xl shrink-0">info</span>
                    <div>
                        <strong class="font-semibold">Penting:</strong> Pastikan alamat email yang Anda masukkan <strong>aktif</strong>. Tautan aktivasi akun beserta <strong>Kode OTP</strong> akan dikirimkan ke email tersebut jika Anda diterima.
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Nama Lengkap -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700" for="nama">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 placeholder:text-slate-400" placeholder="Contoh: Adi Saputra">
                    </div>

                    <!-- Email Aktif -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700" for="email">
                            Alamat Email (Aktif) <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 placeholder:text-slate-400" placeholder="Contoh: adi.saputra@email.com">
                    </div>
                </div>

                <!-- Dropdown Status Kerja yang Dilamar -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700" for="status_kerja">
                        Status Kerja yang Dilamar <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select id="status_kerja" name="status_kerja" required class="w-full appearance-none bg-slate-50 border border-slate-300 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 cursor-pointer">
                            <option value="" disabled {{ old('status_kerja') ? '' : 'selected' }}>-- Pilih Status Kerja --</option>
                            <option value="tetap" {{ old('status_kerja') === 'tetap' ? 'selected' : '' }}>Karyawan Tetap</option>
                            <option value="kontrak" {{ old('status_kerja') === 'kontrak' ? 'selected' : '' }}>Karyawan Kontrak</option>
                            <option value="harian" {{ old('status_kerja') === 'harian' ? 'selected' : '' }}>Karyawan Harian</option>
                            <option value="tenaga_lepas" {{ old('status_kerja') === 'tenaga_lepas' ? 'selected' : '' }}>Tenaga Lepas (Freelance)</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                    </div>
                </div>

                <!-- CV Text / Profil Lengkap dengan Summernote (opsional) -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700" for="cv_text">
                            Profil
                        </label>
                    </div>
                    <p class="text-xs text-slate-500">Anda dapat menuliskan ringkasan profil, pengalaman kerja, pendidikan, dan keahlian Anda menggunakan editor di bawah.</p>
                    <textarea id="cv_text" name="cv_text" rows="8" class="w-full min-h-[250px] p-4 bg-slate-50 border border-slate-300 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 placeholder:text-slate-400 summernote-editor" placeholder="Tuliskan profil singkat, pengalaman kerja, riwayat pendidikan, dan keahlian Anda di sini...">{!! old('cv_text') !!}</textarea>
                </div>

                <!-- Dokumen CV / URL Medsos -->
                <div class="flex flex-col gap-4 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                    <!-- URL / Medsos (Tautan Profil) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center justify-between" for="cv_url">
                            <span>URL LinkedIn / Medsos / Portofolio (Profil)</span>
                            <span class="text-[10px] text-slate-400 font-normal lowercase">(opsional)</span>
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">link</span>
                            <input type="url" id="cv_url" name="cv_url" value="{{ old('cv_url') }}" class="w-full bg-white border border-slate-300 rounded-lg pl-9 pr-3 py-2.5 text-xs outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800 placeholder:text-slate-400" placeholder="https://linkedin.com/in/username">
                        </div>
                        <p class="text-[11px] text-slate-500">Tautan profil publik atau portofolio online</p>
                    </div>

                    <!-- Upload File CV -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center justify-between" for="cv_file">
                            <span>Upload File CV</span>
                            <span class="text-[10px] text-slate-400 font-normal lowercase">(opsional)</span>
                        </label>
                        <input type="file" id="cv_file" name="cv_file" accept=".pdf,.doc,.docx" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 file:cursor-pointer cursor-pointer border border-slate-300 rounded-lg bg-white p-1.5">
                        <p class="text-[11px] text-slate-500">Format: PDF, DOC, DOCX (Maks. 5MB)</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-[#0066ff] hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-md hover:shadow-lg transition-all active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-xl">send</span>
                    <span>Kirim Lamaran Pekerjaan</span>
                </button>
                
                <div class="pt-2 text-center">
                    <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-500 hover:text-primary transition-colors inline-flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        <span>Kembali ke Halaman Login</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('#cv_text').summernote({
            placeholder: 'Tuliskan profil singkat, pengalaman kerja, riwayat pendidikan, dan keahlian Anda di sini...',
            tabsize: 2,
            height: 220,
            toolbar: [
                ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    });
</script>
@endpush