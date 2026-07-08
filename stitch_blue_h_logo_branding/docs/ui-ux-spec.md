
# UI/UX Design Specification - HRDApps

## Design System

### Warna Utama (Corporate Velocity)
| Nama | Hex | Penggunaan |
|------|-----|------------|
| Primary Blue | `#0050cb` | Tombol utama, link, brand identifier |
| Primary Container | `#0066ff` | Hover tombol utama, status aktif |
| Dark Slate Sidebar | `#545f73` | Background navigasi sidebar |
| Success Emerald | `#006645` / `#008259` | Status aktif, badge hadir, tombol approve |
| Warning Amber | `#F59E0B` | Badge izin/sakit, notifikasi warning |
| Danger Red / Error | `#ba1a1a` | Tombol hapus/nonaktifkan, badge alpha, error |
| Light Gray Background| `#f8f9ff` | Background halaman |
| White | `#ffffff` | Background card/konten |

### Typography
| Elemen | Font | Size | Weight |
|--------|------|------|--------|
| Display Large | Hanken Grotesk | 32px | 700 |
| Headline Medium | Hanken Grotesk | 24px | 600 |
| Title Small | Inter | 18px | 600 |
| Body Medium | Inter | 14px | 400 |
| Body Small | Inter | 13px | 400 |
| Table Header | Inter | 13px | 600 |
| Label Uppercase | Inter | 12px | 600 |

### Komponen UI
- **Card**: Border radius 12px (0.75rem), shadow ringan (`0px 10px 15px -3px rgba(0,0,0,0.05)`), border 1px solid #c2c6d8
- **Button & Input**: Border radius 8px (0.5rem), padding 8px 16px
- **Badge / Chip**: Border radius full pill (9999px) untuk status
- **Table**: 1px bottom border per baris (divide-y), hover effect dengan highlight biru sangat tipis, header dengan uppercase label dan background kontras ringan

---

## Spesifikasi Per Halaman

### 1. Halaman Login

**Layout**: Card tengah, background gradient biru
**Komponen**:
- Logo perusahaan (atas card)
- Judul "HRDApps" + subtitle
- Input username (icon user)
- Input password (icon lock)
- Dropdown status kerja (WFO/WFH/WFF/WOD/WEH)
- Tombol "Log In" (full width, biru)
- Footer "Powered by HRDApps.id"

**Interaksi**:
- Validasi kosong saat submit
- Loading spinner saat proses login
- Error message merah jika gagal

---

### 2. Dashboard Admin

**Layout**: Sidebar kiri + konten utama
**Komponen**:
- **Stats Cards** (4 kolom):
  - Total Karyawan (icon people, biru)
  - Hadir Hari Ini (icon check, hijau)
  - Belum Absen (icon warning, merah)
  - Gaji Bulan Ini (icon money, ungu)
- **Chart Area** (2 kolom):
  - Line chart rekap kehadiran bulanan
  - Donut chart status karyawan
- **Tabel Karyawan Terbaru** (full width):
  - Komponen: Tombol "+ Tambah Karyawan" (di kanan atas tabel) yang membuka modal pilihan karyawan global untuk di-assign ke departemen Manager.
  - Kolom: Nama, NIK, Jabatan, Department, Status, Action

**Interaksi**:
- Tombol "+ Tambah Karyawan": Membuka modal berisi dropdown karyawan terdaftar (dari database global) yang belum memiliki departemen, untuk kemudian dimasukkan ke departemen Manager ini.
- Cards clickable (navigasi ke halaman terkait)
- Chart interaktif (hover tooltip)
- Tabel dengan search dan pagination

---

### 3. Manajemen Karyawan (Admin Panel)

**Layout**: Sidebar + tabel full width (Tampilan Read-Only, tanpa operasi CRUD)
**Komponen**:
- Breadcrumb: Beranda > Manajemen Karyawan
- Tombol aksi: Export Excel (outline border)
- Filter dropdown: Jabatan, Departemen, Status
- Search bar
- Data table:
  - Kolom: No, Foto, Nama, NIK, Jabatan, Departemen, Status, Aksi (Aksi dibatasi untuk melihat detail, tanpa operasi penambahan/penghapusan)
- Pagination

**Interaksi**:
- Search real-time (ketik langsung filter)
- Badge status berwarna (Aktif=emerald, Nonaktif=abu-abu slate)
- *Catatan*: Seluruh fungsi penambahan, penyuntingan, dan penghapusan karyawan dikelola secara tersentralisasi pada Panel Super Admin.

---

### 4. Rekap Absensi

**Layout**: Sidebar + filter area + tabel
**Komponen**:
- Filter bar: Date range, Departemen, Status, tombol Export
- Calendar mini (pojok kanan atas)
- Stats cards berwarna: Hadir (hijau), Izin (kuning), Sakit (oranye), Alpha (merah), Cuti (biru)
- Data table:
  - Kolom: No, Nama, NIK, Tanggal, Jam Masuk, Jam Keluar, Status Kerja, Status Kehadiran, Total Jam, Lembur, Aksi

**Interaksi**:
- Filter otomatis saat pilih tanggal/status
- Badge berwarna untuk status kerja dan kehadiran
- Export langsung ke Excel

---

### 5. Proses Penggajian

**Layout**: Sidebar + progress bar + tabel
**Komponen**:
- Judul: "Proses Penggajian - Periode [bulan tahun]"
- Badge status: Draft / Diproses / Selesai
- **Step Progress Bar** (5 langkah):
  1. Tarik Data
  2. Hitung Gaji
  3. Review
  4. Approve
  5. Distribusi
- Card total: "Total Gaji Bersih: Rp xxx.xxx.xxx"
- Tombol aksi: Hitung Gaji (biru), Approve All (hijau), Generate Slip (ungu)
- Data table:
  - Kolom: No, Nama, Gaji Pokok, Tunjangan, Potongan, Lembur, Gaji Bersih, Status, Aksi
  - Aksi: Review, Edit

**Interaksi**:
- Progress bar berubah sesuai tahap
- Klik Review untuk lihat detail komponen gaji per karyawan
- Konfirmasi modal sebelum Approve
- Loading indicator saat proses hitung gaji

---

### 6. Slip Gaji

**Layout**: Card dokumen (seperti surat resmi)
**Komponen**:
- Header: Logo + "SLIP GAJI KARYAWAN" + Periode
- Info karyawan: Nama, NIK, Jabatan, Departemen, No Rekening
- 2 kolom:
  - Kiri: PENDAPATAN (Gaji Pokok, Tunjangan-tunjangan, Lembur, Subtotal)
  - Kanan: POTONGAN (BPJS, PPh 21, dll, Subtotal)
- Footer highlight: "GAJI BERSIH: Rp x.xxx.xxx" (background hijau)
- Tombol: Download PDF (biru), Print (abu), Kirim Email (hijau)

**Interaksi**:
- Download langsung generate PDF
- Print membuka dialog print browser
- Kirim Email konfirmasi dulu, lalu kirim ke email karyawan

---

### 7. Dashboard Karyawan (Absensi Saya)

**Layout**: Sidebar navigasi karyawan + konten utama
**Komponen**:
- Welcome banner: Banner gelap dengan foto profil melingkar, Nama Karyawan, ID Karyawan, dan teks motivasi "Tetap Produktif!".
- Card Status Absensi: Status kehadiran hari ini (misal: "Sudah Absen Masuk"), Jam Masuk (08:15), dan Jam Keluar (--:--).
- Tombol aksi utama (Bento Layout): Tombol besar "Clock In Sekarang" (biru brand) dan "Clock Out Sekarang" (slate gelap).
- Ringkasan kehadiran bulanan (Bento Grid): 4 kolom statistik (Hadir, Izin, Sakit, Alpha) disertai bar persentase visual.
- Widget "Libur Berikutnya": Informasi hari libur terdekat disertai ikon event.
- Kalender absensi bulanan: Kalender visual interaktif yang menandai tanggal kehadiran (hijau), izin (kuning), sakit (oranye), alpha (merah), dan cuti (biru).

**Interaksi**:
- Clock In / Clock Out melakukan pencatatan absensi langsung dengan konfirmasi visual.
- Hover efek yang dinamis pada kalender bulanan dan tombol bento.
- Slip gaji diakses via navigasi menu Penggajian yang mengarah pada layout slip terintegrasi.

---

### 8. Laporan & Rekap

**Layout**: Sidebar + cards + form + tabel
**Komponen**:
- 3 card shortcut berwarna:
  - Laporan Absensi (biru)
  - Laporan Penggajian (hijau)
  - Laporan Karyawan (ungu)
- Form Generate: Tipe, Periode, Departemen, Format (PDF/Excel), tombol Generate
- Tabel riwayat laporan: Nama, Tipe, Periode, Dibuat Oleh, Tanggal, Aksi (Download)

**Interaksi**:
- Klik card langsung isi form sesuai tipe
- Generate memunculkan loading lalu download file
- Riwayat laporan bisa di-download ulang

---

### 9. Halaman Super Admin - Dashboard

**Layout**: Sidebar kiri (Dashboard, Kelola Karyawan, Kelola HRD Manager, Logout) + konten utama
**Komponen**:
- Stats Cards (2 kolom):
  - Total Karyawan (icon people, biru)
  - Total HRD Manager (icon user, cyan)
- Tabel Karyawan Terbaru (full width): Nama, Email, Jabatan, Departemen, Status, Aksi

**Interaksi**:
- Cards dapat diklik untuk navigasi ke halaman kelola masing-masing.

---

### 10. Halaman Super Admin - Kelola HRD Manager

**Layout**: Sidebar kiri + tabel data full width
**Komponen**:
- Tombol aksi: Tambah HRD Manager (hijau)
- Data table: No, Nama, Email, Status, Aksi (Edit, Nonaktifkan)

**Interaksi**:
- Klik Tambah/Edit membuka modal form input (Nama, Email, Password, Status)
- Klik Nonaktifkan melakukan soft delete (mengubah status menjadi Nonaktif)

---

### 11. Halaman Super Admin - Kelola Karyawan

**Layout**: Sidebar kiri + tabel data full width
**Komponen**:
- Tombol aksi: Tambah Karyawan (hijau)
- Data table: No, Nama, Email, Jabatan, Departemen, Gaji Pokok, Status, Aksi (Edit, Nonaktifkan)

**Interaksi**:
- Klik Tambah membuka modal form input detail data karyawan dan pengaturan Gaji Pokok.
- Klik Nonaktifkan mengubah status karyawan menjadi Nonaktif.
