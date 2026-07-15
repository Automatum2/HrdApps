# Entity Relationship Diagram (ERD) - HRDApps

## Diagram ERD

```mermaid
erDiagram
    USERS ||--o{ EMPLOYEES : "manages"
    EMPLOYEES ||--o{ ATTENDANCES : "has"
    EMPLOYEES ||--o{ PAYROLLS : "receives"
    EMPLOYEES }o--|| DEPARTMENTS : "belongs_to"
    EMPLOYEES }o--|| POSITIONS : "holds"
    PAYROLLS ||--|{ PAYROLL_DETAILS : "contains"
    PAYROLLS }o--|| PAYROLL_PERIODS : "in_period"
    EMPLOYEES ||--o{ ALLOWANCES : "gets"
    EMPLOYEES ||--o{ DEDUCTIONS : "has"

    USERS {
        int id PK
        string username
        string password
        string email
        enum role "super_admin, hr_manager, karyawan"
        int employee_id FK
        datetime last_login
        datetime created_at
        datetime updated_at
    }

    EMPLOYEES {
        int id PK
        string nik "Nomor Induk Karyawan"
        string nama_lengkap
        string email
        string no_telepon "nullable"
        string alamat "nullable"
        string tempat_lahir "nullable"
        date tanggal_lahir "nullable"
        enum jenis_kelamin "L, P - nullable"
        int department_id FK "nullable"
        int position_id FK "nullable"
        enum status_kerja "tetap, kontrak, magang - nullable"
        decimal gaji_pokok
        string no_rekening "nullable"
        string nama_bank "nullable"
        date tanggal_masuk "nullable"
        date tanggal_kontrak_berakhir "nullable"
        enum status "aktif, nonaktif, cuti"
        string foto "nullable"
        datetime created_at
        datetime updated_at
    }

    DEPARTMENTS {
        int id PK
        string nama_department
        string kode_department
        string deskripsi
        datetime created_at
    }

    POSITIONS {
        int id PK
        string nama_jabatan
        string level "staff, supervisor, manager, director"
        decimal tunjangan_jabatan
        datetime created_at
    }

    ATTENDANCES {
        int id PK
        int employee_id FK
        date tanggal
        time jam_masuk
        time jam_keluar
        enum status_kerja "WFO, WFH, WFF, WOD, WEH"
        enum status_kehadiran "hadir, izin, sakit, alpha, cuti"
        decimal total_jam_kerja
        decimal jam_lembur
        string keterangan
        datetime created_at
    }

    PAYROLL_PERIODS {
        int id PK
        string nama_periode "contoh: Juni 2026"
        date tanggal_mulai
        date tanggal_selesai
        enum status "draft, proses, selesai, dibatalkan"
        datetime created_at
    }

    PAYROLLS {
        int id PK
        int employee_id FK
        int period_id FK
        decimal gaji_pokok
        decimal total_tunjangan
        decimal total_potongan
        decimal total_lembur
        decimal gaji_kotor
        decimal gaji_bersih
        enum status "draft, approved, paid"
        date tanggal_bayar
        string approved_by
        datetime created_at
        datetime updated_at
    }

    PAYROLL_DETAILS {
        int id PK
        int payroll_id FK
        string komponen "Gaji Pokok, Tunj. Transport, BPJS, dll"
        enum tipe "pendapatan, potongan"
        decimal jumlah
        string keterangan
    }

    ALLOWANCES {
        int id PK
        int employee_id FK
        string nama_tunjangan "Transport, Makan, Kesehatan, dll"
        decimal jumlah
        enum tipe "tetap, tidak_tetap"
        boolean is_active
        datetime created_at
    }

    DEDUCTIONS {
        int id PK
        int employee_id FK
        string nama_potongan "BPJS, Pinjaman, Keterlambatan, dll"
        decimal jumlah
        enum tipe "tetap, tidak_tetap"
        boolean is_active
        datetime created_at
    }
```

---

## Penjelasan Tabel

### Tabel Utama

| Tabel | Fungsi |
|-------|--------|
| `users` | Menyimpan data login (username, password, role) |
| `employees` | Data lengkap karyawan (identitas, jabatan, gaji pokok, rekening) |
| `departments` | Daftar departemen/divisi perusahaan |
| `positions` | Daftar jabatan beserta tunjangan jabatan |

### Tabel Absensi

| Tabel | Fungsi |
|-------|--------|
| `attendances` | Rekap kehadiran harian (jam masuk, jam keluar, status kerja, lembur) |

### Tabel Penggajian

| Tabel | Fungsi |
|-------|--------|
| `payroll_periods` | Periode penggajian (bulanan) |
| `payrolls` | Header gaji per karyawan per periode |
| `payroll_details` | Detail komponen gaji (rincian pendapatan & potongan) |
| `allowances` | Master tunjangan per karyawan |
| `deductions` | Master potongan per karyawan |

---

## Relasi Antar Tabel

1. **Users - Employees**: 1 user terhubung ke 1 karyawan (login account)
2. **Employees - Departments**: Banyak karyawan dalam 1 departemen
3. **Employees - Positions**: Banyak karyawan bisa punya jabatan yang sama
4. **Employees - Attendances**: 1 karyawan punya banyak data kehadiran
5. **Employees - Payrolls**: 1 karyawan punya banyak data gaji (per periode)
6. **Payrolls - Payroll Details**: 1 slip gaji punya banyak komponen detail
7. **Payrolls - Payroll Periods**: Banyak slip gaji dalam 1 periode
