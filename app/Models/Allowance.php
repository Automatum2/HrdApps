<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'nama_tunjangan',
        'jumlah',
        'tipe',
        'is_active',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
