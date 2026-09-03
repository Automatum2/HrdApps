# Spesifikasi Desain: Perombakan Wewenang Super Admin & Promosi Manager Internal

## 1. Ringkasan Tujuan
Dokumen ini mengatur perombakan hak akses dan wewenang **Super Admin** di HRDApps agar fokus pada pengelolaan Master Sistem (Manager & Jabatan/Tunjangan), serta menyempurnakan alur pendaftaran Manager melalui mekanisme **Promosi Karyawan Internal (Auto-detect)**.

---

## 2. Rincian Perubahan Wewenang Role

### A. Super Admin
* **Tanggung Jawab Utama:**
  1. Pengelolaan Akun Manager (`HR Manager` & `Manager Departemen`).
  2. Pengelolaan Master Jabatan / Posisi dan Tunjangan Jabatan.
  3. Pengelolaan Master Departemen.
* **Pembatasan (Restriksi):**
  - **TIDAK** mengakses/mengulas CV Pelamar di portal karir (`/backoffice/cv`).
  - **TIDAK** menambah akun karyawan biasa secara langsung (fitur `Tambah Karyawan` di Super Admin dihapus). Halaman `/backoffice/super-admin/kelola-karyawan` hanya berfungsi sebagai peninjauan data/edit posisi.

### B. HR Manager
* **Tanggung Jawab Utama:**
  1. Pengelolaan alur Rekrutmen Publik & Review CV Pelamar (`/backoffice/cv`).
  2. Penerimaan karyawan baru (Magang/Kontrak/Tetap).
  3. Pengelolaan data operasional karyawan dan penyesuaian tunjangan individual.
  4. Pengloalaan training karyawan

---

## 3. Fitur Promosi Karyawan ke Manager (Modal Tambah Manager)

Pada modal **"Tambah Manager"** di rute `/backoffice/super-admin/kelola-hr`, disediakan 2 tab/opsi:

### Opsi 1: Promosi Karyawan Internal (Default)
* **Input:** Pilihan Dropdown / Autocomplete Email / NIK Karyawan yang ada.
* **Auto-detect:**
  - `nama` dan `nik` terisi otomatis dari data `employees` terpilih.
* **Action:**
  - Memperbarui `role` user terkait di tabel `users` menjadi `manager_departemen` atau `hr_manager`.
  - Memperbarui `position_id` dan `department_id` di tabel `employees`.
  - Mencegah duplikasi record karyawan di database.

### Opsi 2: Manager Eksternal (Pengangkatan Baru)
* **Input:** NIK, Nama Lengkap, Email, Peran, Departemen, dan Jabatan (manual).
* **Action:** Membuat record `Employee` baru & `User` baru.

---

## 4. Alur Tunjangan Jabatan Manager
* Saat seorang karyawan dipromosikan ke jabatan Manager, tunjangan jabatan secara otomatis merujuk pada `tunjangan_jabatan` di tabel `positions`.
* Tunjangan individual tambahan tetap dapat diatur via modul `Allowance`.

---

## 5. Rencana Verifikasi & Testing
1. **Penyekatan Akses:**
   - Coba akses `/backoffice/cv` menggunakan akun Super Admin -> Memastikan akses ditolak / dialihkan (403 Forbidden).
2. **Promosi Karyawan:**
   - Pilih karyawan biasa (contoh: Email `jaya@email.com`), promosikan menjadi Manager Departemen IT.
   - Pastikan NIK tetap sama, role akun berubah menjadi `manager_departemen`, dan tidak ada record ganda di tabel `employees`.
