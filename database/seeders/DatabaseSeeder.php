<?php

namespace Database\Seeders;

use App\Models\CaseReview;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Lesson;
use App\Models\LessonMaterial;
use App\Models\LiveSession;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProgressTracking;
use App\Models\Question;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $roles = collect([
            ['name' => 'super-admin', 'label' => 'Super Admin', 'permissions' => ['*']],
            ['name' => 'admin-lms', 'label' => 'Admin LMS', 'permissions' => ['courses.manage', 'users.manage', 'reports.view']],
            ['name' => 'mentor', 'label' => 'Mentor / Expert', 'permissions' => ['questions.answer', 'cases.review']],
            ['name' => 'participant', 'label' => 'Peserta', 'permissions' => ['courses.learn', 'questions.submit', 'cases.submit']],
            ['name' => 'sales-cs', 'label' => 'Sales / CS', 'permissions' => ['leads.manage', 'followup.manage']],
        ])->mapWithKeys(fn ($role) => [$role['name'] => Role::updateOrCreate(
            ['name' => $role['name']],
            ['label' => $role['label'], 'permissions' => $role['permissions']]
        )]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@tramatekid.test'],
            [
                'role_id' => $roles['super-admin']->id,
                'name' => 'Super Admin LMS',
                'phone' => '081200000001',
                'company' => 'PT. Tera Multi Teknologi',
                'password' => Hash::make('password'),
            ]
        );

        $mentor = User::updateOrCreate(
            ['email' => 'mentor@tramatekid.test'],
            [
                'role_id' => $roles['mentor']->id,
                'name' => 'Cyber Security Mentor',
                'phone' => '081200000002',
                'company' => 'TRAMATEKID',
                'password' => Hash::make('password'),
            ]
        );

        $participant = User::updateOrCreate(
            ['email' => 'peserta@example.test'],
            [
                'role_id' => $roles['participant']->id,
                'name' => 'Budi Santoso',
                'phone' => '081200000003',
                'company' => 'PT Retail Aman Digital',
                'password' => Hash::make('password'),
            ]
        );

        $sales = User::updateOrCreate(
            ['email' => 'sales@tramatekid.test'],
            [
                'role_id' => $roles['sales-cs']->id,
                'name' => 'Sales CS Tramatekid',
                'phone' => '081200000004',
                'company' => 'TRAMATEKID',
                'password' => Hash::make('password'),
            ]
        );

        $course = Course::updateOrCreate(
            ['slug' => 'cyber-security-playbook-for-business'],
            [
                'title' => 'Cyber Security Playbook for Business',
                'summary' => 'Program praktis untuk membangun kebiasaan, checklist, SOP, dan respons awal keamanan siber bisnis.',
                'description' => 'Materi pembelajaran digital untuk founder, IT internal, dan operator bisnis yang ingin menerapkan baseline cyber security secara terstruktur.',
                'price' => 2500000,
                'level' => 'Professional',
                'status' => 'published',
                'mentor_id' => $mentor->id,
            ]
        );

        $modules = [
            [
                'title' => 'Security Baseline & Awareness',
                'summary' => 'Fondasi threat model, password policy, MFA, dan hygiene operasional.',
                'lessons' => [
                    ['title' => 'Membaca Risiko Siber untuk Bisnis', 'type' => 'video', 'duration' => 28],
                    ['title' => 'Checklist Password, MFA, dan Device Policy', 'type' => 'pdf', 'duration' => 18],
                ],
            ],
            [
                'title' => 'Incident Readiness & SOP',
                'summary' => 'Template SOP insiden, alur eskalasi, dan latihan tabletop sederhana.',
                'lessons' => [
                    ['title' => 'SOP Respons Insiden Awal', 'type' => 'ebook', 'duration' => 35],
                    ['title' => 'Simulasi Phishing dan Data Leak', 'type' => 'video', 'duration' => 42],
                ],
            ],
            [
                'title' => 'Case Solving & Upsell Review',
                'summary' => 'Cara submit masalah nyata dan mengubah temuan menjadi rencana perbaikan.',
                'lessons' => [
                    ['title' => 'Menulis Case Review yang Bisa Dianalisis', 'type' => 'video', 'duration' => 24],
                    ['title' => 'Prioritas Quick Fix vs Project Security', 'type' => 'checklist', 'duration' => 16],
                ],
            ],
        ];

        foreach ($modules as $moduleIndex => $moduleData) {
            $module = CourseModule::updateOrCreate(
                ['course_id' => $course->id, 'title' => $moduleData['title']],
                ['summary' => $moduleData['summary'], 'sort_order' => $moduleIndex + 1]
            );

            foreach ($moduleData['lessons'] as $lessonIndex => $lessonData) {
                $lesson = Lesson::updateOrCreate(
                    ['course_module_id' => $module->id, 'title' => $lessonData['title']],
                    [
                        'summary' => 'Materi inti dari blueprint LMS Cyber Security Playbook.',
                        'content_type' => $lessonData['type'],
                        'duration_minutes' => $lessonData['duration'],
                        'is_preview' => $moduleIndex === 0 && $lessonIndex === 0,
                        'sort_order' => $lessonIndex + 1,
                    ]
                );

                LessonMaterial::updateOrCreate(
                    ['lesson_id' => $lesson->id, 'title' => 'Materi ' . $lesson->title],
                    [
                        'type' => $lessonData['type'],
                        'url' => 'https://storage.example.test/lms/' . $lesson->id,
                        'downloadable' => in_array($lessonData['type'], ['pdf', 'ebook', 'checklist']),
                    ]
                );
            }
        }

        $order = Order::updateOrCreate(
            ['invoice_number' => 'INV-LMS-2026-0001'],
            [
                'user_id' => $participant->id,
                'course_id' => $course->id,
                'subtotal' => 2500000,
                'discount' => 250000,
                'total' => 2250000,
                'status' => 'paid',
                'paid_at' => now()->subDays(2),
            ]
        );

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'method' => 'manual_transfer',
                'status' => 'paid',
                'amount' => 2250000,
                'verified_at' => now()->subDays(2),
            ]
        );

        $enrollment = Enrollment::updateOrCreate(
            ['user_id' => $participant->id, 'course_id' => $course->id],
            ['order_id' => $order->id, 'access_type' => 'priority', 'started_at' => now()->subDay()]
        );

        $firstLessons = Lesson::whereHas('module', fn ($query) => $query->where('course_id', $course->id))->limit(3)->get();
        foreach ($firstLessons as $index => $lesson) {
            ProgressTracking::updateOrCreate(
                ['enrollment_id' => $enrollment->id, 'lesson_id' => $lesson->id],
                ['progress_percent' => $index < 2 ? 100 : 45, 'completed_at' => $index < 2 ? now()->subHours(4) : null]
            );
        }

        $session = LiveSession::updateOrCreate(
            ['course_id' => $course->id, 'title' => 'Live Q&A: Hardening Akun Admin dan Backup'],
            [
                'mentor_id' => $mentor->id,
                'description' => 'Problem solving interaktif untuk peserta prioritas.',
                'starts_at' => now()->addDays(5)->setTime(19, 30),
                'ends_at' => now()->addDays(5)->setTime(21, 00),
                'meeting_url' => 'https://meet.example.test/cyber-playbook',
            ]
        );

        Question::updateOrCreate(
            ['live_session_id' => $session->id, 'user_id' => $participant->id, 'subject' => 'Prioritas hardening server public'],
            ['body' => 'Apa quick win yang perlu dilakukan untuk server aplikasi yang sudah live?', 'priority' => 'priority', 'status' => 'submitted']
        );

        $caseReview = CaseReview::updateOrCreate(
            ['user_id' => $participant->id, 'business_name' => 'PT Retail Aman Digital', 'topic' => 'Audit akses admin aplikasi'],
            [
                'mentor_id' => $mentor->id,
                'problem' => 'Akun admin digunakan bersama dan belum ada MFA. Tim butuh rekomendasi quick fix dan rencana audit.',
                'risk_level' => 'high',
                'quick_fix' => 'Pisahkan akun per pengguna, aktifkan MFA, reset password bersama, dan review log login.',
                'recommendation' => 'Lanjutkan ke security audit dan implementasi access governance.',
                'status' => 'reviewed',
            ]
        );

        $lead = Lead::updateOrCreate(
            ['case_review_id' => $caseReview->id],
            [
                'user_id' => $participant->id,
                'company' => 'PT Retail Aman Digital',
                'contact_name' => $participant->name,
                'contact_email' => $participant->email,
                'service_interest' => 'security audit',
                'pipeline_stage' => 'Warm Lead',
                'estimated_value' => 35000000,
                'next_follow_up_at' => now()->addDays(3),
            ]
        );

        LeadActivity::updateOrCreate(
            ['lead_id' => $lead->id, 'user_id' => $sales->id, 'type' => 'note'],
            ['notes' => 'Follow-up kebutuhan audit akses admin dan proposal managed security.', 'activity_at' => now()]
        );
    }
}
