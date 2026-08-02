<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_laporan',
        'tipe',
        'periode',
        'dibuat_oleh',
        'file_path',
        'format',
    ];
}
