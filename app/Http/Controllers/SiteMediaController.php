<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SiteMediaController extends Controller
{
    public function show(string $filename)
    {
        abort_unless(preg_match('/^[A-Za-z0-9._-]+$/', $filename), Response::HTTP_NOT_FOUND);

        $path = storage_path('app/public/site/' . $filename);

        abort_unless(is_file($path), Response::HTTP_NOT_FOUND);

        return response()->file($path);
    }
}
