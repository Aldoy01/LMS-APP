<?php

namespace App\Http\Controllers;

use App\Models\LessonMaterial;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class MaterialController extends Controller
{
    public function show(LessonMaterial $material)
    {
        try {
            if (filter_var($material->url, FILTER_VALIDATE_URL)) {
                return redirect()->away($material->url);
            }

            $disk = Storage::disk('local');

            if (! $material->url || ! $disk->exists($material->url)) {
                return $this->unavailable($material);
            }

            $path = $disk->path($material->url);

            if (! is_file($path) || ! is_readable($path)) {
                return $this->unavailable($material);
            }

            $mimeType = $disk->mimeType($material->url) ?: 'application/octet-stream';
            $disposition = in_array($material->type, ['pdf', 'pdf-slide', 'video-upload'], true) ? 'inline' : 'attachment';
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $filename = Str::slug(pathinfo($material->title ?: 'materi', PATHINFO_FILENAME)) ?: 'materi';

            if ($extension) {
                $filename .= '.' . $extension;
            }

            return response()->file($path, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => $disposition . '; filename="' . $filename . '"',
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->view('materials.error', [
                'material' => $material,
            ], 200);
        }
    }

    private function unavailable(LessonMaterial $material)
    {
        return response()->view('materials.missing', [
            'material' => $material,
        ], 200);
    }
}
