# Design: Dashboard Khusus Manager Departemen

## Tujuan
Memisahkan logika dan antarmuka Dashboard untuk pengguna dengan role `manager_departemen` agar data statistik (Total Karyawan, Kehadiran, Gaji, dll.) secara ketat terisolasi dan hanya menampilkan data karyawan di bawah departemennya sendiri. Hal ini ditujukan untuk pemeliharaan jangka panjang agar dashboard manajer dapat dikembangkan lebih lanjut tanpa mengganggu dashboard HR.

## 1. Arsitektur & Logika (Routing)
Karena logika dashboard saat ini sebagian besar berada dalam bentuk Closure di `routes/web.php`:
- Kita akan membuat blok kondisi khusus (`elseif ($role === 'manager_departemen')`) di dalam rute `/backoffice/dashboard`.
- Mengambil ID departemen dari data manager yang sedang login (`Auth::user()->employee->department_id`).
- Menggunakan ID departemen tersebut sebagai filter wajib (`where('department_id', ...)`) untuk semua perhitungan query (Total Karyawan, Kehadiran, Belum Absen, dan Total Gaji Bulan Ini).

## 2. Antarmuka (View)
- Membuat file view baru: `resources/views/backoffice/dashboard_manager.blade.php`.
- View ini akan diduplikasi dari `dashboard.blade.php` saat ini, tetapi teks pada widget akan disesuaikan agar tidak ambigu (misal: "Total Karyawan Departemen" daripada sekadar "Total Karyawan").
- Menghapus komponen-komponen yang secara eksklusif merupakan wewenang HR (misalnya widget/tabel untuk menempatkan (assign) karyawan baru yang belum memiliki departemen).

## 3. Komponen Data
Data yang akan dikirimkan ke view `dashboard_manager` meliputi:
1. **Total Karyawan Departemen**: Count `Employee` di departemen terkait.
2. **Hadir Hari Ini**: Count `Attendance` hari ini untuk karyawan di departemen terkait.
3. **Belum Absen**: Hasil pengurangan (Total Karyawan Departemen - Hadir Hari Ini).
4. **Total Gaji Departemen**: Sum dari kalkulasi payroll khusus karyawan departemen.
5. **Daftar Karyawan Terbaru**: List 5 karyawan terakhir di departemen.
6. **Grafik Kehadiran**: Tren mingguan yang disesuaikan hanya untuk departemen.

## 4. Keamanan
Semua query yang berjalan pada dashboard manajer akan di-hardcode dengan `.where('department_id', $department_id)` untuk mencegah kebocoran data antar departemen jika terjadi manipulasi parameter atau ketidaksengajaan.
