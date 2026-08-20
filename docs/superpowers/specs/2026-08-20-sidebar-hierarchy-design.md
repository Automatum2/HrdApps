# Design: Sidebar & Hierarki Akses HR Manager vs Manager Departemen

## 1. Tujuan
Membedakan dengan tegas hak akses menu (Sidebar) dan visibilitas data antara peran `hr_manager` dan `manager_departemen` untuk memastikan keamanan data dan meminimalkan distraksi antarmuka.

## 2. Peta Hierarki Sidebar (`resources/views/layouts/admin.blade.php`)

Grup menu yang saat ini disatukan akan dipisah menjadi dua blok berbeda secara eksplisit.

### A. Sidebar untuk `hr_manager`
Sebagai administrator sumber daya manusia, HR Manager dapat melihat seluruh menu:
- **Dashboard** (Global)
- **Karyawan** (Master data seluruh perusahaan)
- **Trainer / Pelatihan**
- **Kelola CV**
- **Absensi** (Pemantauan absensi seluruh perusahaan)
- **Persetujuan Cuti** (Validasi akhir cuti seluruh perusahaan)
- **Penggajian**
- **Pengaturan** (Bila ada hak akses)

### B. Sidebar untuk `manager_departemen`
Sebagai kepala lini, fokus utama manajer adalah produktivitas timnya sendiri.
- **Dashboard** (Khusus data Departemen - sudah diimplementasikan)
- **Karyawan** (Dibatasi hanya melihat list karyawan di departemennya)
- **Absensi** (Hanya memantau absensi anggota departemennya dan absensi diri sendiri)
- **Persetujuan Cuti** (Hanya cuti anggota departemennya)
- **Penggajian** (Hanya data gajinya sendiri)
- *Menu "Trainer" dan "Kelola CV" dihilangkan seluruhnya.*

## 3. Isolasi Data di Controller (Pendekatan 1)
Data akan difilter secara langsung pada *Controller* terkait, dengan mendeteksi *role* pengguna:

1. **KaryawanController (`index`)**:
   - Jika `manager_departemen`, berikan perintah query `.where('department_id', Auth::user()->employee->department_id)`. Akses fitur tambah/ubah dinonaktifkan (di-hide di view).
2. **AbsensiController (`index`)**:
   - Jika `manager_departemen`, batasi `$query->whereHas('employee', function($q){ ... })`.
3. **LeaveController (`index`)**:
   - Jika `manager_departemen`, batasi daftar pengajuan cuti hanya untuk karyawan dengan `department_id` yang sama.

## 4. Pertimbangan UI (Views)
- Saat `manager_departemen` membuka halaman "Karyawan", tombol "Tambah Karyawan Baru" harus disembunyikan.
- Kolom "Aksi" pada tabel (Edit/Hapus) disembunyikan atau didisable untuk `manager_departemen` karena mereka hanya memiliki hak akses baca (*Read-only*).

## 5. Keamanan
Pemisahan hak akses (*authorization*) ini mengandalkan pengecekan `if ($role === 'manager_departemen')` langsung pada logika awal setiap Controller (bukan sekadar di-*hide* dari HTML/Sidebar), mencegah manipulasi langsung dari pencarian *URL* rute.
