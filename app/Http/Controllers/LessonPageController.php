<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\ProgressTracking;
use Illuminate\Support\Facades\Auth;

class LessonPageController extends Controller
{
    public function show(Course $course, Lesson $lesson)
    {
        $course->load(['modules.lessons.materials']);
        abort_unless($lesson->module && (int) $lesson->module->course_id === (int) $course->id, 404);

        $enrollment = $this->resolveEnrollment($course);
        if ($enrollment && ! $enrollment->started_at) {
            $enrollment->update(['started_at' => now()]);
        }
        $lessons = $course->modules->flatMap->lessons->values();
        $currentIndex = $lessons->search(fn ($item) => $item->id === $lesson->id);
        $progress = $enrollment
            ? $enrollment->progress()->get()->keyBy('lesson_id')
            : collect();
        $completedLessons = $progress->where('progress_percent', 100)->count();
        $progressPercent = $lessons->count() > 0
            ? (int) round(($completedLessons / $lessons->count()) * 100)
            : 0;
        $lesson->load(['module', 'materials']);
        $primaryMaterial = $lesson->materials->first(fn ($material) => $material->type === 'video-upload')
            ?? $lesson->materials->first(fn ($material) => $material->type === 'video-embed')
            ?? $lesson->materials->first(fn ($material) => $this->isYouTubeUrl($material->url))
            ?? $lesson->materials->first(fn ($material) => in_array($material->type, ['pdf', 'pdf-slide'], true));
        $embedUrl = $primaryMaterial && ($primaryMaterial->type === 'video-embed' || $this->isYouTubeUrl($primaryMaterial->url))
            ? $this->embedUrl($primaryMaterial->url)
            : null;

        return view('lms.lesson', [
            'course' => $course,
            'lesson' => $lesson,
            'primaryMaterial' => $primaryMaterial,
            'embedUrl' => $embedUrl,
            'lessons' => $lessons,
            'progress' => $progress,
            'completedLessons' => $completedLessons,
            'progressPercent' => $progressPercent,
            'currentIndex' => $currentIndex,
            'enrollment' => $enrollment,
            'previousLesson' => $currentIndex > 0 ? $lessons[$currentIndex - 1] : null,
            'nextLesson' => $currentIndex !== false && $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null,
        ]);
    }

    public function complete(Course $course, Lesson $lesson)
    {
        abort_unless($lesson->module && (int) $lesson->module->course_id === (int) $course->id, 404);

        $enrollment = $this->resolveEnrollment($course, true);

        ProgressTracking::query()->updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'progress_percent' => 100,
                'completed_at' => now(),
            ]
        );

        $course->load('modules.lessons');
        $lessons = $course->modules->flatMap->lessons->values();
        $currentIndex = $lessons->search(fn ($item) => $item->id === $lesson->id);
        $nextLesson = $currentIndex !== false && $currentIndex < $lessons->count() - 1
            ? $lessons[$currentIndex + 1]
            : null;

        if ($nextLesson) {
            return redirect()
                ->route('lms.lessons.show', [$course, $nextLesson])
                ->with('lesson_status', 'Pelajaran selesai. Lanjut ke materi berikutnya.');
        }

        $enrollment->update(['completed_at' => now()]);

        return redirect()
            ->route('participant.dashboard')
            ->with('lesson_status', 'Selamat, course berhasil diselesaikan.');
    }

    private function resolveEnrollment(Course $course, bool $required = false): ?Enrollment
    {
        $isAdmin = in_array(optional(Auth::user()->role)->name, ['super-admin', 'admin-lms'], true);
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        if ($required) {
            abort_unless($enrollment, 403, 'Progress hanya dapat disimpan oleh peserta yang terdaftar.');
        } else {
            abort_unless($isAdmin || $enrollment, 403, 'Anda belum memiliki akses ke course ini.');
        }

        return $enrollment;
    }

    private function embedUrl(?string $url): ?string
    {
        if (! $url || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        $parts = parse_url($url);
        $host = preg_replace('/^www\./', '', strtolower($parts['host'] ?? ''));
        $path = trim($parts['path'] ?? '', '/');
        $videoId = null;

        if ($host === 'youtu.be') {
            $videoId = explode('/', $path)[0] ?? null;
        } elseif (in_array($host, ['youtube.com', 'm.youtube.com', 'youtube-nocookie.com'], true)) {
            parse_str($parts['query'] ?? '', $query);

            if ($path === 'watch') {
                $videoId = $query['v'] ?? null;
            } elseif (preg_match('~^(?:embed|shorts|live)/([^/]+)~', $path, $matches)) {
                $videoId = $matches[1];
            }
        }

        if ($videoId && preg_match('/^[A-Za-z0-9_-]{6,20}$/', $videoId)) {
            return 'https://www.youtube-nocookie.com/embed/' . $videoId . '?rel=0&modestbranding=1';
        }

        return $url;
    }

    private function isYouTubeUrl(?string $url): bool
    {
        if (! $url || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $host = preg_replace('/^www\./', '', strtolower(parse_url($url, PHP_URL_HOST) ?? ''));

        return in_array($host, [
            'youtube.com',
            'm.youtube.com',
            'youtube-nocookie.com',
            'youtu.be',
        ], true);
    }
}
