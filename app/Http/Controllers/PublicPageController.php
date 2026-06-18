<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\LiveSession;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Throwable;

class PublicPageController extends Controller
{
    public function programs()
    {
        try {
            $courses = Course::with(['mentor', 'modules.lessons'])
                ->where('status', 'published')
                ->latest()
                ->get();

            $liveSessions = LiveSession::with(['course', 'mentor'])
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->limit(8)
                ->get();
        } catch (QueryException $exception) {
            $courses = new Collection();
            $liveSessions = new Collection();
        } catch (Throwable $exception) {
            report($exception);
            $courses = new Collection();
            $liveSessions = new Collection();
        }

        return view('public.programs', compact('courses', 'liveSessions'));
    }

    public function about()
    {
        return view('public.about');
    }

    public function privacy()
    {
        return view('public.legal', [
            'pageTitle' => 'Privacy Policy',
            'eyebrow' => 'Keamanan & Privasi',
            'intro' => 'Kami menjaga data peserta agar digunakan secara wajar, aman, dan hanya untuk mendukung layanan pembelajaran.',
            'sections' => [
                ['Informasi yang Kami Kelola', 'Data yang dapat diproses meliputi nama, email, nomor kontak, informasi pembayaran, aktivitas kelas, serta progres pembelajaran.'],
                ['Tujuan Penggunaan Data', 'Data digunakan untuk mengelola akun, enrollment, pembayaran, akses materi, dukungan peserta, komunikasi kelas, dan peningkatan kualitas layanan.'],
                ['Penyimpanan dan Keamanan', 'Kami menerapkan pembatasan akses dan praktik keamanan yang wajar. Peserta tetap bertanggung jawab menjaga kerahasiaan kredensial akun.'],
                ['Hak Peserta', 'Peserta dapat meminta koreksi data atau menghubungi admin untuk pertanyaan terkait penggunaan informasi pribadi.'],
            ],
        ]);
    }

    public function terms()
    {
        return view('public.legal', [
            'pageTitle' => 'Terms & Conditions',
            'eyebrow' => 'Ketentuan Layanan',
            'intro' => 'Ketentuan ini membantu menjaga pengalaman belajar yang aman, adil, dan nyaman bagi seluruh peserta Trama Verse.',
            'sections' => [
                ['Akses Akun', 'Akun peserta bersifat pribadi. Pengguna wajib memberikan informasi yang benar dan tidak membagikan akses kepada pihak lain.'],
                ['Materi Pembelajaran', 'Materi hanya boleh digunakan untuk kebutuhan belajar peserta dan tidak boleh disalin, dijual, atau didistribusikan tanpa izin.'],
                ['Pembayaran dan Enrollment', 'Akses program diberikan setelah proses pembayaran dan verifikasi selesai sesuai informasi yang ditampilkan pada halaman pembelian.'],
                ['Perilaku Komunitas', 'Peserta wajib berkomunikasi dengan hormat dan tidak melakukan spam, penyalahgunaan sistem, atau aktivitas yang merugikan peserta lain.'],
            ],
        ]);
    }
}
