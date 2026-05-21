<?php

namespace App\Http\Controllers;

use App\Models\LessonMaterial;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MaterialController extends Controller
{
    public function show(LessonMaterial $material)
    {
        try {
            if (filter_var($material->url, FILTER_VALIDATE_URL)) {
                return redirect()->away($material->url);
            }

            if (! $material->url || ! Storage::disk('local')->exists($material->url)) {
                return response()->view('materials.missing', [
                    'material' => $material,
                ], 404);
            }

            $path = Storage::disk('local')->path($material->url);
            $mimeType = Storage::disk('local')->mimeType($material->url) ?: 'application/octet-stream';
            $disposition = in_array($material->type, ['pdf', 'pdf-slide', 'video-upload'], true) ? 'inline' : 'attachment';
            $filename = (Str::slug(pathinfo($material->title, PATHINFO_FILENAME)) ?: 'materi') . '.' . pathinfo($path, PATHINFO_EXTENSION);

            return response()->file($path, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => $disposition . '; filename="' . $filename . '"',
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->view('materials.error', [
                'material' => $material,
            ], 500);
        }
    }
}
