<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $role = session('user_role');
        if ($role === 'employee') {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        $recent_reports = Report::orderBy('created_at', 'desc')->get();
        $departments = \Illuminate\Support\Facades\DB::table('departments')->get();
        
        return view('backoffice.laporan', compact('recent_reports', 'departments'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'tipe_laporan' => 'required|string',
            'periode' => 'required|string',
            'department_id' => 'required',
            'format' => 'required|string'
        ]);

        $tipe = $request->tipe_laporan;
        $periodeStr = $request->periode;
        $dept_id = $request->department_id;
        $format = strtolower($request->format);
        
        $dates = explode(' - ', $periodeStr);
        if (count($dates) == 2) {
            try {
                $start = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                $end = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
            } catch (\Exception $e) {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
            }
        } else {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        }

        $deptName = 'Semua Departemen';
        if ($dept_id != 'all') {
            $department = \Illuminate\Support\Facades\DB::table('departments')->where('id', $dept_id)->first();
            if ($department) $deptName = $department->nama_departemen;
        }

        $data = [];
        $fileNameBase = str_replace(' ', '_', $tipe) . '_' . str_replace(' ', '_', $deptName) . '_' . time();
        $filePath = '';

        if (str_contains($tipe, 'Absensi') || str_contains($tipe, 'Kinerja')) {
            $query = Attendance::with('employee.department', 'employee.position')
                ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()]);
            
            if ($dept_id != 'all') {
                $query->whereHas('employee', function($q) use ($dept_id) {
                    $q->where('department_id', $dept_id);
                });
            }
            
            $data = $query->orderBy('tanggal', 'desc')->get();
            
            if ($format === 'excel' || $format === 'csv') {
                $format = 'csv';
                $filePath = 'reports/' . $fileNameBase . '.csv';
                $csvData = "No,Nama Karyawan,NIK,Departemen,Tanggal,Jam Masuk,Jam Keluar,Status Kehadiran,Total Jam Kerja\n";
                $i = 1;
                foreach ($data as $att) {
                    $nama = $att->employee->nama_lengkap ?? '-';
                    $nik = $att->employee->nik ?? '-';
                    $dept = $att->employee->department->nama_departemen ?? 'Umum';
                    $csvData .= "$i,\"$nama\",\"$nik\",\"$dept\",\"{$att->tanggal}\",\"{$att->jam_masuk}\",\"{$att->jam_keluar}\",\"" . ucfirst($att->status_kehadiran) . "\",\"{$att->total_jam_kerja}\"\n";
                    $i++;
                }
                Storage::disk('public')->put($filePath, $csvData);
            } else {
                $filePath = 'reports/' . $fileNameBase . '.pdf';
                $pdf = Pdf::loadView('backoffice.pdf.laporan_absensi', compact('data', 'tipe', 'periodeStr', 'deptName'));
                $pdf->setPaper('A4', 'landscape');
                Storage::disk('public')->put($filePath, $pdf->output());
            }

        } elseif (str_contains($tipe, 'Gaji')) {
            $query = Payroll::with('employee.department', 'employee.position', 'period')
                ->whereHas('period', function($q) use ($start, $end) {
                    $q->whereBetween('tanggal_mulai', [$start->toDateString(), $end->toDateString()]);
                });
            
            if ($dept_id != 'all') {
                $query->whereHas('employee', function($q) use ($dept_id) {
                    $q->where('department_id', $dept_id);
                });
            }
            
            $data = $query->orderBy('id', 'desc')->get();
            
            if ($format === 'excel' || $format === 'csv') {
                $format = 'csv';
                $filePath = 'reports/' . $fileNameBase . '.csv';
                $csvData = "No,Nama Karyawan,Departemen,Periode,Gaji Pokok,Tunjangan,Potongan,Gaji Bersih,Status\n";
                $i = 1;
                foreach ($data as $pay) {
                    $nama = $pay->employee->nama_lengkap ?? '-';
                    $dept = $pay->employee->department->nama_departemen ?? 'Umum';
                    $periode = $pay->period->nama_periode ?? '-';
                    $csvData .= "$i,\"$nama\",\"$dept\",\"$periode\",{$pay->gaji_pokok},{$pay->total_tunjangan},{$pay->total_potongan},{$pay->gaji_bersih},\"{$pay->status}\"\n";
                    $i++;
                }
                Storage::disk('public')->put($filePath, $csvData);
            } else {
                $filePath = 'reports/' . $fileNameBase . '.pdf';
                $pdf = Pdf::loadView('backoffice.pdf.laporan_gaji', compact('data', 'tipe', 'periodeStr', 'deptName'));
                $pdf->setPaper('A4', 'landscape');
                Storage::disk('public')->put($filePath, $pdf->output());
            }
        }

        $report = Report::create([
            'nama_laporan' => $tipe . ' - ' . $deptName,
            'tipe' => $tipe,
            'periode' => $periodeStr,
            'dibuat_oleh' => session('user_name'),
            'file_path' => $filePath,
            'format' => $format,
        ]);

        return redirect()->route('backoffice.laporan')->with('success', 'Laporan berhasil di-generate.');
    }

    public function download($id)
    {
        $report = Report::findOrFail($id);
        
        if (Storage::disk('public')->exists($report->file_path)) {
            return Storage::disk('public')->download($report->file_path);
        }
        
        return redirect()->back()->with('error', 'File laporan tidak ditemukan.');
    }

    public function kinerja(Request $request)
    {
        $role = session('user_role');
        if ($role === 'employee') {
            return redirect()->route('backoffice.dashboard')->with('error', 'Akses ditolak.');
        }

        $query = Attendance::with('employee.department', 'employee.position');
        
        $dari = $request->input('dari_tanggal', Carbon::now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai_tanggal', Carbon::now()->endOfMonth()->toDateString());
        $query->whereBetween('tanggal', [$dari, $sampai]);
        
        if ($request->filled('departemen_id') && $request->departemen_id != 'all') {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->departemen_id);
            });
        }
        
        $attendances = $query->orderBy('tanggal', 'desc')->get();
        $departments = \Illuminate\Support\Facades\DB::table('departments')->get();
        
        return view('backoffice.laporan_kinerja', compact('attendances', 'departments', 'dari', 'sampai'));
    }
}
