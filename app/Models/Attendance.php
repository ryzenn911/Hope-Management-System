<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in_morn',
        'check_out_morn',
        'check_in_aft',
        'check_out_aft',
        'morn_status',
        'aft_status',
        'device_info',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
