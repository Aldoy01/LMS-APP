<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'course_module_id',
        'title',
        'summary',
        'content_type',
        'duration_minutes',
        'is_preview',
        'sort_order',
    ];

    protected $casts = [
        'is_preview' => 'boolean',
    ];

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    public function materials()
    {
        return $this->hasMany(LessonMaterial::class);
    }
}
