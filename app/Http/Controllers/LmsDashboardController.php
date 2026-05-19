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

        return view('lms.course', compact('course'));
    }
}
