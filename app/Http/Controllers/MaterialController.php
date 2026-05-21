<?php

namespace App\Http\Controllers;

use App\Models\LessonMaterial;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function show(LessonMaterial $material)
    {
        if (filter_var($material->url, FILTER_VALIDATE_URL)) {
            return redirect()->away($material->url);
        }

        if (! Storage::disk('local')->exists($material->url)) {
            return response()->view('materials.missing', [
                'material' => $material,
            ], 404);
        }

        $path = Storage::disk('local')->path($material->url);
        $mimeType = Storage::disk('local')->mimeType($material->url) ?: 'application/octet-stream';
        $disposition = in_array($material->type, ['pdf', 'pdf-slide', 'video-upload'], true) ? 'inline' : 'attachment';
        $filename = Str::slug(pathinfo($material->title, PATHINFO_FILENAME)) ?: 'materi';

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $disposition . '; filename="' . $filename . '"',
        ]);
    }
}
