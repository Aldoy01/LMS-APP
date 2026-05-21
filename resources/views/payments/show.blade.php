@extends('layouts.lms', ['title' => 'Konfirmasi Pembayaran'])

@section('content')
    <main class="main">
        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Invoice {{ $order->invoice_number }}</span>
                    <h2>Instruksi & Konfirmasi Pembayaran</h2>
                </div>
                <a class="button" style="background:#172033" href="{{ route('lms.dashboard') }}">Kembali ke Home</a>
            </div>

            @if(session('status'))
                <div class="list-row" style="border-color:#0f766e;background:#eef6f5;margin-bottom:14px">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid split">
                <div class="card">
                    <span class="eyebrow">Paket Dipilih</span>
                    <h3>{{ $order->course->title }}</h3>
                    <p>{{ $order->course->summary }}</p>
                    <div class="meta">
                        <span class="badge">Status: {{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                        <span class="badge">Total: Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>

                    <div class="list" style="margin-top:14px">
                        <div class="list-row">
                            <strong>Transfer Manual</strong>
                            <span class="muted">Bank: BCA / Mandiri Virtual Manual</span><br>
                            <span class="muted">No. Rekening: 1234567890</span><br>
                            <span class="muted">Atas Nama: TECHVERSE Learning</span>
                        </div>
                        <div class="list-row">
                            <strong>Catatan</strong>
                            <span class="muted">Gunakan nominal sesuai invoice agar admin mudah melakukan verifikasi.</span>
                        </div>
                    </div>
                </div>

                <form class="card" method="POST" action="{{ route('payments.confirm', $order->invoice_number) }}">
                    @csrf
                    <span class="eyebrow">Konfirmasi Pembayaran</span>
                    <h3>Isi Data Transfer</h3>
                    <div class="form-grid" style="grid-template-columns:1fr">
                        <label>
                            <span>Nama Pengirim</span>
                            <input name="payer_name" value="{{ old('payer_name') }}" required>
                            @error('payer_name') <small>{{ $message }}</small> @enderror
                        </label>
                        <label>
                            <span>Bank / E-wallet Pengirim</span>
                            <input name="payer_bank" value="{{ old('payer_bank') }}" required>
                            @error('payer_bank') <small>{{ $message }}</small> @enderror
                        </label>
                        <label>
                            <span>Tanggal Transfer</span>
                            <input type="date" name="transfer_date" value="{{ old('transfer_date') }}" required>
                            @error('transfer_date') <small>{{ $message }}</small> @enderror
                        </label>
                        <label>
                            <span>Nomor Referensi / Link Bukti Transfer</span>
                            <input name="proof_reference" value="{{ old('proof_reference') }}" required>
                            @error('proof_reference') <small>{{ $message }}</small> @enderror
                        </label>
                        <label>
                            <span>Catatan</span>
                            <textarea name="note" rows="4">{{ old('note') }}</textarea>
                            @error('note') <small>{{ $message }}</small> @enderror
                        </label>
                    </div>
                    <div class="meta" style="margin-top:18px">
                        <button class="button" type="submit">Kirim Konfirmasi</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
@endsection
