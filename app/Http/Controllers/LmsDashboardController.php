<?php

namespace App\Http\Controllers;

use App\Models\CaseReview;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lead;
use App\Models\LiveSession;
use App\Models\Order;
use App\Models\Question;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Throwable;

class LmsDashboardController extends Controller
{
    public function index()
    {
        try {
            return view('lms.dashboard', [
                'courses' => Course::with(['mentor', 'modules.lessons'])->latest()->get(),
                'metrics' => [
                    'courses' => Course::count(),
                    'participants' => Enrollment::distinct('user_id')->count('user_id'),
                    'revenue' => Order::where('status', 'paid')->sum('total'),
                    'conversion' => Lead::where('pipeline_stage', 'Closed Won')->count(),
                ],
                'liveSessions' => LiveSession::with('course')->orderBy('starts_at')->limit(4)->get(),
                'questions' => Question::latest()->limit(5)->get(),
                'caseReviews' => CaseReview::latest()->limit(4)->get(),
                'leads' => Lead::with('activities')->latest()->limit(6)->get(),
            ]);
        } catch (QueryException $exception) {
            return $this->fallbackDashboard();
        } catch (Throwable $exception) {
            report($exception);

            return $this->fallbackDashboard();
        }
    }

    private function fallbackDashboard()
    {
        return view('lms.dashboard', [
            'courses' => new Collection(),
            'metrics' => [
                'courses' => 0,
                'participants' => 0,
                'revenue' => 0,
                'conversion' => 0,
            ],
            'liveSessions' => new Collection(),
            'questions' => new Collection(),
            'caseReviews' => new Collection(),
            'leads' => new Collection(),
        ]);
    }

    public function show(Course $course)
    {
        $course->load(['mentor', 'modules.lessons.materials', 'liveSessions']);

        $isAdmin = in_array(optional(Auth::user()->role)->name, ['super-admin', 'admin-lms'], true);
        $enrollment = Enrollment::with('progress')
            ->where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        abort_unless($isAdmin || $enrollment, 403, 'Anda belum memiliki akses ke course ini.');

        $lessons = $course->modules->flatMap->lessons->values();
        $progress = $enrollment ? $enrollment->progress->keyBy('lesson_id') : collect();
        $completedLessons = $progress->where('progress_percent', 100)->count();
        $progressPercent = $lessons->count() > 0
            ? (int) round(($completedLessons / $lessons->count()) * 100)
            : 0;
        $continueLesson = $lessons->first(
            fn ($lesson) => optional($progress->get($lesson->id))->progress_percent !== 100
        ) ?? $lessons->first();
        $settings = \App\Models\SiteSetting::publicSettings();
        $whatsappNumber = preg_replace('/\D+/', '', $settings['contact_whatsapp'] ?? '08513332305');
        $whatsappTarget = str_starts_with($whatsappNumber, '0')
            ? '62' . substr($whatsappNumber, 1)
            : $whatsappNumber;
        $discussionGroups = [
            [
                'name' => 'Telegram Community',
                'description' => 'Diskusi materi, berbagi insight, dan bertanya bersama peserta Trama Verse.',
                'url' => 'https://t.me/tramaverse',
            ],
            [
                'name' => 'WhatsApp Support',
                'description' => 'Hubungi admin untuk kendala akses kelas, materi, dan progres belajar.',
                'url' => 'https://wa.me/' . $whatsappTarget,
            ],
            [
                'name' => 'Discord Lab Room',
                'description' => 'Ruang diskusi praktik, troubleshooting tools, dan review workflow.',
                'url' => 'https://discord.gg/tramaverse',
            ],
        ];

        return view('lms.course', compact(
            'course',
            'enrollment',
            'lessons',
            'progress',
            'completedLessons',
            'progressPercent',
            'continueLesson',
            'discussionGroups'
        ));
    }
}
