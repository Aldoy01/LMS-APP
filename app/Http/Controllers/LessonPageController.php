<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\ProgressTracking;
use App\Models\SiteSetting;
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
        $displayMaterials = $lesson->materials
            ->map(function ($material) {
                if ($material->type === 'video-upload') {
                    return ['material' => $material, 'kind' => 'video-upload', 'url' => null];
                }

                if ($material->type === 'video-embed' || $this->isYouTubeUrl($material->url)) {
                    return ['material' => $material, 'kind' => 'video-embed', 'url' => $this->embedUrl($material->url)];
                }

                if (in_array($material->type, ['pdf', 'pdf-slide'], true) || $this->isPdfUrl($material->url)) {
                    return ['material' => $material, 'kind' => 'pdf', 'url' => null];
                }

                return null;
            })
            ->filter()
            ->values();
        $firstDisplayMaterial = $displayMaterials->first();
        $primaryMaterial = $firstDisplayMaterial['material'] ?? null;
        $settings = SiteSetting::publicSettings();
        $whatsappNumber = preg_replace('/\D+/', '', $settings['contact_whatsapp'] ?? '08513332305');
        $whatsappTarget = str_starts_with($whatsappNumber, '0')
            ? '62' . substr($whatsappNumber, 1)
            : $whatsappNumber;

        return view('lms.lesson', [
            'course' => $course,
            'lesson' => $lesson,
            'primaryMaterial' => $primaryMaterial,
            'displayMaterials' => $displayMaterials,
            'lessons' => $lessons,
            'progress' => $progress,
            'completedLessons' => $completedLessons,
            'progressPercent' => $progressPercent,
            'currentIndex' => $currentIndex,
            'enrollment' => $enrollment,
            'previousLesson' => $currentIndex > 0 ? $lessons[$currentIndex - 1] : null,
            'nextLesson' => $currentIndex !== false && $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null,
            'discussionGroups' => [
                ['name' => 'Telegram Community', 'description' => 'Diskusi materi, berbagi insight, dan bertanya bersama peserta Trama Verse.', 'url' => 'https://t.me/tramaverse'],
                ['name' => 'WhatsApp Support', 'description' => 'Hubungi admin untuk kendala akses kelas dan progres belajar.', 'url' => 'https://wa.me/' . $whatsappTarget],
                ['name' => 'Discord Lab Room', 'description' => 'Tanyakan kendala praktik, troubleshooting tools, dan review workflow bersama komunitas.', 'url' => 'https://discord.gg/tramaverse'],
            ],
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

    private function isPdfUrl(?string $url): bool
    {
        if (! $url) {
            return false;
        }

        $path = filter_var($url, FILTER_VALIDATE_URL)
            ? parse_url($url, PHP_URL_PATH)
            : $url;

        return strtolower(pathinfo((string) $path, PATHINFO_EXTENSION)) === 'pdf';
    }
}
