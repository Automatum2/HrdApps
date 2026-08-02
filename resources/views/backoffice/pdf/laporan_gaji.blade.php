<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penggajian</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
        .info { margin-bottom: 20px; }
        .text-right { text-align: right; }
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
                <th>Departemen</th>
                <th>Periode Gaji</th>
                <th class="text-right">Gaji Pokok</th>
                <th class="text-right">Tunjangan</th>
                <th class="text-right">Potongan</th>
                <th class="text-right">Gaji Bersih</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $pay)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $pay->employee->nama_lengkap ?? '-' }}</td>
                <td>{{ $pay->employee->department->nama_department ?? 'Umum' }}</td>
                <td>{{ $pay->period->nama_periode ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($pay->gaji_pokok, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($pay->total_tunjangan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($pay->total_potongan, 0, ',', '.') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($pay->gaji_bersih, 0, ',', '.') }}</td>
                <td>{{ ucfirst($pay->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
