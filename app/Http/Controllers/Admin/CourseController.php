<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index()
    {
        return view('admin.courses.index', [
            'courses' => Course::with(['mentor', 'modules.lessons'])->latest()->paginate(12),
        ]);
    }

    public function create()
    {
        return view('admin.courses.form', [
            'course' => new Course([
                'status' => 'draft',
                'level' => 'Beginner',
                'category' => 'Cyber Security',
            ]),
            'mentors' => $this->mentors(),
            'method' => 'POST',
            'action' => route('admin.courses.store'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug($data['title']);

        Course::create($data);

        return redirect()
            ->route('admin.courses.index')
            ->with('status', 'Course berhasil dibuat.');
    }

    public function edit(Course $course)
    {
        return view('admin.courses.form', [
            'course' => $course,
            'mentors' => $this->mentors(),
            'method' => 'PUT',
            'action' => route('admin.courses.update', $course),
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $data = $this->validatedData($request);

        if ($course->title !== $data['title']) {
            $data['slug'] = $this->uniqueSlug($data['title'], $course->id);
        }

        $course->update($data);

        return redirect()
            ->route('admin.courses.index')
            ->with('status', 'Course berhasil diperbarui.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'original_price' => ['nullable', 'integer', 'min:0', 'gte:price'],
            'category' => ['required', 'in:Cyber Security,Programming,AI & Automation'],
            'level' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:draft,published,archived'],
            'mentor_id' => ['nullable', 'exists:users,id'],
        ]);
    }

    private function mentors()
    {
        return User::whereHas('role', fn ($query) => $query->where('name', 'mentor'))
            ->orderBy('name')
            ->get();
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 2;

        while (Course::where('slug', $slug)->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
