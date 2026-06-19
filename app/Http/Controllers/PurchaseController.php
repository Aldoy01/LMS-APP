<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{
    public function register()
    {
        return view('purchase.register', [
            'courses' => Course::with('modules.lessons')
                ->where('status', 'published')
                ->orderBy('level')
                ->orderBy('title')
                ->get(),
        ]);
    }

    public function create(Course $course)
    {
        return view('purchase.create', [
            'course' => $course->load('modules.lessons'),
        ]);
    }

    public function order(Request $request, Course $course)
    {
        $order = Order::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->where('status', 'waiting_payment')
            ->latest()
            ->first();

        if (! $order) {
            $invoice = 'INV-LMS-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

            $order = Order::create([
                'user_id' => Auth::id(),
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
        }

        return redirect()
            ->route('payments.show', $order->invoice_number)
            ->with('status', 'Silakan lanjutkan pembayaran dan konfirmasi transfer.');
    }

    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'max:40'],
            'company' => ['nullable', 'string', 'max:255'],
        ], [
            'email.unique' => 'Email sudah terdaftar. Silakan login jika sudah menjadi member, atau hubungi admin jika pembayaran masih diproses.',
        ]);

        $participantRole = Role::where('name', 'participant')->first();
        $user = User::create([
            'role_id' => optional($participantRole)->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'company' => $data['company'] ?? null,
            'is_active' => false,
            'password' => Hash::make(Str::random(48)),
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
            ->with('status', 'Akun sementara berhasil dibuat. Setelah pembayaran diverifikasi, akun akan aktif dan password login dikirim ke email Anda.');
    }
}
