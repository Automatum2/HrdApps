# TODO List HRDApps

## Bug Fixes & Refactoring (Ditunda)
- [ ] **Perbaikan Responsive Mobile (Layout Admin):** Sidebar (menu kiri) masih memaksa terbuka atau menggencet konten utama (Dashboard/Absensi) saat diakses melalui browser HP. 
  - *Target File:* `resources/views/layouts/admin.blade.php`.
  - *Solusi Nanti:* Pastikan fungsionalitas hamburger menu dan *overlay* berfungsi sempurna dengan CSS Tailwind tanpa bentrok dengan class bawaan.

## Prioritas Saat Ini
- [x] **Modul Slip Gaji Karyawan:** Membuat dan mengintegrasikan halaman UI untuk Slip Gaji (Payroll) beserta fungsionalitas perhitungannya.

## Status Sistem Karyawan
Berikut adalah rekap halaman untuk Karyawan (*Employee Role*) yang sudah selesai dibangun dan yang masih masuk daftar antrean:

### ✅ Sudah Selesai (Done)
- [x] **Autentikasi (Login):** Karyawan dapat masuk menggunakan akun masing-masing.
- [x] **Dashboard Utama:** Ringkasan status hari ini, total jam kerja, dan riwayat aktivitas terakhir.
- [x] **Modul Absensi (Clock In / Clock Out):** Lengkap dengan integrasi kamera (wajah), deteksi lokasi (*Geocoding* GPS OpenStreetMap), form keterangan, dan pencegahan *double-submit*.
- [x] **Struktur Database Absensi:** Sudah menampung kolom `foto` (base64) dan `lokasi`.
- [x] **Layout Kerangka Dasar (Desktop):** *Sidebar* navigasi dan *topbar* sudah jadi dengan *styling* yang modern.

### ⏳ Belum Selesai (To-Do)
- [x] **Modul Penggajian / Slip Gaji:** Halaman untuk melihat detail dan rincian gaji bulanan. (Selesai)
- [ ] **Modul Riwayat Lengkap Absensi:** Halaman kalender / daftar tabel untuk melihat riwayat absen lengkap di bulan-bulan sebelumnya.
- [ ] **Rekap Absensi (Khusus Manager):** Fitur dan halaman bagi Manager untuk melihat, memantau, dan memvalidasi data absensi (jam masuk/keluar & foto/lokasi) dari *seluruh* karyawan.
- [x] **Pengaturan Profil & Akun:** Halaman bagi karyawan untuk mengganti profil dan *upload* dokumen. (Selesai)
  - *Catatan Teknis Upload File:* 
    - **Limitasi Server (PHP):** *Default* `upload_max_filesize` dan `post_max_size` pada PHP (seringkali 2MB) membatasi ukuran gambar (JPG/PNG). Jika file > 2MB, request ditolak oleh PHP, `$_FILES` akan kosong, dan sistem memunculkan error seolah-olah "file belum dipilih". Solusi jangka panjang: tingkatkan limit di `php.ini` ke 10M atau 20M.
    - **Bug Browser (File Picker):** Memilih file yang sama persis dua kali berturut-turut pada *browser* (Chrome/Firefox) tidak akan memicu event `onchange`. Solusi yang telah diterapkan: menggunakan `onclick="this.value = null;"` pada elemen `<input type="file">` agar *browser* selalu mereset *cache* input.
- [ ] **Sistem Notifikasi:** Lonceng notifikasi di pojok kanan atas belum tersambung dengan sistem *alert* backend.
- [ ] **Layout Mobile (Responsif):** Seperti yang tercatat di daftar Bug, perbaikan tampilan khusus akses lewat HP.

### 🚀 Rencana Masa Depan (Opsional / Future Features)
- [ ] **Sistem Pengajuan Lembur (Surat Perintah Lembur):** Fitur bagi karyawan untuk melakukan *request* jam lembur dan harus di-Approve oleh atasan agar jam ekstra setelah jam pulang sah dihitung sebagai uang lembur.
