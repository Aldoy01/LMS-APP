<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{
    public function create(Course $course)
    {
        return view('purchase.create', [
            'course' => $course->load('modules.lessons'),
        ]);
    }

    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'Email sudah terdaftar. Silakan login atau gunakan email lain.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $participantRole = Role::where('name', 'participant')->first();
        $user = User::create([
            'role_id' => optional($participantRole)->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'company' => $data['company'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        $invoice = 'INV-LMS-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        $order = Order::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'invoice_number' => $invoice,
            'subtotal' => $course->price,
            'discount' => 0,
            'total' => $course->price,
            'status' => 'waiting_payment',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'method' => 'manual_transfer',
            'status' => 'waiting_confirmation',
            'amount' => $order->total,
        ]);

        return redirect()
            ->route('payments.show', $order->invoice_number)
            ->with('status', 'Registrasi berhasil. Silakan lanjutkan pembayaran dan konfirmasi transfer.');
    }
}
