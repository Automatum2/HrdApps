<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status_kerja',
        'status_kehadiran',
        'total_jam_kerja',
        'jam_lembur',
        'keterangan',
        'foto_masuk',
        'lokasi_masuk',
        'foto_keluar',
        'lokasi_keluar'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
