<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'position_id',
        'employee_code',
        'name_kh',
        'name_en',
        'gender',
        'marital_status',
        'dob',
        'phone',
        'address_id',
        'education_id',
        'hire_date',
        'end_date',
        'status',
        'nssf_number',
        'labor_book',
        'family_number',
    ];

    /**
     * Relationship ទៅកាន់ User Account
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship ទៅកាន់ Position (តួនាទី)
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Relationship ទៅកាន់ Address (ទីលំនៅ)
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    /**
     * Relationship ទៅកាន់ EducationLevel (កម្រិតវប្បធម៌)
     */
    public function education(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class, 'education_id');
    }
    protected function casts(): array
    {
        return [
            // បន្ថែមបន្ទាត់នេះ ដើម្បីឱ្យវាបំប្លែង format អូតូពេលទាញចេញពី Database
            'dob' => 'datetime:d-m-Y',
            'hire_date' => 'datetime:d-m-Y',
            'end_date' => 'datetime:d-m-Y',
        ];
    }
}
