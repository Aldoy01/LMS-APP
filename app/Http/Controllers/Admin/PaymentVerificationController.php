<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CourseAccessActivated;
use App\Models\Enrollment;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class PaymentVerificationController extends Controller
{
    public function index()
    {
        return view('admin.payments.index', [
            'orders' => Order::with(['user', 'course', 'payments'])
                ->whereIn('status', ['waiting_payment', 'payment_submitted', 'paid'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function verify(Order $order)
    {
        if ($order->status === 'paid') {
            return back()->with('status', 'Pembayaran ini sudah pernah diverifikasi.');
        }

        if ($order->status !== 'payment_submitted') {
            return back()->withErrors([
                'payment' => 'Peserta belum mengirim konfirmasi pembayaran. Akun dan kelas belum dapat diaktifkan.',
            ]);
        }

        try {
            DB::transaction(function () use ($order) {
                $order->load(['user', 'course', 'payments']);
                $payment = $order->payments->sortByDesc('created_at')->firstOrFail();
                $temporaryPassword = null;

                if (! $order->user->is_active) {
                    $temporaryPassword = 'TV-' . strtoupper(Str::random(5)) . '-' . Str::random(7);
                    $order->user->update([
                        'is_active' => true,
                        'password' => Hash::make($temporaryPassword),
                        'activation_credentials_sent_at' => now(),
                    ]);
                }

                $payment->update([
                    'status' => 'paid',
                    'verified_at' => now(),
                ]);

                $order->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                Enrollment::updateOrCreate(
                    ['user_id' => $order->user_id, 'course_id' => $order->course_id],
                    ['order_id' => $order->id, 'access_type' => 'standard', 'started_at' => now()]
                );

                Mail::to($order->user->email)->send(
                    new CourseAccessActivated($order->fresh(['user', 'course']), $temporaryPassword)
                );
            });
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'email' => 'Verifikasi belum disimpan karena email akses gagal dikirim. Periksa konfigurasi SMTP lalu coba kembali.',
            ]);
        }

        return back()->with('status', 'Pembayaran terverifikasi. Akun dan kelas sudah aktif, serta informasi akses telah dikirim ke email peserta.');
    }
}
