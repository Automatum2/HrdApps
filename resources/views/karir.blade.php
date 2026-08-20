@extends('layouts.guest')

@section('title', 'Karir - HRDApps')

@section('content')
<div class="min-h-screen bg-slate-50 flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
        
        <div class="bg-primary p-8 text-white text-center">
            <h1 class="text-3xl font-bold font-title-large">Portal Karir HRDApps</h1>
            <p class="mt-2 text-primary-100 text-body-lg text-white/80">Bergabunglah dengan tim kami. Silakan lengkapi formulir di bawah ini.</p>
        </div>

        <div class="p-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 text-green-700 border border-green-200 rounded-lg p-4 flex items-center gap-3">
                    <span class="material-symbols-outlined">check_circle</span>
                    <div>
                        <p class="font-bold">Berhasil!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('karir.submit') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Nama Lengkap -->
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700" for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" placeholder="Masukkan nama lengkap Anda">
                    @error('nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700" for="email">Alamat Email</label>
                    <input type="email" id="email" name="email" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" placeholder="Masukkan email yang aktif">
                    @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- CV Text -->
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700" for="cv_text">Riwayat Hidup (CV)</label>
                    <p class="text-xs text-slate-500 mb-2">Tuliskan riwayat pendidikan, pengalaman kerja, dan keahlian Anda di sini.</p>
                    <textarea id="cv_text" name="cv_text" required rows="10" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-slate-800" placeholder="Contoh: Saya lulusan S1 Teknik Informatika..."></textarea>
                    @error('cv_text') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md transition-all active:scale-95 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">send</span>
                    Kirim Lamaran
                </button>
                
                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="text-sm text-slate-500 hover:text-primary transition-colors">Kembali ke halaman Login</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
