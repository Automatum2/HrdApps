<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($type) && $type === 'activation' ? 'Aktivasi Akun' : 'Reset Kata Sandi' }} - HRDApps</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-lg border border-slate-100 max-w-md w-full">
        <div class="text-center mb-6">
            <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="w-16 h-16 mx-auto mb-4">
            @if(isset($type) && $type === 'activation')
                <h2 class="text-2xl font-bold text-slate-800">Aktivasi Akun Karyawan</h2>
                <p class="text-slate-500 text-sm mt-2">Masukkan kode OTP yang dikirim ke email dan buat password baru Anda.</p>
            @else
                <h2 class="text-2xl font-bold text-slate-800">Reset Kata Sandi</h2>
                <p class="text-slate-500 text-sm mt-2">Silakan buat password baru untuk mengakses akun Anda kembali.</p>
            @endif
        </div>

        @if($errors->any())
            <div class="mb-4 bg-red-50 text-red-700 border border-red-200 rounded-lg p-3 text-xs">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="type" value="{{ $type ?? 'reset' }}">

            @if(isset($namaLengkap) && $namaLengkap)
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" value="{{ $namaLengkap }}" disabled class="w-full px-4 py-2 bg-slate-100 border border-slate-300 rounded-lg text-slate-700 font-semibold cursor-not-allowed">
            </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" value="{{ $email }}" disabled class="w-full px-4 py-2 bg-slate-100 border border-slate-300 rounded-lg text-slate-500 cursor-not-allowed">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Username <span class="text-xs text-slate-400 font-normal">(Gunakan ini untuk login)</span></label>
                <input type="text" value="{{ $username ?? 'Username belum dimuat' }}" disabled class="w-full px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 font-bold cursor-not-allowed select-all">
            </div>

            @if(isset($type) && $type === 'activation')
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">
                    Kode OTP Aktivasi <span class="text-xs text-primary font-normal">(6 Digit di Email)</span>
                </label>
                <input type="text" name="otp" required maxlength="6" pattern="[0-9]{6}" placeholder="123456" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-center font-mono tracking-widest text-lg font-bold">
            </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Password Baru</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg transition-colors">
                Simpan Password & Login
            </button>
        </form>
    </div>
</body>
</html>
