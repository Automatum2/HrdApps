<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [
        'employee_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'tipe',
        'keterangan',
        'dokumen_pendukung',
        'status',
        'manager_approved_at',
        'manager_approved_by',
        'hr_approved_at',
        'hr_approved_by',
        'alasan_penolakan'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'manager_approved_at' => 'datetime',
        'hr_approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_approved_by');
    }

    public function hr()
    {
        return $this->belongsTo(User::class, 'hr_approved_by');
    }
}
