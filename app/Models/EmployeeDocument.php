<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    protected $fillable = ['employee_id', 'file_name', 'file_path', 'file_size', 'file_type'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
