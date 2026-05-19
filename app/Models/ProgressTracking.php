<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressTracking extends Model
{
    protected $table = 'progress_tracking';

    protected $fillable = ['enrollment_id', 'lesson_id', 'progress_percent', 'completed_at'];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
