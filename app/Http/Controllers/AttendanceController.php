<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $employeeId = session('employee_id');

        
        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('tanggal', Carbon::today()->toDateString())
            ->first();

        return view('attendance.index', compact('attendance'));
    }

    public function clockIn(Request $request)
    {
        $request->validate([
            'status_kerja' => 'required|in:WFO,WFD,WFH,WFF,WOD,WEH',
            'foto' => 'required|string',
            'lokasi' => 'required|string',
            'keterangan' => 'nullable|string'
        ]);

        $employeeId = session('employee_id');


        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('tanggal', Carbon::today()->toDateString())
            ->first();

        if ($attendance && $attendance->jam_masuk) {
            return back()->with('error', 'Anda sudah melakukan absensi masuk hari ini.');
        }

        // Haversine Radius Validation for WFO / WFD
        if (in_array($request->status_kerja, ['WFO', 'WFD'])) {
            // Extrak lat dan lon dari request->lokasi (format awal adalah: "lat,lon | alamat...")
            $lokasiParts = explode('|', $request->lokasi);
            $coords = explode(',', trim($lokasiParts[0]));
            
            if (count($coords) >= 2) {
                $userLat = (float) trim($coords[0]);
                $userLon = (float) trim($coords[1]);

                // Koordinat Kantor Dummy (Monas)
                $officeLat = -6.1753924;
                $officeLon = 106.8271528;

                // Haversine formula
                $earthRadius = 6371000; // in meters
                $dLat = deg2rad($userLat - $officeLat);
                $dLon = deg2rad($userLon - $officeLon);
                
                $a = sin($dLat/2) * sin($dLat/2) +
                     cos(deg2rad($officeLat)) * cos(deg2rad($userLat)) *
                     sin($dLon/2) * sin($dLon/2);
                $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                $distance = $earthRadius * $c;

                if ($distance > 100) {
                    return back()->with('error', 'Jarak Anda (' . round($distance) . ' meter) melebihi batas maksimal 100 meter dari area kantor.');
                }
            } else {
                return back()->with('error', 'Gagal memverifikasi koordinat lokasi Anda.');
            }
        }

        $image_parts = explode(";base64,", $request->foto);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = 'in_' . $employeeId . '_' . time() . '.' . $image_type;
        $path = storage_path('app/public/attendances/' . $fileName);
        
        if (!file_exists(storage_path('app/public/attendances'))) {
            mkdir(storage_path('app/public/attendances'), 0755, true);
        }
        
        file_put_contents($path, $image_base64);

        if (!$attendance) {
            $attendance = new Attendance();
            $attendance->employee_id = $employeeId; 
            $attendance->tanggal = Carbon::today()->toDateString();
        }

        $attendance->jam_masuk = Carbon::now()->toTimeString();
        $attendance->status_kerja = $request->status_kerja;
        $attendance->status_kehadiran = 'hadir';
        $attendance->foto_masuk = 'attendances/' . $fileName;
        $attendance->lokasi_masuk = $request->lokasi;
        $attendance->keterangan = $request->keterangan;
        $attendance->save();

        return redirect()->route('backoffice.dashboard')->with('success', 'Berhasil melakukan absensi masuk.');
    }

    public function clockOut(Request $request)
    {
        $request->validate([
            'foto' => 'required|string',
            'lokasi' => 'required|string',
            'keterangan' => 'required|string'
        ], [
            'keterangan.required' => 'Laporan harian wajib diisi sebelum melakukan absensi keluar.'
        ]);

        $employeeId = session('employee_id');


        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('tanggal', Carbon::today()->toDateString())
            ->first();

        if (!$attendance || !$attendance->jam_masuk) {
            return back()->with('error', 'Anda belum melakukan absensi masuk.');
        }

        if ($attendance->jam_keluar) {
            return back()->with('error', 'Anda sudah melakukan absensi keluar hari ini.');
        }

        $image_parts = explode(";base64,", $request->foto);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = 'out_' . $employeeId . '_' . time() . '.' . $image_type;
        $path = storage_path('app/public/attendances/' . $fileName);
        
        if (!file_exists(storage_path('app/public/attendances'))) {
            mkdir(storage_path('app/public/attendances'), 0755, true);
        }

        file_put_contents($path, $image_base64);

        $attendance->jam_keluar = Carbon::now()->toTimeString();
        $attendance->foto_keluar = 'attendances/' . $fileName;
        $attendance->lokasi_keluar = $request->lokasi;
        if ($request->filled('keterangan')) {
            $attendance->keterangan = $request->keterangan;
        }

        $jamMasuk = Carbon::parse($attendance->jam_masuk);
        $jamKeluar = Carbon::parse($attendance->jam_keluar);
        $totalMinutes = $jamKeluar->diffInMinutes($jamMasuk);
        $attendance->total_jam_kerja = round($totalMinutes / 60, 2);

        $attendance->save();

        return redirect()->route('backoffice.dashboard')->with('success', 'Berhasil melakukan absensi keluar.');
    }

    public function submitLeave(Request $request)
    {
        if ($request->tipe === 'sakit' && empty($request->tanggal_selesai)) {
            $request->merge(['tanggal_selesai' => $request->tanggal_mulai]);
        }

        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tipe' => 'required|in:izin,sakit,cuti',
            'keterangan' => 'required|string',
            'dokumen_pendukung' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $employeeId = session('employee_id');

        $mulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
        $selesai = Carbon::parse($request->tanggal_selesai)->startOfDay();
        $besok = Carbon::tomorrow()->startOfDay();

        // Validasi H-1: Cuti harus diajukan minimal sehari sebelumnya
        if ($request->tipe === 'cuti' && $mulai->lt($besok)) {
            return back()->with('error', 'Gagal: Batas maksimal pengajuan cuti adalah H-1 sebelum hari H.');
        }

        // Menghitung jumlah hari yang diajukan
        $daysRequested = $mulai->diffInDays($selesai) + 1;

        // Membatasi Izin dan Cuti berdasarkan sisa Kuota Tahunan
        if (in_array($request->tipe, ['izin', 'cuti'])) {
            $employee = \App\Models\Employee::find($employeeId);
            $kuotaCuti = $employee ? ($employee->kuota_cuti ?? 12) : 12;

            $existingLeaveThisYear = Attendance::where('employee_id', $employeeId)
                ->whereYear('tanggal', $mulai->year)
                ->whereIn('status_kehadiran', ['izin', 'cuti'])
                ->count();

            // Tambahkan cuti/izin yang masih pending
            $pendingLeaveDays = \App\Models\LeaveRequest::where('employee_id', $employeeId)
                ->whereYear('tanggal_mulai', $mulai->year)
                ->whereIn('status', ['menunggu_manager', 'menunggu_hr'])
                ->whereIn('tipe', ['izin', 'cuti'])
                ->get()
                ->sum(function($leave) {
                    $start = \Carbon\Carbon::parse($leave->tanggal_mulai);
                    $end = \Carbon\Carbon::parse($leave->tanggal_selesai);
                    return $start->diffInDays($end) + 1;
                });

            $totalRequestedThisYear = $existingLeaveThisYear + $pendingLeaveDays;

            if ($totalRequestedThisYear + $daysRequested > $kuotaCuti) {
                return back()->with('error', 'Gagal: Sisa kuota cuti/izin tahunan Anda tidak mencukupi. Sisa kuota: ' . max(0, $kuotaCuti - $totalRequestedThisYear) . ' hari, dan Anda mengajukan ' . $daysRequested . ' hari.');
            }
        }

        $dokumenPath = null;
        if ($request->hasFile('dokumen_pendukung')) {
            $file = $request->file('dokumen_pendukung');
            $fileName = 'doc_' . $employeeId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = storage_path('app/public/documents');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            $file->move($path, $fileName);
            $dokumenPath = 'documents/' . $fileName;
        }

        $leaveRequest = new \App\Models\LeaveRequest();
        $leaveRequest->employee_id = $employeeId;
        $leaveRequest->tanggal_mulai = $mulai->toDateString();
        $leaveRequest->tanggal_selesai = $selesai->toDateString();
        $leaveRequest->tipe = $request->tipe;
        $leaveRequest->keterangan = $request->keterangan;
        
        if ($dokumenPath) {
            $leaveRequest->dokumen_pendukung = $dokumenPath;
        }

        $leaveRequest->status = 'menunggu_manager';
        $leaveRequest->save();

        return back()->with('success', 'Pengajuan ' . ucfirst($request->tipe) . ' berhasil dikirim.');
    }
}
