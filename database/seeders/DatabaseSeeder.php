<?php

namespace Database\Seeders;

use App\Models\CaseReview;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LiveSession;
use App\Models\Order;
use App\Models\Payment;
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
                'original_price' => 3000000,
                'category' => 'Cyber Security',
                'level' => 'Professional',
                'status' => 'published',
                'mentor_id' => $mentor->id,
            ]
        );

        $modules = [
            ['category' => 'Basic', 'title' => 'Intro Cyber Security', 'summary' => 'Pengenalan konsep keamanan siber, tujuan belajar, etika, dan ruang lingkup defensive learning.', 'duration' => 90, 'lessons' => [
                ['title' => 'Fondasi Cyber Security', 'type' => 'video', 'duration' => 35, 'material' => 'Slide Intro Cyber Security'],
                ['title' => 'Checklist Etika dan Safety Lab', 'type' => 'checklist', 'duration' => 20, 'material' => 'Checklist Safety Lab'],
            ]],
            ['category' => 'Basic', 'title' => 'Networking Basic', 'summary' => 'Dasar jaringan, IP address, DNS, HTTP, port, dan cara membaca komunikasi antar sistem.', 'duration' => 120, 'lessons' => [
                ['title' => 'IP, Port, DNS, dan HTTP', 'type' => 'video', 'duration' => 45, 'material' => 'Mindmap Networking Basic'],
                ['title' => 'Latihan Membaca Alur Request', 'type' => 'worksheet', 'duration' => 30, 'material' => 'Worksheet Request Flow'],
            ]],
            ['category' => 'Basic', 'title' => 'Linux Basic', 'summary' => 'Perintah Linux dasar, struktur direktori, permission, service, dan log untuk kebutuhan lab keamanan.', 'duration' => 120, 'lessons' => [
                ['title' => 'Command Line dan File Permission', 'type' => 'video', 'duration' => 45, 'material' => 'Cheatsheet Linux Command'],
                ['title' => 'Praktik Navigasi dan Log', 'type' => 'checklist', 'duration' => 35, 'material' => 'Checklist Linux Lab'],
            ]],
            ['category' => 'Basic', 'title' => 'Web Security', 'summary' => 'Konsep dasar keamanan aplikasi web, authentication, session, input validation, dan common risk.', 'duration' => 105, 'lessons' => [
                ['title' => 'Anatomi Aplikasi Web', 'type' => 'video', 'duration' => 40, 'material' => 'Slide Web Security'],
                ['title' => 'Checklist Risiko Web Dasar', 'type' => 'pdf', 'duration' => 25, 'material' => 'Checklist Web Risk'],
            ]],
            ['category' => 'Basic', 'title' => 'Jenis Hacker', 'summary' => 'Memahami peran white hat, blue team, red team, black hat, bug hunter, dan batasan legal.', 'duration' => 75, 'lessons' => [
                ['title' => 'Role dan Etika Hacker', 'type' => 'video', 'duration' => 30, 'material' => 'Infografis Jenis Hacker'],
                ['title' => 'Studi Kasus Etika Keamanan', 'type' => 'ebook', 'duration' => 25, 'material' => 'Ebook Etika Cyber'],
            ]],
            ['category' => 'Intermediate', 'title' => 'SQL Injection', 'summary' => 'Memahami risiko query injection, dampak pada data, dan prinsip mitigasi input database.', 'duration' => 150, 'lessons' => [
                ['title' => 'Konsep SQL Injection', 'type' => 'video', 'duration' => 55, 'material' => 'Slide SQL Injection'],
                ['title' => 'Checklist Mitigasi SQLi', 'type' => 'checklist', 'duration' => 35, 'material' => 'Checklist SQLi Mitigation'],
            ]],
            ['category' => 'Intermediate', 'title' => 'XSS', 'summary' => 'Mempelajari reflected, stored, dan DOM XSS serta cara pencegahan berbasis encoding dan CSP.', 'duration' => 135, 'lessons' => [
                ['title' => 'Jenis dan Dampak XSS', 'type' => 'video', 'duration' => 50, 'material' => 'Slide XSS'],
                ['title' => 'Worksheet Output Encoding', 'type' => 'worksheet', 'duration' => 30, 'material' => 'Worksheet XSS Defense'],
            ]],
            ['category' => 'Intermediate', 'title' => 'Vulnerability', 'summary' => 'Cara membaca vulnerability, severity, CVE/CVSS, prioritas patch, dan risk-based remediation.', 'duration' => 120, 'lessons' => [
                ['title' => 'Membaca Temuan Vulnerability', 'type' => 'video', 'duration' => 45, 'material' => 'Template Risk Register'],
                ['title' => 'Prioritas Patch dan Remediasi', 'type' => 'pdf', 'duration' => 30, 'material' => 'Checklist Remediation'],
            ]],
            ['category' => 'Intermediate', 'title' => 'Reconnaissance', 'summary' => 'Dasar pengumpulan informasi secara legal untuk memahami permukaan serangan dan aset digital.', 'duration' => 135, 'lessons' => [
                ['title' => 'Recon Legal dan Asset Mapping', 'type' => 'video', 'duration' => 50, 'material' => 'Template Asset Mapping'],
                ['title' => 'Checklist Passive Recon', 'type' => 'checklist', 'duration' => 30, 'material' => 'Checklist Passive Recon'],
            ]],
            ['category' => 'Intermediate', 'title' => 'Wireshark', 'summary' => 'Mengenal packet capture, filter dasar, membaca trafik HTTP/DNS, dan indikasi anomali sederhana.', 'duration' => 150, 'lessons' => [
                ['title' => 'Analisis Trafik Dasar', 'type' => 'video', 'duration' => 60, 'material' => 'Sample PCAP dan Filter'],
                ['title' => 'Latihan Membaca Packet Capture', 'type' => 'worksheet', 'duration' => 40, 'material' => 'Worksheet Wireshark'],
            ]],
            ['category' => 'Practical', 'title' => 'Burp Suite', 'summary' => 'Workflow dasar proxy testing, intercept request, repeater, dan dokumentasi temuan web app.', 'duration' => 180, 'lessons' => [
                ['title' => 'Setup Burp Suite Lab', 'type' => 'video', 'duration' => 60, 'material' => 'Checklist Setup Burp'],
                ['title' => 'Praktik Proxy dan Repeater', 'type' => 'checklist', 'duration' => 60, 'material' => 'Lab Guide Burp Suite'],
            ]],
            ['category' => 'Practical', 'title' => 'Nmap', 'summary' => 'Praktik network discovery, port scanning yang aman, service detection, dan pencatatan hasil.', 'duration' => 150, 'lessons' => [
                ['title' => 'Nmap Discovery dan Service Scan', 'type' => 'video', 'duration' => 55, 'material' => 'Cheatsheet Nmap'],
                ['title' => 'Worksheet Hasil Scanning', 'type' => 'worksheet', 'duration' => 45, 'material' => 'Template Scan Result'],
            ]],
            ['category' => 'Practical', 'title' => 'Pentest Workflow', 'summary' => 'Alur kerja pentest legal dari scoping, recon, testing, evidence, validasi, sampai rekomendasi.', 'duration' => 210, 'lessons' => [
                ['title' => 'Tahapan Pentest Profesional', 'type' => 'video', 'duration' => 70, 'material' => 'Pentest Workflow Map'],
                ['title' => 'Template Scope dan Rules of Engagement', 'type' => 'ebook', 'duration' => 50, 'material' => 'Template ROE'],
            ]],
            ['category' => 'Practical', 'title' => 'Real Case Study', 'summary' => 'Studi kasus terarah untuk membaca masalah, menyusun hipotesis, memvalidasi risiko, dan membuat quick fix.', 'duration' => 180, 'lessons' => [
                ['title' => 'Analisis Case Aplikasi Web', 'type' => 'video', 'duration' => 65, 'material' => 'Case Study Pack'],
                ['title' => 'Menyusun Quick Fix', 'type' => 'worksheet', 'duration' => 45, 'material' => 'Worksheet Case Review'],
            ]],
            ['category' => 'Practical', 'title' => 'Reporting', 'summary' => 'Menyusun laporan temuan yang jelas untuk bisnis: evidence, severity, impact, rekomendasi, dan executive summary.', 'duration' => 150, 'lessons' => [
                ['title' => 'Struktur Laporan Security', 'type' => 'video', 'duration' => 50, 'material' => 'Template Pentest Report'],
                ['title' => 'Menulis Executive Summary', 'type' => 'pdf', 'duration' => 35, 'material' => 'Checklist Reporting'],
            ]],
        ];

        foreach ($modules as $moduleIndex => $moduleData) {
            $module = CourseModule::updateOrCreate(
                ['course_id' => $course->id, 'title' => $moduleData['title']],
                [
                    'summary' => $moduleData['summary'],
                    'category' => $moduleData['category'],
                    'duration_minutes' => $moduleData['duration'],
                    'sort_order' => $moduleIndex + 1,
                ]
            );

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
