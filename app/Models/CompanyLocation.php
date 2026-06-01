<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyLocation extends Model
{
    use HasFactory;

    // បន្ថែមជួរនេះ ដើម្បីអនុញ្ញាតឱ្យបោះទិន្នន័យចូលក្នុង Column ទាំងនេះ
    protected $fillable = [
        'id', // ត្រូវតែថែម id ដែរ ព្រោះបងប្រើវាក្នុង updateOrCreate
        'lat',
        'lng',
        'radius'
    ];
    
}
