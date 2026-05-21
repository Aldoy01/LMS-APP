<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PaymentConfirmationController extends Controller
{
    public function show(string $invoice)
    {
        return view('payments.show', [
            'order' => $this->findOrder($invoice),
        ]);
    }

    public function confirm(Request $request, string $invoice)
    {
        $order = $this->findOrder($invoice);
        $payment = $order->payments()->latest()->firstOrFail();

        $data = $request->validate([
            'payer_name' => ['required', 'string', 'max:255'],
            'payer_bank' => ['required', 'string', 'max:120'],
            'transfer_date' => ['required', 'date'],
            'proof_reference' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $payment->update([
            'status' => 'submitted',
            'proof_path' => $data['proof_reference'],
            'gateway_payload' => $data,
        ]);

        $order->update(['status' => 'payment_submitted']);

        return back()->with('status', 'Konfirmasi pembayaran terkirim. Admin akan memverifikasi dan mengaktifkan akun kelas.');
    }

    private function findOrder(string $invoice): Order
    {
        return Order::with(['course.modules.lessons', 'user', 'payments'])
            ->where('invoice_number', $invoice)
            ->firstOrFail();
    }
}
