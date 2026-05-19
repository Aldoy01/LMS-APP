<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseReview extends Model
{
    protected $fillable = [
        'user_id',
        'mentor_id',
        'business_name',
        'topic',
        'problem',
        'attachment_path',
        'risk_level',
        'quick_fix',
        'recommendation',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lead()
    {
        return $this->hasOne(Lead::class);
    }
}
