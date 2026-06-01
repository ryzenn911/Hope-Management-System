<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [
        'employee_id',
        'leave_type',
        'reason',
        'start_date',
        'end_date',
        'total_days',
        'status'
    ];

    protected $casts = [
        'start_date' => 'datetime:d-m-Y',
        'end_date'   => 'datetime:d-m-Y',
        'created_at' => 'datetime:d-m-Y H:i',
    ];

    /**
     * Relationship ទៅកាន់ Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // ក្នុងឯកសារ app/Models/Employee.php
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
