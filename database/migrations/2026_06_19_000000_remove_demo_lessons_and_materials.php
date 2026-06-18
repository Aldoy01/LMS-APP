<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $courseId = DB::table('courses')
            ->where('slug', 'cyber-security-playbook-for-business')
            ->value('id');

        if (! $courseId) {
            return;
        }

        $demoLessonIds = DB::table('lessons')
            ->join('course_modules', 'course_modules.id', '=', 'lessons.course_module_id')
            ->join('lesson_materials', 'lesson_materials.lesson_id', '=', 'lessons.id')
            ->where('course_modules.course_id', $courseId)
            ->where('lesson_materials.url', 'like', 'https://storage.example.test/lms/%')
            ->pluck('lessons.id')
            ->unique()
            ->values();

        if ($demoLessonIds->isEmpty()) {
            return;
        }

        // lesson_materials and progress_tracking are removed by their FK cascades.
        DB::table('lessons')
            ->whereIn('id', $demoLessonIds)
            ->delete();
    }

    public function down()
    {
        // Demo learning content is intentionally not restored.
    }
};
