# TODO List HRDApps

## Bug Fixes & Refactoring (Ditunda)
- [ ] **Perbaikan Responsive Mobile (Layout Admin):** Sidebar (menu kiri) masih memaksa terbuka atau menggencet konten utama (Dashboard/Absensi) saat diakses melalui browser HP. 
  - *Target File:* `resources/views/layouts/admin.blade.php`.
  - *Solusi Nanti:* Pastikan fungsionalitas hamburger menu dan *overlay* berfungsi sempurna dengan CSS Tailwind tanpa bentrok dengan class bawaan.

## Daftar Fitur Berdasarkan Dokumentasi (Docs)

Berikut adalah rekap fitur berdasarkan `konsep-dasar.md` beserta status pengerjaannya saat ini:

### ✅ Sudah Selesai (Done)

**1. Modul Login & Autentikasi**
- [x] Form Login & Session Management
- [x] Role-based Access (Super Admin, Admin/Manager, Karyawan)

**2. Panel Super Admin**
- [x] Dashboard Super Admin (Statistik sistem)
- [x] Kelola HRD Manager (Fungsi CRUD)
- [x] Kelola Karyawan (Fungsi CRUD & Setup Gaji Pokok)

**3. Panel Admin / HRD (Manager)**
- [x] Dashboard Admin (Ringkasan absensi, gaji, dan karyawan)
- [x] Daftar Karyawan (Tampilan Data dan Manajemen Departemen)
- [x] Rekap Absensi *(Sedang Testing: Dinamisasi Filter Tanggal & Export Excel)*
- [x] Pengaturan Akun & Logout
- [x] **Assign Karyawan ke Departemen:** Fitur bagi Manager untuk menempatkan (assign) karyawan baru ke departemen kelolaannya, dan melepas (remove) karyawan dari departemen.

**4. Panel Karyawan**
- [x] Dashboard Utama: Ringkasan status hari ini, total jam kerja, riwayat aktivitas, dan kalender absensi.
- [x] Modul Absensi (Clock In / Clock Out): Integrasi kamera wajah, deteksi lokasi GPS, form keterangan. *(Sedang Testing: Kompresi Resolusi Foto Kamera)*
- [x] **Validasi Pengajuan Izin/Cuti:** Aturan validasi H-1 sebelum hari H untuk pengajuan cuti beserta batasan sisa kuota.
- [x] **Perhitungan Masa Kerja (Tenure):** Sistem menghitung tenure otomatis berdasarkan *Join Date* secara *real-time*.
- [x] Pengaturan Profil & CV: Upload foto profil, dokumen pendukung (KTP/NPWP), dan update CV.
  - *Catatan Teknis Upload File:* 
    - **Limitasi Server (PHP):** *Default* `upload_max_filesize` dan `post_max_size` pada PHP (seringkali 2MB) membatasi ukuran gambar. Solusi: tingkatkan limit di `php.ini` ke 10M atau 20M.
    - **Bug Browser (File Picker):** Memilih file yang sama persis dua kali tidak memicu event `onchange`. Solusi: gunakan `onclick="this.value = null;"` pada elemen `<input type="file">`.
- [x] Modul Slip Gaji Dasar: Karyawan dapat melihat dan mengunduh (PDF) slip gaji secara *on-the-fly*. Termasuk kalkulasi detail potongan **BPJS & PPh 21**.

**5. Infrastruktur & Lain-lain**
- [x] Struktur Database Dasar (Users, Employees, Attendances, dll.)
- [x] Layout Kerangka Dasar Desktop (Sidebar & Topbar)
- [x] **Email & Reset Password:** *(Sedang Testing: Pemisahan Email Lupa Sandi vs Email Aktivasi Karyawan Baru)*

### ⏳ Belum Selesai (To-Do)

**Modul Admin / HRD (Manager)**
- [ ] **Modul Penggajian Lengkap:** Pembuatan *Periode Gaji*, penyimpanan *Payroll* ke Database (tabel `payroll_periods` & `payrolls`), proses Review & Approval, serta Riwayat Penggajian (Saat ini gaji baru dikalkulasi sementara *on-the-fly*).
- [ ] **Laporan & Rekap Terpadu:** Generate laporan absensi, penggajian, dan data karyawan ke format PDF/Excel. Termasuk **View Khusus Kinerja + Absensi** yang menyatukan foto, lokasi GPS, dan teks Laporan Harian.
- [ ] **Fitur Manager (Data Pribadi):** Manager dapat melihat detail data pribadi dan dokumen dari karyawan bawahannya.
- [ ] **Visualisasi Grafik (Chart):** Menggunakan pustaka riil (misal Chart.js / ApexCharts) di Dashboard Admin untuk menggantikan SVG statis/mockup.
- [ ] **Fitur Edit / Koreksi Absensi:** Halaman khusus bagi HRD untuk mengubah data absensi (jika karyawan lupa clock out atau error sistem).

**Modul Karyawan & Umum**
- [ ] **Integrasi Rich Text Editor:** Menerapkan *editor* (seperti Quill/Summernote) untuk kolom Laporan Harian pada saat Karyawan melakukan absen.
- [ ] **Sistem Notifikasi:** Lonceng notifikasi di pojok kanan atas belum tersambung dengan sistem alert backend.
- [ ] **Layout Mobile (Responsif):** Perbaikan tampilan khusus akses lewat HP untuk semua panel.
  - [ ] **Testing:** Uji coba akurasi deteksi lokasi GPS absensi langsung menggunakan handphone saat tampilan mobile sudah selesai.

### 🚀 Rencana Masa Depan (Future Features)
- [ ] **Sistem Pengajuan Lembur (Surat Perintah Lembur):** Fitur pengajuan jam ekstra yang memerlukan approval atasan agar sah dihitung sebagai uang lembur.