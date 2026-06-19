@extends('layouts.lms', ['title' => 'Verifikasi Pembayaran'])

@section('content')
    <main class="main">
        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Admin LMS</span>
                    <h2>Verifikasi Pembayaran</h2>
                </div>
                <a class="button" style="background:var(--night)" href="{{ route('admin.courses.index') }}">Kembali ke Admin</a>
            </div>

            @if(session('status'))
                <div class="list-row" style="border-color:var(--teal);background:var(--teal-soft);margin-bottom:14px">
                    {{ session('status') }}
                </div>
            @endif
            @if($errors->any())
                <div class="list-row" style="border-color:var(--danger);background:var(--accent-soft);margin-bottom:14px">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="list">
                @forelse($orders as $order)
                    @php
                        $payment = $order->payments->sortByDesc('created_at')->first();
                    @endphp
                    <article class="list-row">
                        <div class="section-head" style="margin-bottom:8px;align-items:center">
                            <div>
                                <strong>{{ $order->invoice_number }} - {{ $order->user->name }}</strong>
                                <span class="muted">{{ $order->course->title }} / {{ $order->user->email }}</span>
                            </div>
                            <span class="badge">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                        </div>
                        <div class="meta">
                            <span class="badge">Total Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                            <span class="badge">Payment: {{ optional($payment)->status ?? '-' }}</span>
                            <span class="badge">Akun: {{ $order->user->is_active ? 'Aktif' : 'Menunggu pembayaran' }}</span>
                            @if(optional($payment)->proof_path)
                                <span class="badge">Ref: {{ $payment->proof_path }}</span>
                            @endif
                        </div>
                        @if($order->status === 'payment_submitted')
                            <form method="POST" action="{{ route('admin.payments.verify', $order) }}" class="meta">
                                @csrf
                                @method('PUT')
                                <button class="button" type="submit">Verifikasi, Aktifkan & Kirim Email</button>
                            </form>
                        @elseif($order->status === 'waiting_payment')
                            <p class="muted" style="margin-bottom:0">Menunggu peserta mengirim konfirmasi pembayaran.</p>
                        @endif
                    </article>
                @empty
                    <div class="card">Belum ada pembayaran.</div>
                @endforelse
            </div>

            <div style="margin-top:18px">
                {{ $orders->links() }}
            </div>
        </section>
    </main>
@endsection
