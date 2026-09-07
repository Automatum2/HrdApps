# Comprehensive Multi-Role Feature Testing Plan (HRDApps)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Menyiapkan panduan dan daftar pengujian fitur secara komprehensif untuk seluruh role pada aplikasi HRDApps sebelum/saat tahap hosting.

**Architecture:** Pengujian berurutan (sequential role-based testing) mulai dari Super Admin, HR Manager, Manager Depa1rtemen, Karyawan, hingga Publik/Pelamar.

**Tech Stack:** Laravel 11, Blade, MySQL/MariaDB, TailwindCSS/Vanilla CSS, Mailer (SMTP).

---

### 1. Role: Super Admin (`username: superadmin` / `password: password`)

### Fitur 1.1: Dashboard Super Admin
- **URL:** `/backoffice/dashboard`
- **Role Terlibat:** Super Admin
- **Checklist Pengujian:**
  - [x] 1.1.1 Login sebagai `superadmin` (`password: password`).
  - [x] 1.1.2 Buka halaman Dashboard (`/backoffice/dashboard`).
  - [ -] 1.1.3 Periksa widget statistik: total karyawan, total akun HR, total departemen, dan absensi global hari ini. harus test absensi dulu
- **Indikator Keberhasilan:** Seluruh ringkasan statistik tampil presisi tanpa error 500/403.

### Fitur 1.2: Kelola Jabatan & Tunjangan Jabatan (Master Position)
- **URL:** `/backoffice/posisi`
- **Role Terlibat:** Super Admin (Akses eksklusif)
- **Checklist Pengujian:**
  - [x] 1.2.1 Buka halaman Kelola Jabatan (`/backoffice/posisi`).
  - [ ] 1.2.2 Tambah Jabatan Baru (misal: "Lead Engineer", level: "staff", tunjangan: Rp 1.000.000).
  - [ ] 1.2.3 Edit Jabatan yang ada (ubah nama jabatan atau nominal tunjangan).
  - [ ] 1.2.4 Hapus Jabatan yang tidak terpakai.
- **Indikator Keberhasilan:** Data jabatan berhasil ditambah, diubah, dan dihapus dari database.

### Fitur 1.3: Kelola Akun HR & Manager (Manajemen Akun HR)
- **URL:** `/backoffice/super-admin/kelola-hr`
- **Role Terlibat:** Super Admin & HR Manager
- **Checklist Pengujian:**
  - [ ] 1.3.1 Buka halaman Kelola HR & Manager (`/backoffice/super-admin/kelola-hr`).
  - [ ] 1.3.2 Buat Akun HR / Manager baru.
  - [ ] 1.3.3 Edit data profil / email / role akun HR.
  - [ ] 1.3.4 Hapus akun HR / Manager.
- **Indikator Keberhasilan:** Akun berhasil dibuat dan bisa digunakan untuk login sesuai role yang ditetapkan.

### Fitur 1.4: Kelola Karyawan (Global Employee Directory)
- **URL:** `/backoffice/super-admin/kelola-karyawan`
- **Role Terlibat:** Super Admin
- **Checklist Pengujian:**
  - [ ] 1.4.1 Buka halaman Kelola Karyawan (`/backoffice/super-admin/kelola-karyawan`).
  - [ ] 1.4.2 Lihat daftar seluruh karyawan dari semua departemen.
  - [ ] 1.4.3 Klik tombol **Detail** untuk melihat profil lengkap karyawan.
  - [ ] 1.4.4 Edit data karyawan (NIK, Nama, Email, Status).
  - [ ] 1.4.5 Hapus data karyawan (opsional).
- **Indikator Keberhasilan:** Data seluruh karyawan dari berbagai departemen dapat dikelola secara penuh.

### Fitur 1.5: Monitoring Absensi Global
- **URL:** `/backoffice/super-admin/absensi`
- **Role Terlibat:** Super Admin & Karyawan
- **Checklist Pengujian:**
  - [ ] 1.5.1 Buka halaman Monitoring Absensi Global (`/backoffice/super-admin/absensi`).
  - [ ] 1.5.2 Filter absensi berdasarkan rentang tanggal, departemen, dan status kehadiran.
  - [ ] 1.5.3 Periksa apakah absensi seluruh karyawan perusahaan dari semua departemen muncul dengan benar.
- **Indikator Keberhasilan:** Data absensi lintas departemen tampil secara transparan dan akurat.

---

## 2. Role: HR Manager (`username: hrmanager` / `password: password`)

### Fitur 2.1: Screening & Review CV Pelamar (Rekrutmen)
- **URL:** `/backoffice/cv`
- **Role Terlibat:** HR Manager / Manager Departemen (Pengirim: Pelamar via `/karir`)
- **Langkah Pengujian:**
  1. Buka menu Review CV.
  2. Klik ikon/tombol "Lihat CV / Dokumen" atau URL CV pelamar (pastikan modal CV bersih tanpa kotak putih berlebih).
  3. Klik **Terima CV** -> Masukkan Departemen, Jabatan, Gaji Pokok -> Simpan.
  4. Cek email pelamar: pastikan email penerimaan dengan link aktivasi akun & OTP terkirim.
  5. Klik **Tolak CV** pada pelamar lain -> Masukkan Alasan Penolakan -> Simpan.
  6. Cek email pelamar: pastikan email penolakan terkirim via `CVRejectedMail`.
- **Indikator Keberhasilan:** Status CV berubah, email terkirim via SMTP, dan data karyawan baru terbentuk saat disetujui.

### Fitur 2.2: Pengaturan Gaji, Tunjangan & Potongan Karyawan
- **URL:** `/backoffice/karyawan/{id}/detail`
- **Role Terlibat:** HR Manager / Super Admin
- **Langkah Pengujian:**
  1. Masuk ke Detail Karyawan.
  2. Update Gaji Pokok Karyawan.
  3. Tambah Tunjangan kustom (nama tunjangan & nominal).
  4. Tambah Potongan kustom (BPJS, PPh21, dll).
  5. Edit / Hapus tunjangan & potongan.
- **Indikator Keberhasilan:** Rincian tunjangan dan potongan tersimpan dan otomatis dihitung pada kalkulasi penggajian.

### Fitur 2.3: Pemrosesan & Approval Penggajian (Payroll)
- **URL:** `/backoffice/penggajian`
- **Role Terlibat:** HR Manager & Karyawan
- **Langkah Pengujian:**
  1. Buat Periode Penggajian Baru (misal: September 2026).
  2. Klik **Generate Payroll** -> Periksa apakah slip gaji terbuat untuk seluruh karyawan aktif.
  3. Klik **Approve** per karyawan atau **Approve Semua**.
  4. Cetak/Download Massal Slip Gaji PDF.
- **Indikator Keberhasilan:** Slip gaji berhasil diproses, di-approve, dan PDF dapat diunduh.

### Fitur 2.4: Manajemen Training & Pelatihan Karyawan
- **URL:** `/backoffice/training`
- **Role Terlibat:** HR Manager & Manager Departemen
- **Langkah Pengujian:**
  1. Tambah Jadwal Training (Nama Training, Karyawan Terlibat, Tanggal, Lokasi).
  2. Lihat daftar training aktif dan selesai.
  3. Hapus data training jika ada pembatalan.
- **Indikator Keberhasilan:** Training terdaftar dan notifikasi terkirim ke karyawan terkait.

### Fitur 2.5: Approval Cuti / Izin Karyawan
- **URL:** `/backoffice/leaves`
- **Role Terlibat:** HR Manager / Manager Departemen & Karyawan (Pengaju)
- **Langkah Pengujian:**
  1. Buka daftar pengajuan cuti/izin.
  2. Klik **Setujui (Approve)** atau **Tolak (Reject)** beserta catatan.
- **Indikator Keberhasilan:** Status cuti berubah dan saldo cuti/status absensi ter-update.

### Fitur 2.6: Laporan Rekapitulasi & Kinerja
- **URL:** `/backoffice/laporan` dan `/backoffice/laporan/kinerja`
- **Role Terlibat:** HR Manager
- **Langkah Pengujian:**
  1. Generate Laporan Bulanan (Absensi, Penggajian, Karyawan).
  2. Download file Laporan.
  3. Cek visualisasi Laporan Kinerja Karyawan.
- **Indikator Keberhasilan:** File laporan terunduh dengan data yang valid.

---

## 3. Role: Manager Departemen (`username: managerit` / `password: password`)

### Fitur 3.1: Dashboard & Monitoring Departemen IT
- **URL:** `/backoffice/dashboard`
- **Role Terlibat:** Manager Departemen
- **Langkah Pengujian:**
  1. Login sebagai `managerit`.
  2. Periksa statistik yang tampil (hanya mencakup anggota departemen IT).
- **Indikator Keberhasilan:** Data terbatas sesuai lingkup departemen IT (isolated per-department).

### Fitur 3.2: Lepas & Assign Karyawan Departemen
- **URL:** `/backoffice/karyawan`
- **Role Terlibat:** Manager Departemen & Karyawan
- **Langkah Pengujian:**
  1. Klik "Lepas dari Departemen" untuk merelokasi karyawan.
  2. Klik "Assign Departemen" untuk memasukkan karyawan ke departemen IT.
- **Indikator Keberhasilan:** Karyawan berpindah status departemen dengan benar.

### Fitur 3.3: Edit & Export Data Absensi Departemen
- **URL:** `/backoffice/absensi` dan `/backoffice/absensi/export`
- **Role Terlibat:** Manager Departemen
- **Langkah Pengujian:**
  1. Lihat absensi staf di departemennya.
  2. Koreksi jam masuk / jam keluar / status absensi jika ada kesalahan input.
  3. Klik tombol **Export CSV/Excel**.
- **Indikator Keberhasilan:** Data absensi ter-update dan file CSV absensi departemen berhasil diunduh.

---

## 4. Role: Karyawan (`username: karyawan` / `password: password`)

### Fitur 4.1: Absensi Harian (Clock In & Clock Out)
- **URL:** `/attendance`
- **Role Terlibat:** Karyawan
- **Langkah Pengujian:**
  1. Login sebagai `karyawan`.
  2. Buka halaman Absensi.
  3. Klik **Clock In** (Masuk) -> Cek jam masuk tercatat.
  4. Klik **Clock Out** (Keluar) -> Cek total jam kerja dihitung otomatis.
- **Indikator Keberhasilan:** Absensi harian tersimpan dengan jam presisi.

### Fitur 4.2: Pengajuan Cuti / Izin Online
- **URL:** `/attendance` (Tab/Form Pengajuan Cuti)
- **Role Terlibat:** Karyawan & HR/Manager (Approver)
- **Langkah Pengujian:**
  1. Isi Form Cuti (Jenis: Cuti/Izin/Sakit, Tanggal Mulai, Tanggal Selesai, Alasan).
  2. Submit pengajuan.
  3. Cek status di daftar pengajuan (Status: Pending).
- **Indikator Keberhasilan:** Pengajuan muncul di halaman approval HR Manager / Manager Departemen.

### Fitur 4.3: Download Slip Gaji PDF Pribadi
- **URL:** `/backoffice/penggajian`
- **Role Terlibat:** Karyawan
- **Langkah Pengujian:**
  1. Buka menu Penggajian.
  2. Lihat riwayat gajinya sendiri (tidak bisa melihat gaji orang lain).
  3. Klik **Download Slip Gaji (PDF)**.
- **Indikator Keberhasilan:** File PDF Slip Gaji terunduh dengan rincian gaji pokok, tunjangan, dan potongan yang sesuai.

### Fitur 4.4: Pengaturan Profil & Upload Dokumen CV/Foto
- **URL:** `/backoffice/pengaturan`
- **Role Terlibat:** Karyawan
- **Langkah Pengujian:**
  1. Ubah Password Akun.
  2. Update Informasi Bank (Nama Bank, Nomor Rekening, Atas Nama).
  3. Upload Foto Profil / Dokumen Tambahan.
- **Indikator Keberhasilan:** Data profil dan rekening bank berhasil diperbarui.

---

## 5. Role: Pelamar / Publik (Tanpa Auth / Halaman Karir)

### Fitur 5.1: Form Pengajuan Lamaran Pekerjaan (Submit CV)
- **URL:** `/karir`
- **Role Terlibat:** Publik / Pelamar & HR Manager (Penerima)
- **Langkah Pengujian:**
  1. Akses halaman public `/karir`.
  2. Isi Nama, Email, Status Kerja yang dilamar, Teks CV / Upload File CV (PDF/DOCX) / URL LinkedIn.
  3. Submit Lamaran.
- **Indikator Keberhasilan:** Pesan sukses muncul dan data pelamar masuk ke menu `/backoffice/cv` milik HR Manager.

### Fitur 5.2: Aktivasi Akun & Set Password (Link Email & OTP)
- **URL:** `/reset-password/{token}?email=...&type=activation`
- **Role Terlibat:** Pelamar Diterima & Sistem Email
- **Langkah Pengujian:**
  1. Pelamar membuka link aktivasi dari email penerimaan.
  2. Input Kode OTP 6 digit yang ada di email + Password Baru.
  3. Submit Form Aktivasi.
- **Indikator Keberhasilan:** Akun aktif, password terpasang, dan pelamar dapat login sebagai Karyawan.

### Fitur 5.3: Lupa Password & Verifikasi OTP
- **URL:** `/forgot-password` -> `/verify-otp` -> `/reset-password`
- **Role Terlibat:** Seluruh Role Karyawan/User & Sistem Email
- **Langkah Pengujian:**
  1. Buka `/forgot-password`, masukkan email terdaftar.
  2. Cek email untuk kode OTP 6 digit.
  3. Masukkan OTP di `/verify-otp`.
  4. Buat password baru di `/reset-password`.
- **Indikator Keberhasilan:** Password ter-reset dan user bisa login menggunakan password baru.
