# HRDApps - Human Resource Management System

Aplikasi web berbasis Laravel 12 untuk pengelolaan sumber daya manusia (HRD), proses rekrutmen pelamar, absensi, pengajuan cuti, manajemen departemen & jabatan, serta penggajian dan cetak slip gaji otomatis.

---

## Daftar Isi
1. [Fitur Utama](#fitur-utama)
2. [Hak Akses & Role Pengguna](#hak-akses--role-pengguna)
3. [Kebutuhan Sistem (Prerequisites)](#kebutuhan-sistem-prerequisites)
4. [Panduan Instalasi Lokal (Development)](#panduan-instalasi-lokal-development)
5. [Akun Default Seeder](#akun-default-seeder)
6. [Panduan & Kebutuhan Hosting (Production)](#panduan--kebutuhan-hosting-production)
7. [Daftar Perintah Artisan Penting](#daftar-perintah-artisan-penting)

---

## Fitur Utama

- **Portal Karir & Rekrutmen Pelamar**: Form pengajuan lamaran, upload CV, verifikasi OTP via email, dan seleksi kandidat oleh HR.
- **Manajemen Karyawan**: Pengelolaan data biodata, mutasi departemen, update gaji pokok, serta arsip dokumen digital.
- **Absensi Karyawan**: Pencatatan kehadiran harian (clock-in & clock-out), rekap status kehadiran, dan ekspor laporan absensi (CSV).
- **Pengajuan & Approval Cuti**: Pengajuan cuti oleh karyawan dan persetujuan bertingkat oleh Manager / HR.
- **Penggajian (Payroll)**: Perhitungan gaji periode, tunjangan, potongan, kalkulasi total otomatis, serta cetak slip gaji format PDF.
- **Struktur Organisasi**: Pengaturan data departemen dan level jabatan beserta tunjangan jabatan.
- **Manajemen Pelatihan (Training)**: Pencatatan program training dan peningkatan kompetensi karyawan.
- **Laporan & Ekspor**: Rekap laporan absensi dan penggajian berkala.

---

## Hak Akses & Role Pengguna

1. **Super Admin**: Memiliki kendali penuh ke seluruh modul sistem, kelola akun HR, pengaturan jabatan, dan monitoring absensi global.
2. **HR Manager**: Mengelola data karyawan, review CV pelamar, absensi, approve cuti, penggajian, tunjangan/potongan, dan program training.
3. **Manager Departemen**: Memantau absensi karyawan di departemennya, review permohonan cuti, dan melihat laporan kinerja tim.
4. **Karyawan**: Melakukan absensi (clock in/out), mengajukan cuti, melihat rekap kehadiran, dan mengunduh slip gaji pribadi.

---

## Kebutuhan Sistem (Prerequisites)

- **PHP**: Versi `>= 8.2`
- **Ekstensi PHP Wajib**:
  - `bcmath`
  - `ctype`
  - `fileinfo`
  - `json`
  - `mbstring`
  - `openssl`
  - `pdo` & driver database (`pdo_mysql` / `pdo_pgsql` / `pdo_sqlite`)
  - `tokenizer`
  - `xml`
  - `gd` (untuk proses PDF / manipulasi gambar)
- **Composer**: Versi `>= 2.2`
- **Node.js**: Versi `>= 18.x` dan **NPM**
- **Database Server**: MySQL 8.0+ / MariaDB 10.4+ / PostgreSQL / SQLite

---

## Panduan Instalasi Lokal (Development)

Jalankan langkah-langkah berikut di terminal:

### 1. Masuk ke Direktori Proyek
```bash
cd HRDAPPS
```

### 2. Pasang Dependensi PHP (Composer)
```bash
composer install
```

### 3. Salin dan Konfigurasikan File Environment
```bash
cp .env.example .env
```
Sesuaikan konfigurasi database dan email pada file `.env`:
```env
APP_NAME="HRDApps"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hrdapps_db
DB_USERNAME=root
DB_PASSWORD=

# Konfigurasi SMTP Email (wajib untuk fitur aktivasi akun, OTP, dan penolakan CV)
# Contoh 1: Menggunakan Hostinger Mail / Titan Email (Rekomendasi Domain Kantor)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME="admin@dhs.or.id"
MAIL_PASSWORD="isi_password_email_hostinger"
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="admin@dhs.or.id"
MAIL_FROM_NAME="HRDApps Sistem"

# Contoh 2: Menggunakan Gmail / Google Workspace (Wajib Google App Password 16 Digit)
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.gmail.com
# MAIL_PORT=587
# MAIL_USERNAME="email_anda@gmail.com"
# MAIL_PASSWORD="isi_16_digit_app_password"
# MAIL_ENCRYPTION=tls
# MAIL_FROM_ADDRESS="email_anda@gmail.com"
# MAIL_FROM_NAME="HRDApps Sistem"
```

> **Catatan Keamanan:** Jangan pernah menuliskan *password* email asli ke file `README.md` atau commit Git. Selalu simpan *password* asli di file `.env`. Setelah mengubah konfigurasi `.env`, jalankan `php artisan config:clear`.


### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Jalankan Migrasi Database dan Seeder
```bash
php artisan migrate --seed
```

### 6. Hubungkan Folder Storage ke Public (Symlink)
```bash
php artisan storage:link
```

### 7. Pasang Dependensi Frontend & Compile Aset
```bash
npm install
npm run build
```

### 8. Jalankan Server Lokal
Untuk menjalankan server secara mandiri:
```bash
php artisan serve
```
Atau jika ingin menjalankan server, queue, dan vite watcher sekaligus:
```bash
npm run dev
```
Akses aplikasi melalui browser di alamat: `http://localhost:8000` atau `http://127.0.0.1:8000`.

---

## Akun Default Seeder

Semua akun default menggunakan password: `password`

| Role | Username | Email | Password |
|---|---|---|---|
| Super Admin | `superadmin` | `superadmin@example.com` | `password` |
| HR Manager | `hrmanager` | `hrmanager@example.com` | `password` |
| Manager IT | `managerit` | `managerit@example.com` | `password` |
| Karyawan Staff | `karyawan` | `karyawan@example.com` | `password` |

---

## Panduan & Kebutuhan Hosting (Production)

### A. Hal yang Diperlukan Sebelum Hosting

1. **Paket Hosting / Server**:
   - **Shared Hosting / cPanel**: Pastikan memiliki fitur Terminal SSH, opsi PHP 8.2+, dan manajemen database MySQL.
   - **VPS (Ubuntu 22.04 / 24.04)**: Direkomendasikan untuk stabilitas (menggunakan Nginx/Apache, PHP 8.2-FPM, MySQL Server, Composer, Certbot SSL).
2. **Domain / Subdomain** yang sudah mengarah ke IP hosting / server.
3. **Akun SMTP Email Aktif** (misal: Gmail SMTP, SendGrid, Mailgun, atau SMTP bawaan domain hosting) untuk pengiriman kode OTP aktivasi/reset password.
4. **SSL Certificate (HTTPS)** untuk keamanan data sesi dan transfer berkas CV.

---

### B. Langkah Deployment ke cPanel (Shared Hosting)

1. **Build Aset Frontend di Komputer Lokal Sebelum Upload**:
   ```bash
   npm run build
   ```
2. **Kompresi File Proyek**:
   - Kompres seluruh isi folder proyek ke format `.zip` (abaikan folder `node_modules`, `.git`).
3. **Upload & Ekstrak**:
   - Upload file zip ke root direktori cPanel (di luar direktori `public_html`, contoh: `/home/username/hrdapps`).
   - Ekstrak seluruh berkas di folder tersebut.
4. **Atur Document Root**:
   - Pindahkan isi folder `hrdapps/public/*` ke dalam folder `public_html`, ATAU ubah Document Root domain di cPanel agar mengarah ke `/home/username/hrdapps/public`.
5. **Konfigurasi File `.env` Produksi**:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://domainanda.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_cpanel_db
   DB_USERNAME=nama_cpanel_user
   DB_PASSWORD=password_db_anda
   ```
6. **Import / Migrasi Database**:
   - Buat database dan user melalui menu *MySQL Databases* di cPanel.
   - Jalankan `php artisan migrate --seed` via Terminal cPanel, atau import file `.sql` melalui *phpMyAdmin*.
7. **Buat Symlink Storage**:
   Jalankan perintah melalui Terminal cPanel:
   ```bash
   php artisan storage:link
   ```

---

### C. Langkah Deployment ke VPS (Nginx + PHP-FPM)

1. **Clone Repository ke Direktori Web**:
   ```bash
   cd /var/www
   git clone <URL_REPOSITORY> hrdapps
   cd hrdapps
   ```

2. **Install Dependensi**:
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install
   npm run build
   ```

3. **Atur File `.env`**:
   ```bash
   cp .env.example .env
   nano .env
   ```
   Isi konfigurasi produksi (`APP_ENV=production`, `APP_DEBUG=false`, database, dan SMTP).

4. **Generate Key, Migrasi Database, & Link Storage**:
   ```bash
   php artisan key:generate --force
   php artisan migrate --force --seed
   php artisan storage:link
   ```

5. **Pengaturan Permission / Hak Akses**:
   Berikan izin akses ke web server user (`www-data`):
   ```bash
   sudo chown -R www-data:www-data /var/www/hrdapps/storage /var/www/hrdapps/bootstrap/cache
   sudo chmod -R 775 /var/www/hrdapps/storage /var/www/hrdapps/bootstrap/cache
   ```

6. **Konfigurasi Web Server Nginx**:
   Arahkan `root` konfigurasi virtual host Nginx ke direktori `public`:
   ```nginx
   server {
       listen 80;
       server_name hrd.domainanda.com;
       root /var/www/hrdapps/public;

       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";

       index index.php;
       charset utf-8;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location = /favicon.ico { access_log off; log_not_found off; }
       location = /robots.txt  { access_log off; log_not_found off; }

       error_page 404 /index.php;

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }

       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```

7. **Caching untuk Performa Produksi**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

8. **Cron Job / Scheduler (Opsional)**:
   Tambahkan ke crontab server jika memerlukan background task otomatis:
   ```bash
   * * * * * cd /var/www/hrdapps && php artisan schedule:run >> /dev/null 2>&1
   ```

---

## Daftar Perintah Artisan Penting

| Perintah | Fungsi |
|---|---|
| `php artisan migrate` | Menjalankan migrasi struktur tabel database |
| `php artisan db:seed` | Mengisi data awal akun dan departemen |
| `php artisan storage:link` | Membuat symlink folder public untuk akses upload dokumen & CV |
| `php artisan optimize:clear` | Membersihkan seluruh cache config, route, dan view |
| `php artisan config:cache` | Membuat cache konfigurasi untuk mode produksi |
| `php artisan route:cache` | Membuat cache rute URL untuk mempercepat respon aplikasi |
