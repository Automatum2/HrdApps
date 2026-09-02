<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_training',
        'skill_dipelajari',
        'tanggal_mulai',
        'tanggal_selesai',
        'manager_id'
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_training')->withPivot('status')->withTimestamps();
    }
}
