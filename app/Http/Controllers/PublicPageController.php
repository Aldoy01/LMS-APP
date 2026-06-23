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

    public function faq()
    {
        return view('public.faq', [
            'faqs' => [
                [
                    'question' => 'Apa itu Trama Verse?',
                    'answer' => 'Trama Verse adalah platform pembelajaran online yang menyediakan berbagai course teknologi untuk membantu peserta belajar skill digital secara lebih mudah, terarah, dan praktis.',
                ],
                [
                    'question' => 'Siapa saja yang cocok belajar di Trama Verse?',
                    'answer' => 'Trama Verse cocok untuk pemula, mahasiswa, profesional, pelaku bisnis, maupun siapa saja yang ingin meningkatkan skill digital dan teknologi dari dasar.',
                ],
                [
                    'question' => 'Apa saja materi yang didapatkan?',
                    'answer' => 'Peserta akan mendapatkan materi pembelajaran berupa video, modul tulisan, lab praktik, quiz, dan panduan belajar yang disusun secara terstruktur.',
                ],
                [
                    'question' => 'Apakah course bisa diakses selamanya?',
                    'answer' => 'Ya. Setelah bergabung, peserta dapat mengakses course yang sudah dibeli selamanya tanpa batas waktu.',
                ],
                [
                    'question' => 'Apakah ada lab praktik?',
                    'answer' => 'Ya. Beberapa course dilengkapi dengan lab praktik agar peserta tidak hanya memahami teori, tetapi juga bisa langsung mencoba dan menerapkan materi.',
                ],
                [
                    'question' => 'Apakah ada quiz di setiap materi?',
                    'answer' => 'Ya. Quiz disediakan untuk membantu peserta mengukur pemahaman setelah mempelajari materi tertentu.',
                ],
                [
                    'question' => 'Apakah mendapatkan sertifikat?',
                    'answer' => 'Ya. Peserta akan mendapatkan sertifikat penyelesaian setelah menyelesaikan course sesuai ketentuan yang berlaku.',
                ],
                [
                    'question' => 'Apakah ada grup diskusi?',
                    'answer' => 'Ya. Peserta dapat bergabung ke grup diskusi melalui Discord dan Telegram untuk tanya jawab, sharing, dan berdiskusi bersama peserta lainnya.',
                ],
                [
                    'question' => 'Apakah harus punya basic IT dulu?',
                    'answer' => 'Tidak harus. Materi di Trama Verse dibuat agar mudah dipahami, termasuk untuk peserta yang baru mulai belajar teknologi dari dasar.',
                ],
                [
                    'question' => 'Bagaimana cara mulai belajar?',
                    'answer' => 'Peserta cukup memilih course yang tersedia, melakukan pendaftaran, menyelesaikan pembayaran, lalu mulai belajar melalui platform Trama Verse.',
                ],
            ],
        ]);
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
