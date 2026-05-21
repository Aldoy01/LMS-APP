<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonMaterial extends Model
{
    protected $fillable = ['lesson_id', 'title', 'type', 'url', 'downloadable', 'sort_order'];

    protected $casts = [
        'downloadable' => 'boolean',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
