<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;

class LessonPageController extends Controller
{
    public function show(Course $course, Lesson $lesson)
    {
        $course->load(['modules.lessons.materials']);
        abort_unless($lesson->module && (int) $lesson->module->course_id === (int) $course->id, 404);

        $lessons = $course->modules->flatMap->lessons->values();
        $currentIndex = $lessons->search(fn ($item) => $item->id === $lesson->id);

        return view('lms.lesson', [
            'course' => $course,
            'lesson' => $lesson->load(['module', 'materials']),
            'previousLesson' => $currentIndex > 0 ? $lessons[$currentIndex - 1] : null,
            'nextLesson' => $currentIndex !== false && $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null,
        ]);
    }
}
