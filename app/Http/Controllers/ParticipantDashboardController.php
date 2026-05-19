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
        ]);
    }
}
