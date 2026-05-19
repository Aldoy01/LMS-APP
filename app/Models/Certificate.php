<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = ['enrollment_id', 'certificate_number', 'file_path', 'issued_at'];

    protected $casts = [
        'issued_at' => 'datetime',
    ];
}
