<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadActivity extends Model
{
    protected $fillable = ['lead_id', 'user_id', 'type', 'notes', 'activity_at'];

    protected $casts = [
        'activity_at' => 'datetime',
    ];
}
