<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $employee->nama_lengkap }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .header { border-bottom: 2px solid #0056b3; padding-bottom: 10px; margin-bottom: 20px; }
        .company-name { font-size: 20px; font-weight: bold; color: #0056b3; margin: 0; }
        .company-address { font-size: 10px; color: #666; margin: 0; }
        .title { text-align: right; margin-top: -40px; }
        .title h2 { margin: 0; font-size: 18px; letter-spacing: 2px; }
        .periode { display: inline-block; background: #e0f2fe; color: #0284c7; padding: 2px 10px; border-radius: 10px; font-size: 10px; font-weight: bold; margin-top: 5px; }
        
        .info-table { width: 100%; margin-bottom: 20px; background: #f8fafc; padding: 10px; border: 1px solid #e2e8f0; border-radius: 5px; }
        .info-table td { padding: 3px 0; }
        .info-table .label { width: 100px; color: #64748b; text-transform: uppercase; font-size: 10px; }
        .info-table .value { font-weight: bold; }
        
        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .main-table th { background: #f1f5f9; text-align: left; padding: 8px; border-bottom: 1px solid #cbd5e1; font-size: 11px; text-transform: uppercase; }
        .main-table td { padding: 8px; border-bottom: 1px dashed #e2e8f0; }
        .text-right { text-align: right; }
        
        .summary-box { border: 1px solid #94a3b8; padding: 10px; background: #f8fafc; margin-bottom: 20px; }
        .terbilang { font-style: italic; color: #64748b; margin-top: 5px; }
        
        .footer { width: 100%; margin-top: 50px; }
        .footer-table { width: 100%; text-align: center; }
        .signature-line { border-bottom: 1px solid #000; width: 150px; margin: 50px auto 5px auto; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="company-name">PT Global Tech Solusindo</h1>
        <p class="company-address">Cyber Tower Suite 10, Jakarta Pusat, Indonesia</p>
        <div class="title">
            <h2>SLIP GAJI KARYAWAN</h2>
            <span class="periode">Periode: {{ $monthName }}</span>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama</td>
            <td class="value">: {{ $employee->nama_lengkap }}</td>
            <td class="label">Jabatan</td>
            <td class="value">: {{ $employee->position->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td class="value">: {{ $employee->nik }}</td>
            <td class="label">Departemen</td>
            <td class="value">: {{ $employee->department->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">No Rekening</td>
            <td class="value">: {{ $employee->nama_bank }} - {{ $employee->no_rekening }}</td>
            <td class="label">Hari Kerja</td>
            <td class="value">: {{ $hadir }} Hari</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th colspan="2">PENGHASILAN (PENERIMAAN)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok</td>
                <td class="text-right">Rp {{ number_format($gajiPokok, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tunjangan Jabatan</td>
                <td class="text-right">Rp {{ number_format($tunjanganJabatan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tunjangan Makan</td>
                <td class="text-right">Rp {{ number_format($tunjanganMakan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tunjangan Transport</td>
                <td class="text-right">Rp {{ number_format($tunjanganTransport, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #0284c7;">Total Penghasilan (A)</td>
                <td class="text-right" style="font-weight: bold; color: #0284c7;">Rp {{ number_format($gajiKotor, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th colspan="2">POTONGAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>BPJS Kesehatan (4%)</td>
                <td class="text-right">Rp {{ number_format($potonganBPJSKesehatan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>BPJS Ketenagakerjaan (2%)</td>
                <td class="text-right">Rp {{ number_format($potonganBPJSKetenagakerjaan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Potongan Absen (Alpha: {{ $alpha }} hari)</td>
                <td class="text-right">Rp {{ number_format($potonganAlpha, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #b91c1c;">Total Potongan (B)</td>
                <td class="text-right" style="font-weight: bold; color: #b91c1c;">Rp {{ number_format($totalPotongan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="summary-box">
        <table width="100%">
            <tr>
                <td style="font-size: 14px; font-weight: bold;">PENERIMAAN BERSIH (A - B)</td>
                <td class="text-right" style="font-size: 16px; font-weight: bold; color: #16a34a;">Rp {{ number_format($gajiBersih, 0, ',', '.') }}</td>
            </tr>
        </table>
        <div class="terbilang">Terbilang: {{ $terbilangGaji }}</div>
    </div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td width="50%">
                    Penerima,
                    <div class="signature-line"></div>
                    <b>{{ $employee->nama_lengkap }}</b>
                </td>
                <td width="50%">
                    Jakarta, {{ \Carbon\Carbon::now()->format('d F Y') }}<br>
                    Manager HRD,
                    <div class="signature-line"></div>
                    <b>Rina Melati, S.E.</b>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
