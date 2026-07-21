# Panduan Fitur Aktivasi Akun via Email (User Provisioning)

Dokumen ini menjelaskan alur sistem pembuatan akun karyawan baru (oleh Super Admin/Manager) dan bagaimana karyawan tersebut mengatur kata sandinya (*password*) melalui mekanisme email aktivasi. 

Sistem ini dirancang untuk memberikan standar keamanan dan profesionalitas tingkat tinggi bagi HRDApps.

## 1. Alur Sistem (Flow)

1. **Super Admin / Manager Membuat Karyawan**
   - Mengakses halaman "Kelola Karyawan".
   - Mengisi form "Tambah Karyawan" (Nama, Email, Gaji, dll).
   - Menekan tombol "Simpan".

2. **Proses di Backend (Laravel)**
   - Sistem akan memvalidasi data.
   - Sistem akan membuat data user baru di tabel `users` dengan kata sandi acak (sementara).
   - Sistem akan menghasilkan *Token* rahasia (sekali pakai).
   - Sistem akan mengirimkan Email berisi tautan (URL) khusus yang memuat token tersebut ke alamat email karyawan.

3. **Karyawan Mengaktifkan Akun**
   - Karyawan menerima email berjudul "Aktivasi Akun HRDApps".
   - Karyawan menekan tombol/tautan "Aktifkan Akun & Buat Password".
   - Karyawan diarahkan ke halaman "Buat Password Baru" di website HRDApps.
   - Setelah memasukkan password baru yang mereka inginkan, sistem akan memperbarui kata sandi di database dan karyawan bisa langsung login.

## 2. Implementasi Teknis (Laravel)

Sistem ini memanfaatkan fitur bawaan Laravel yaitu **Password Reset Broker** dengan beberapa modifikasi:

- **Tabel Database:** Menggunakan tabel `password_reset_tokens` untuk menyimpan token aktivasi.
- **Mail Driver:** Pada mode *development* (pengembangan lokal), kita dapat menggunakan pengaturan `MAIL_MAILER=log` (email masuk ke file `storage/logs/laravel.log`) atau menggunakan layanan penangkap email gratis seperti **Mailtrap**.
- **Controller:** Modifikasi `UserController` atau `EmployeeController` untuk mengeksekusi pengiriman email saat user berhasil dibuat.
- **Views:** 
  - `resources/views/emails/activation.blade.php` (Desain tampilan email).
  - `resources/views/auth/reset-password.blade.php` (Halaman untuk karyawan memasukkan password).

## 3. Persiapan Lingkungan (Environment)
Untuk menguji fitur ini saat pengembangan (PKL), pastikan konfigurasi `.env` bagian Mail disesuaikan. Contoh menggunakan Mailtrap (Opsional):
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@hrdapps.id"
MAIL_FROM_NAME="HRDApps System"
```
*Jika menggunakan `MAIL_MAILER=log`, email dapat dilihat dengan membuka file `storage/logs/laravel.log`.*
