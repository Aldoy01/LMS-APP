<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
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

        return view('participant.dashboard', [
            'user' => Auth::user(),
            'enrollments' => $enrollments,
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
                'whatsapp' => 'https://wa.me/6281200000001',
                'email' => 'mailto:admin@techverselearning.test',
                'email_label' => 'admin@techverselearning.test',
                'whatsapp_label' => '+62 812-0000-0001',
            ],
        ]);
    }
}
