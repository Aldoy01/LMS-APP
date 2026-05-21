<?php

namespace App\Http\Controllers;

use App\Models\LessonMaterial;
use Illuminate\Support\Facades\Log;
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
            Log::warning('Material preview failed', [
                'material_id' => $material->id,
                'url' => $material->url,
                'message' => $exception->getMessage(),
            ]);

            return $this->unavailable($material, 'LMS belum bisa membuka file ini. Upload ulang PDF/video atau ganti link resource dari menu Kelola Materi.');
        }
    }

    private function unavailable(LessonMaterial $material, ?string $message = null)
    {
        $title = e($material->title ?: 'Materi LMS');
        $message = e($message ?: 'File materi belum ditemukan di storage LMS. Jika ini materi lama atau hasil upload sebelum redeploy, admin perlu upload ulang PDF/video atau mengganti link resource.');
        $backUrl = e(url()->previous() ?: url('/home'));

        return response(<<<HTML
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Materi Belum Tersedia</title>
    <style>
        body{margin:0;font-family:Arial,sans-serif;background:#f7f6ff;color:#100b3f}
        main{min-height:100vh;display:grid;place-items:center;padding:24px}
        section{width:min(720px,100%);background:#fff;border:1px solid #dcd8ff;border-radius:8px;padding:32px;box-shadow:0 14px 34px rgba(16,11,63,.12)}
        span{display:block;color:#8921C2;font-weight:700;text-transform:uppercase;font-size:13px;letter-spacing:.08em}
        h1{margin:12px 0 10px;font-size:30px}
        p{line-height:1.7;color:#68728c}
        a{display:inline-block;margin-top:18px;background:linear-gradient(135deg,#8921C2,#FE39A4);color:#fff;text-decoration:none;padding:12px 18px;border-radius:8px;font-weight:700}
    </style>
</head>
<body>
    <main>
        <section>
            <span>Materi Belum Tersedia</span>
            <h1>{$title}</h1>
            <p>{$message}</p>
            <a href="{$backUrl}">Kembali</a>
        </section>
    </main>
</body>
</html>
HTML, 200);
    }
}
