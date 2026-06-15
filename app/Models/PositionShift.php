<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PositionShift extends Model
{
    use HasFactory;

    protected $table = 'position_shifts';

    protected $fillable = [
        'position_id',
        'morn_in_time',
        'morn_out_time',
        'aft_in_time',
        'aft_out_time',
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
