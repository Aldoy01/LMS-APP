<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Auth;

class ParticipantDashboardController extends Controller
{
    public function index()
    {
        $enrollments = Enrollment::with([
            'course.mentor',
            'course.modules.lessons',
            'progress.lesson',
        ])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
        $modules = $enrollments
            ->flatMap(function ($enrollment) {
                return $enrollment->course->modules->map(function ($module) use ($enrollment) {
                    $lessonIds = $module->lessons->pluck('id');
                    $completedCount = $enrollment->progress
                        ->whereIn('lesson_id', $lessonIds)
                        ->where('progress_percent', 100)
                        ->count();
                    $lessonCount = $module->lessons->count();

                    return [
                        'course' => $enrollment->course,
                        'module' => $module,
                        'category' => $module->category,
                        'duration_minutes' => $module->duration_minutes,
                        'lesson_count' => $lessonCount,
                        'completed_count' => $completedCount,
                        'progress' => $lessonCount > 0 ? round(($completedCount / $lessonCount) * 100) : 0,
                    ];
                });
            })
            ->sortBy(fn ($item) => [$item['course']->title, $item['module']->sort_order])
            ->values();
        $settings = SiteSetting::publicSettings();
        $whatsappNumber = preg_replace('/\D+/', '', $settings['contact_whatsapp'] ?? '08513332305');
        $whatsappTarget = str_starts_with($whatsappNumber, '0') ? '62' . substr($whatsappNumber, 1) : $whatsappNumber;
        $email = $settings['contact_email'] ?? 'admin@techverselearning.test';

        return view('participant.dashboard', [
            'user' => Auth::user(),
            'enrollments' => $enrollments,
            'modules' => $modules,
            'announcements' => [
                [
                    'title' => 'Update Materi Mingguan',
                    'body' => 'Admin akan menambahkan materi praktik dan checklist baru secara berkala di modul aktif.',
                ],
                [
                    'title' => 'Gunakan Bantuan Jika Terkendala',
                    'body' => 'Hubungi admin jika akses course, progress, atau akun login belum sesuai.',
                ],
            ],
            'support' => [
                'whatsapp' => 'https://wa.me/' . $whatsappTarget,
                'email' => 'mailto:' . $email,
                'email_label' => $email,
                'whatsapp_label' => $settings['contact_whatsapp'] ?? '08513332305',
            ],
        ]);
    }
}
