<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\LessonMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModuleMaterialController extends Controller
{
    public function index(Course $course)
    {
        return view('admin.materials.index', [
            'course' => $course->load(['modules.lessons.materials']),
        ]);
    }

    public function updateModule(Request $request, CourseModule $module)
    {
        $data = $request->validate([
            'category' => ['required', 'in:Basic,Intermediate,Practical'],
            'duration_minutes' => ['required', 'integer', 'min:0', 'max:9999'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
        ]);

        $module->update($data);

        return back()->with('status', 'Urutan dan kategori modul berhasil diperbarui.');
    }

    public function storeMaterial(Request $request, Lesson $lesson)
    {
        $data = $this->validateMaterial($request);
        $data['lesson_id'] = $lesson->id;
        $data['url'] = $this->resolveMaterialUrl($request, $data['type']);
        $data['downloadable'] = $request->boolean('downloadable');

        LessonMaterial::create($data);

        return back()->with('status', 'Materi berhasil ditambahkan.');
    }

    public function updateMaterial(Request $request, LessonMaterial $material)
    {
        $data = $this->validateMaterial($request, false);

        if ($request->hasFile('file')) {
            $this->deleteStoredFile($material);
            $data['url'] = $this->resolveMaterialUrl($request, $data['type']);
        } elseif ($request->filled('external_url')) {
            $this->deleteStoredFile($material);
            $data['url'] = $request->input('external_url');
        }

        $data['downloadable'] = $request->boolean('downloadable');
        $material->update($data);

        return back()->with('status', 'Materi berhasil diperbarui.');
    }

    public function destroyMaterial(LessonMaterial $material)
    {
        $this->deleteStoredFile($material);
        $material->delete();

        return back()->with('status', 'Materi berhasil dihapus.');
    }

    private function validateMaterial(Request $request, bool $requireSource = true): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:pdf,pdf-slide,video-upload,video-embed,tool,resource'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'external_url' => [$requireSource ? 'required_without:file' : 'nullable', 'nullable', 'url', 'max:2000'],
            'file' => [$requireSource ? 'required_without:external_url' : 'nullable', 'nullable', 'file', 'max:51200'],
        ];

        return $request->validate($rules, [
            'file.max' => 'Ukuran file maksimal 50 MB agar LMS tetap ringan.',
            'external_url.required_without' => 'Isi link embed/resource atau upload file.',
            'file.required_without' => 'Upload file atau isi link embed/resource.',
        ]);
    }

    private function resolveMaterialUrl(Request $request, string $type): string
    {
        if ($request->hasFile('file')) {
            $directory = in_array($type, ['video-upload'], true) ? 'materials/videos' : 'materials/files';

            return $request->file('file')->store($directory, 'local');
        }

        return $request->input('external_url');
    }

    private function deleteStoredFile(LessonMaterial $material): void
    {
        if (! filter_var($material->url, FILTER_VALIDATE_URL) && Storage::disk('local')->exists($material->url)) {
            Storage::disk('local')->delete($material->url);
        }
    }
}
