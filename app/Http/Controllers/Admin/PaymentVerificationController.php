<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Order;

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
        $payment = $order->payments()->latest()->firstOrFail();

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

        return back()->with('status', 'Pembayaran terverifikasi. Akun peserta sudah mendapat akses kelas.');
    }
}
