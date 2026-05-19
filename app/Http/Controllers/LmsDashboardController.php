<?php

namespace App\Http\Controllers;

use App\Models\CaseReview;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lead;
use App\Models\LiveSession;
use App\Models\Order;
use App\Models\Question;

class LmsDashboardController extends Controller
{
    public function index()
    {
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
    }

    public function show(Course $course)
    {
        $course->load(['mentor', 'modules.lessons.materials', 'liveSessions']);

        return view('lms.course', compact('course'));
    }
}
