# Design Spec: Alur Rekrutmen & Onboarding Karyawan Baru

## 1. Tujuan
Memisahkan tanggung jawab antara HR Manager (penyeleksi CV & wawancara) dan Superadmin (pembuat akun), sehingga alur rekrutmen berjalan secara profesional dan aman. Karyawan juga dapat mengaktifkan akunnya sendiri (membuat password) via email.

## 2. Alur Sistem (Workflow)

### Tahap 1: Penilaian CV & Wawancara (HR Manager)
1. Pelamar mengisi form publik di `/karir`. Data masuk ke tabel `employees` dengan status rekrutmen: `menunggu_wawancara`.
2. **HR Manager** masuk ke menu **Kelola CV**.
3. Di daftar tersebut, terdapat tabel pelamar yang statusnya "Belum Diwawancara" (atau `menunggu_wawancara`).
4. HR Manager mengklik CV untuk melihat detail (dokumen). Setelah proses wawancara di dunia nyata selesai, HR Manager memberikan keputusan di sistem:
   - Tombol **[Lolos]**: Status pelamar berubah menjadi `lolos_wawancara`.
   - Tombol **[Tolak]**: Status pelamar menjadi `ditolak` (sistem akan mengirimkan email penolakan).
*Pada tahap ini, akun User belum dibuat.*

### Tahap 2: Registrasi Akun (Superadmin)
1. **Superadmin** masuk ke menu **Kelola Karyawan**.
2. Tersedia tab/tombol khusus **"Daftarkan Karyawan Baru (Lolos Wawancara)"**.
3. Superadmin melihat daftar pelamar yang statusnya `lolos_wawancara`.
4. Superadmin mengklik tombol **[Buat Akun]** untuk seorang kandidat.
   - Sistem membaca Nama dan Email aktif dari data CV.
   - Sistem *membuatkan* baris di tabel `users` untuk kandidat tersebut.
   - Status pelamar di tabel `employees` berubah menjadi `karyawan_baru` (atau `aktif`).
   - Sistem mengirim **Email Aktivasi** yang berisi link bagi karyawan untuk membuat *password* mereka sendiri.

### Tahap 3: Aktivasi & Penempatan (Karyawan & HR)
1. **Karyawan** membuka email, mengklik link, dan diarahkan ke halaman pembuatan password. Setelah selesai, mereka bisa login.
2. **HR Manager** masuk ke menu **Kelola Karyawan**. Di sana, mereka akan melihat karyawan baru yang belum memiliki departemen.
3. HR Manager mengubah profil karyawan tersebut untuk **menempatkan mereka ke departemen** dan jabatan yang sesuai dengan skill di CV mereka.

## 3. Perubahan Arsitektur & Database
- Modifikasi tabel `employees`:
  - Tambah enum status (atau ubah logika `is_cv_approved`) untuk mencakup tahapan: `menunggu_wawancara`, `lolos_wawancara`, `ditolak`.
- Modifikasi Controller `CVController`:
  - Pisahkan fungsi pembuatan akun user dan pengiriman email aktivasi, hapus dari sini.
- Pembuatan fitur di `EmployeeController` (untuk Superadmin):
  - Fitur mengambil daftar `lolos_wawancara` dan mendaftarkan mereka menjadi `User`.
- Pembuatan sistem **Setup Password**:
  - Memanfaatkan sistem Reset Password bawaan Laravel untuk link aktivasi.
