<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ParticipantDashboardController extends Controller
{
    public function profile()
    {
        return view('participant.profile', [
            'user' => Auth::user()->load('role'),
            'enrollmentCount' => Enrollment::where('user_id', Auth::id())->count(),
        ]);
    }

    public function index()
    {
        $enrollments = Enrollment::with([
            'course.mentor',
            'course.modules.lessons.materials',
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
        $enrolledCourseIds = $enrollments->pluck('course_id')->all();
        $recommendedCourses = Course::with('modules.lessons')
            ->where('status', 'published')
            ->whereNotIn('id', $enrolledCourseIds)
            ->orderBy('level')
            ->limit(4)
            ->get();
        $latestCourses = Course::with('modules.lessons')
            ->where('status', 'published')
            ->latest()
            ->limit(4)
            ->get();
        $settings = SiteSetting::publicSettings();
        $whatsappNumber = preg_replace('/\D+/', '', $settings['contact_whatsapp'] ?? '08513332305');
        $whatsappTarget = str_starts_with($whatsappNumber, '0') ? '62' . substr($whatsappNumber, 1) : $whatsappNumber;
        $email = $settings['contact_email'] ?? 'admin@tramaverse.test';

        return view('participant.dashboard', [
            'user' => Auth::user(),
            'enrollments' => $enrollments,
            'modules' => $modules,
            'recommendedCourses' => $recommendedCourses,
            'latestCourses' => $latestCourses,
            'discussionGroups' => [
                [
                    'name' => 'Telegram Community',
                    'description' => 'Diskusi cepat seputar roadmap, tugas modul, dan update kelas.',
                    'url' => 'https://t.me/tramaverse',
                ],
                [
                    'name' => 'WhatsApp Group',
                    'description' => 'Koordinasi kelas, pengumuman cepat, dan bantuan akses bersama admin.',
                    'url' => 'https://wa.me/' . $whatsappTarget,
                ],
                [
                    'name' => 'Discord Lab Room',
                    'description' => 'Ruang tanya jawab praktik lab, tools, dan troubleshooting peserta.',
                    'url' => 'https://discord.gg/tramaverse',
                ],
            ],
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

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:255'],
        ], [
            'email.unique' => 'Email sudah digunakan akun lain.',
        ]);

        $user->update($data);

        return redirect()
            ->route('participant.profile')
            ->with('profile_status', 'Data peserta berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak sama.',
        ]);

        $user = Auth::user();

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password lama tidak sesuai.'])
                ->withInput();
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
            'remember_token' => null,
        ])->save();

        return redirect(route('participant.profile') . '#password')
            ->with('password_status', 'Password berhasil diperbarui.');
    }
}
