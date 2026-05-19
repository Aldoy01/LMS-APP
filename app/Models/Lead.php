<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'user_id',
        'case_review_id',
        'company',
        'contact_name',
        'contact_email',
        'service_interest',
        'pipeline_stage',
        'estimated_value',
        'next_follow_up_at',
    ];

    protected $casts = [
        'next_follow_up_at' => 'datetime',
    ];

    public function activities()
    {
        return $this->hasMany(LeadActivity::class);
    }
}
