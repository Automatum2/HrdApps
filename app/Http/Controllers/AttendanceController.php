<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $employeeId = session('employee_id', '00001221'); // default fallback if session is not set
        
        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('tanggal', Carbon::today()->toDateString())
            ->first();

        return view('attendance.index', compact('attendance'));
    }

    public function clockIn(Request $request)
    {
        $request->validate([
            'status_kerja' => 'required|in:WFO,WFH,WFF,WOD,WEH',
            'foto' => 'required|string',
            'lokasi' => 'required|string',
            'keterangan' => 'nullable|string'
        ]);

        $employeeId = session('employee_id', '00001221');

        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('tanggal', Carbon::today()->toDateString())
            ->first();

        if ($attendance && $attendance->jam_masuk) {
            return back()->with('error', 'Anda sudah melakukan absensi masuk hari ini.');
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
            'lokasi' => 'required|string'
        ]);

        $employeeId = session('employee_id', '00001221');

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
            'keterangan' => 'required|string'
        ]);

        $employeeId = session('employee_id', '00001221');
        $mulai = Carbon::parse($request->tanggal_mulai);
        $selesai = Carbon::parse($request->tanggal_selesai);

        // Menghitung jumlah hari yang diajukan
        $daysRequested = $mulai->diffInDays($selesai) + 1;

        // Membatasi Izin dan Cuti maksimal 1 hari per bulan
        if (in_array($request->tipe, ['izin', 'cuti'])) {
            $existingLeaveThisMonth = Attendance::where('employee_id', $employeeId)
                ->whereMonth('tanggal', $mulai->month)
                ->whereYear('tanggal', $mulai->year)
                ->whereIn('status_kehadiran', ['izin', 'cuti'])
                ->count();

            if ($existingLeaveThisMonth + $daysRequested > 1) {
                return back()->with('error', 'Gagal: Jatah izin/cuti maksimal hanya 1 hari per bulan. Anda sudah menggunakan ' . $existingLeaveThisMonth . ' hari bulan ini.');
            }
        }

        for ($date = clone $mulai; $date->lte($selesai); $date->addDay()) {
            $attendance = Attendance::where('employee_id', $employeeId)
                ->where('tanggal', $date->toDateString())
                ->first();

            if (!$attendance) {
                $attendance = new Attendance();
                $attendance->employee_id = $employeeId;
                $attendance->tanggal = $date->toDateString();
                $attendance->status_kerja = 'WFO'; 
                $attendance->status_kehadiran = $request->tipe;
                $attendance->keterangan = $request->keterangan;
                $attendance->save();
            } else {
                if ($attendance->status_kehadiran != 'hadir') {
                    $attendance->status_kehadiran = $request->tipe;
                    $attendance->keterangan = $request->keterangan;
                    $attendance->save();
                }
            }
        }

        return back()->with('success', 'Pengajuan ' . ucfirst($request->tipe) . ' berhasil dikirim.');
    }
}
