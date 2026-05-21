<?php

namespace App\Http\Controllers;

use App\Models\LessonMaterial;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function show(LessonMaterial $material)
    {
        if (filter_var($material->url, FILTER_VALIDATE_URL)) {
            return redirect()->away($material->url);
        }

        abort_unless(Storage::disk('local')->exists($material->url), 404);

        return response()->file(storage_path('app/' . $material->url));
    }
}
