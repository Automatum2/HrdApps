<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'email',
        'no_telepon',
        'alamat',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'department_id',
        'position_id',
        'status_kerja',
        'gaji_pokok',
        'no_rekening',
        'nama_bank',
        'tanggal_masuk',
        'tanggal_kontrak_berakhir',
        'status',
        'wfd_destination_name',
        'wfd_destination_address',
        'wfd_latitude',
        'wfd_longitude',
        'foto',
        'cv_text',
        'cv_file',
        'cv_url',
        'activation_otp',
        'activation_otp_expires_at',
        'is_cv_approved'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function allowances()
    {
        return $this->hasMany(Allowance::class);
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }

    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'employee_training')->withPivot('status')->withTimestamps();
    }
}
