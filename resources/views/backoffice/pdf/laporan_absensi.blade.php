<!DOCTYPE html>
<html>
<head>
    <title>Laporan Absensi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
        .info { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>{{ $tipe }}</h2>
    <div class="info">
        <strong>Departemen:</strong> {{ $deptName }}<br>
        <strong>Periode:</strong> {{ $periodeStr }}
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Karyawan</th>
                <th>NIK</th>
                <th>Departemen</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Status</th>
                <th>Total Jam</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $att)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $att->employee->nama_lengkap ?? '-' }}</td>
                <td>{{ $att->employee->nik ?? '-' }}</td>
                <td>{{ $att->employee->department->nama_department ?? 'Umum' }}</td>
                <td>{{ $att->tanggal }}</td>
                <td>{{ $att->jam_masuk ?? '--:--' }}</td>
                <td>{{ $att->jam_keluar ?? '--:--' }}</td>
                <td>{{ ucfirst($att->status_kehadiran) }}</td>
                <td>{{ $att->total_jam_kerja ?? '--' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
