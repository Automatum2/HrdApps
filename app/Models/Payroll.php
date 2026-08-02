<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'period_id',
        'gaji_pokok',
        'total_tunjangan',
        'total_potongan',
        'total_lembur',
        'gaji_kotor',
        'gaji_bersih',
        'status',
        'tanggal_bayar',
        'approved_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function period()
    {
        return $this->belongsTo(PayrollPeriod::class, 'period_id');
    }
}
