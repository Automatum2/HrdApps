# Konsep Dasar Sistem HRDApps
## Sistem Penggajian Digital Karyawan

---

## 1. Latar Belakang

HRDApps adalah sistem informasi Human Resource Development berbasis web yang bertujuan untuk mendigitalisasi proses pengelolaan karyawan, absensi, dan **penggajian**. Saat ini, website https://hrdapps.id sudah memiliki fitur dasar login dan tracking performa/absensi. Project ini bertujuan untuk **mengembangkan fitur penggajian digital** agar HRD dapat membagi gaji karyawan secara digital.

---

## 2. Tujuan Project

1. Mengembangkan modul **penggajian digital** pada sistem HRDApps yang sudah ada
2. Menyediakan fitur **generate slip gaji otomatis** berdasarkan data kehadiran
3. Memudahkan HRD dalam menghitung dan mendistribusikan gaji karyawan
4. Menyediakan **laporan penggajian** yang terstruktur dan dapat diexport

---

## 3. Teknologi yang Digunakan

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 10/11 (PHP 8.2+) |
| Frontend | Tailwind CSS + Custom CSS |
| Database | MySQL |
| JavaScript | Vanilla JS / Alpine.js |
| Font | Google Fonts - Hanken Grotesk & Inter |
| Icons | Material Symbols Outlined |
| Hosting | Hostinger |

> Website menggunakan stack ini, sehingga pengembangan fitur baru harus konsisten dengan teknologi yang sama.

---

### 4.1 Super Admin (Baru)
- Mengelola data HRD Manager (CRUD)
- Mengelola data Karyawan (CRUD - soft delete & setting Gaji Pokok)
- Tidak memiliki akses ke operasional absensi, penggajian, dan laporan bulanan

### 4.2 Admin / HRD (Manager)
- Melihat dan memfilter data karyawan (Read-Only dengan ekspor Excel, serta memiliki fungsi "Tambah Karyawan" untuk memasukkan karyawan terdaftar secara global ke dalam departemen kelolaan Manager)
- Mengelola dan mengedit absensi karyawan
- **Memproses penggajian** (fitur utama, mencakup perhitungan komponen gaji, review, approval, dan generate slip)
- Generate laporan (absensi dan penggajian)

### 4.3 Karyawan
- Akses terbatas pada panel personal (Dashboard Absensi Saya)
- Melakukan Clock In / Clock Out absensi dengan timestamp & status kerja
- **Melihat dan download slip gaji sendiri** (menggunakan visual slip ter-update)

---

## 5. Modul / Fitur Sistem

### 5.1 Modul Login & Autentikasi
- Form login dengan username, password, dan status kerja
- Session management
- Role-based access (Super Admin, Admin, dan Karyawan)

### 5.2 Modul Dashboard
**Dashboard Admin:**
- Statistik total karyawan, kehadiran hari ini, belum absen
- Total gaji bulan berjalan
- Chart rekap kehadiran dan status karyawan
- Tabel karyawan terbaru

**Dashboard Karyawan:**
- Status kehadiran hari ini
- Jam masuk/keluar
- Ringkasan slip gaji terakhir
- Tombol Clock In / Clock Out

### 5.3 Modul Manajemen Karyawan
- Daftar karyawan dengan search, filter, pagination
- Pendaftaran karyawan baru tingkat dasar (Nama Lengkap, Alamat Email, dan Gaji Pokok - dikelola oleh Super Admin)
- Penempatan karyawan baru yang terdaftar global ke departemen HRD dengan pengisian Jabatan Baru dan memilih Status Hubungan Kerja (dikelola oleh HRD Manager secara dinamis)
- Edit data karyawan
- Lihat detail karyawan
- Hapus karyawan
- Export data ke Excel

### 5.4 Modul Absensi
- Rekap kehadiran semua karyawan (Admin)
- Filter berdasarkan tanggal, departemen, status
- Clock In/Out dengan timestamp (Karyawan)
- Status kehadiran: Hadir, Izin, Sakit, Alpha, Cuti
- Status kerja: WFO, WFH, WFF, WOD, WEH
- Hitung total jam kerja dan lembur otomatis
- Export rekap absensi

### 5.5 Modul Penggajian (Fitur Utama)

#### Komponen Gaji:

**Pendapatan:**
| Komponen | Keterangan |
|----------|------------|
| Gaji Pokok | Berdasarkan jabatan/kontrak |
| Tunjangan Transport | Tunjangan tetap bulanan |
| Tunjangan Makan | Tunjangan tetap bulanan |
| Tunjangan Jabatan | Berdasarkan level jabatan |
| Lembur | Dihitung dari jam lembur x tarif |

**Potongan:**
| Komponen | Keterangan |
|----------|------------|
| BPJS Kesehatan | Potongan wajib |
| BPJS Ketenagakerjaan | Potongan wajib |
| PPh 21 | Pajak penghasilan |
| Potongan lain | Pinjaman, keterlambatan, dll |

#### Alur Proses Penggajian:
1. **Buat Periode** - HRD membuat periode gaji baru (misal: Juni 2026)
2. **Tarik Data Kehadiran** - Sistem menarik data absensi periode tersebut
3. **Hitung Gaji** - Sistem menghitung gaji berdasarkan komponen
4. **Review** - HRD mereview detail gaji setiap karyawan
5. **Approve** - HRD menyetujui perhitungan gaji
6. **Generate Slip** - Sistem membuat slip gaji digital
7. **Distribusi** - Slip gaji tersedia di dashboard karyawan / kirim email / download PDF

### 5.6 Modul Laporan
- Laporan Absensi (per bulan/periode)
- Laporan Penggajian (per bulan/periode)
- Laporan Data Karyawan
- Export ke PDF dan Excel

---

## 6. Formula Perhitungan Gaji

```
Gaji Kotor = Gaji Pokok + Total Tunjangan + Upah Lembur

Upah Lembur = Jam Lembur x (Gaji Pokok / 173) x 1.5
  (173 = rata-rata jam kerja per bulan)

Total Potongan = BPJS Kesehatan + BPJS TK + PPh 21 + Potongan Lain

Gaji Bersih = Gaji Kotor - Total Potongan
```

---

## 7. Struktur Database

Lihat detail ERD di file: `docs/erd.md`

Tabel utama:
- `users` - Data login
- `employees` - Data karyawan
- `departments` - Departemen
- `positions` - Jabatan
- `attendances` - Absensi
- `payroll_periods` - Periode gaji
- `payrolls` - Header gaji
- `payroll_details` - Detail komponen gaji
- `allowances` - Master tunjangan
- `deductions` - Master potongan

---

## 8. Struktur Halaman

Lihat detail sitemap di file: `docs/sitemap.md`

### Panel Super Admin (3 halaman utama):
1. Dashboard Super Admin
2. Kelola HRD Manager (CRUD)
3. Kelola Karyawan (CRUD & Setup Gaji Pokok)

### Panel Admin (9 halaman):
1. Dashboard Admin
2. Daftar Karyawan (Tampilan Data & Filter, tanpa CRUD)
3. Rekap Absensi
4. Daftar Periode Gaji
5. Proses Penggajian (Tarik data, Hitung, Review, Approve, Distribusi)
6. Preview Slip Gaji
7. Riwayat Penggajian
8. Laporan & Rekap
9. Pengaturan (dilengkapi Logout)

### Panel Karyawan (2 halaman utama/terintegrasi):
1. Dashboard Absensi Saya (Clock In/Out, Ringkasan Bulanan, Kalender Kehadiran)
2. Slip Gaji Personal (Preview & Cetak)

---

## 9. Desain UI/UX

Semua mockup desain tersedia di folder `design/`:

| File | Halaman |
|------|---------|
| `01_dashboard_admin.png` | Dashboard Admin/HRD |
| `02_halaman_login.png` | Halaman Login |
| `03_halaman_karyawan.png` | Manajemen Karyawan |
| `04_halaman_absensi.png` | Rekap Absensi |
| `05_halaman_penggajian.png` | Proses Penggajian |
| `06_slip_gaji.png` | Slip Gaji Digital |
| `07_dashboard_karyawan.png` | Dashboard Karyawan |
| `08_halaman_laporan.png` | Laporan & Rekap |

---

## 10. Flowchart Sistem

Lihat detail flowchart di file: `docs/flowchart.md`

Flowchart yang tersedia:
1. Alur Utama Sistem
2. Alur Penggajian Digital (Fitur Utama)
3. Alur Login & Autentikasi
4. Alur CRUD Karyawan
5. Alur Absensi

---

## 11. Timeline Pengembangan (Estimasi)

| Minggu | Kegiatan |
|--------|----------|
| 1 | Analisis sistem existing, pembuatan konsep & desain |
| 2 | Setup environment lokal, setup database |
| 3-4 | Develop modul manajemen karyawan & departemen |
| 5-6 | Develop modul absensi |
| 7-9 | Develop modul penggajian (fitur utama) |
| 10 | Develop modul slip gaji & distribusi |
| 11 | Develop modul laporan |
| 12 | Testing, bug fixing, deployment |
