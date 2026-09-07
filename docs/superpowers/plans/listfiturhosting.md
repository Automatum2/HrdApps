# Daftar Temuan & Rekomendasi Perbaikan Environment Hosting (https://www.hrdapps.id)

> **Catatan untuk Senior Developer:**
> Dokumen ini berisi daftar perbedaan perilaku, kendala tampilan (UI/CSS), dan error teknis antara Environment Lokal dan Environment Hosted (Production) pada domain `https://www.hrdapps.id`. Source code project tidak diubah.

---

## 1. Masalah Utama Environment & Build Asset (Global)

### A. Utility Tailwind `bg-primary` & Color Tokens Tidak Ter-render di Build Production
- **Deskripsi:** Semua tombol utama yang menggunakan class `bg-primary` (seperti tombol *+ Tambah Jabatan*, *+ Tambah Manager*, *+ Buat Periode*, *+ Buat Pelatihan*, *Generate Laporan*, dan tombol submit modal) tampil transparan/putih tanpa warna di hosting.
- **Penyebab:** Pada Tailwind v4 `@theme`, `--color-primary` tidak otomatis ter-map ke class `bg-primary` saat di-build untuk production via Vite (`npm run build`).
- **Rekomendasi Senior Dev:** 
  1. Ganti class `bg-primary` pada tombol-tombol utama Blade view menjadi class warna Tailwind standar (seperti `bg-blue-600 hover:bg-blue-700 text-white`).
  2. Atau definisikan utility kustom `@utility bg-primary { background-color: #0050cb; }` pada `resources/css/app.css`.

### B. Header Help Icon (`?`) Masih Terlihat
- **Deskripsi:** Tombol ikon tanda tanya (`?`) pada header `resources/views/layouts/admin.blade.php` (baris 366-368) belum dihapus.
- **Rekomendasi Senior Dev:** Hapus tag `<button>` bantuan tanda tanya tersebut dari layout header.

### C. Symlink Upload File (`storage:link`) pada Hosting
- **Deskripsi:** Berkas CV pelamar (`cv_files/`) dan foto absensi/profil karyawan tidak dapat dibuka / mengembalikan error 404 pada hosting.
- **Penyebab:** Folder `public/storage` belum di-symlink ke `storage/app/public` di server hosting.
- **Rekomendasi Senior Dev:** Jalankan `php artisan storage:link` di server hosting atau buat folder/symlink di cPanel File Manager.

---

## 2. Super Admin Role (`username: superadmin`)

- [ ] **Monitoring Absensi Global (`/backoffice/super-admin/absensi`)**:
  - Tombol **Terapkan Filter** transparan/putih (class `bg-primary`).
  - Kolom **Keterangan** menampilkan tag HTML mentah (misal: `<p>yes</p>`). Rekomendasi: bungkus dengan `strip_tags($att->keterangan)`.
- [ ] **Kelola Master Jabatan (`/backoffice/posisi`)**:
  - Tombol **+ Tambah Jabatan** di pojok kanan atas transparan.
  - Tombol **Simpan** pada Modal Tambah & Edit Jabatan transparan/putih.
- [ ] **Kelola HR & Manager (`/backoffice/super-admin/kelola-hr`)**:
  - Tombol **+ Tambah Manager** transparan.
  - Tombol **Simpan Manager** pada footer modal transparan.

---

## 3. HR Manager Role (`username: hrmanager`)

- [ ] **Review CV Pelamar (`/backoffice/cv`)**:
  - Tampilan tabel normal. File CV/URL membutuhkan symlink hosting (poin 1.C).
  - Pastikan pengiriman email (penerimaan/penolakan CV) menggunakan konfigurasi `MAIL_MAILER=smtp` aktif di `.env` hosting.
- [ ] **Penggajian (`/backoffice/penggajian`)**:
  - Tombol **+ Buat Periode Baru** transparan/putih.
  - Pada Detail Periode (`/backoffice/penggajian/periode/{id}`):
    - Tombol **Setujui & Distribusikan**, **Tarik Data & Hitung**, **Setujui Semua**, dan **Generate Slip** transparan/putih.
    - Badge angka tahapan (misal: `1. Tarik Data`) transparan.
- [ ] **Manajemen Pelatihan / Training (`/backoffice/training`)**:
  - Tombol **+ Buat Pelatihan** transparan/putih.
  - Modal pembuatan pelatihan tidak terbuka saat tombol diklik. Rekomendasi: Periksa event listener JS / fungsi `openModal()`.
- [ ] **Laporan (`/backoffice/laporan`)**:
  - Kartu header *Laporan Absensi* & *Laporan Penggajian* berwarna putih transparan sehingga teks judul kurang kontras.
  - Tombol **Generate** & **View Kinerja** transparan.
  - Menu sidebar **Laporan** tidak muncul untuk role `hr_manager` di `admin.blade.php` (hanya ada `@if($role === 'manager')`). Rekomendasi: Sesuaikan kondisi `@if(in_array($role, ['hr_manager', 'manager']))`.

---

## 4. Manager Departemen Role (`username: managerit`)

- [ ] **Dashboard & Karyawan Departemen (`/backoffice/karyawan`)**:
  - Tombol **Lepas dari Departemen** dan **Assign Karyawan** transparan/putih.
- [ ] **Absensi Departemen (`/backoffice/absensi`)**:
  - Tombol **Export CSV** transparan.
  - Jarak tombol kamera dan preview foto absensi membutuhkan penyesuaian tata letak responsive.

---

## 5. Karyawan Role (`username: karyawan`)

- [ ] **Halaman Presensi (`/attendance`)**:
  - Membutuhkan izin akses kamera browser (HTTPS wajib di-enforce).
  - Gambar hasil tangkapan webcam tidak muncul jika folder storage belum di-symlink.
- [ ] **Download Slip Gaji PDF (`/backoffice/penggajian`)**:
  - Pastikan extension PHP `dompdf` / `gd` / `mbstring` aktif di PHP hosting agar ekspor PDF tidak error 500.

---

## 6. Public Career Page (`/karir`)

- [ ] **Form Submit CV (`/karir`)**:
  - Input file CV (`cv_file`) dan URL LinkedIn (`cv_url`) berfungsi dengan baik.
  - Setelah disubmit, file tersimpan di `storage/app/public/cv_files/` yang membutuhkan `storage:link` agar HR Manager dapat mengunduhnya dari dashboard backoffice.