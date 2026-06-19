@extends('layouts.lms', ['title' => 'Beli Paket Modul'])

@section('content')
    <section class="hero">
        <div>
            <span class="eyebrow" style="color:var(--gold)">Beli Paket Modul</span>
            <h1>{{ $course->title }}</h1>
            <p>{{ $course->summary }}</p>
            <div class="chips">
                <span class="chip">{{ $course->modules->count() }} modul</span>
                <span class="chip">{{ $course->modules->sum('duration_minutes') }} menit estimasi</span>
                <span class="chip">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="hero-panel">
            <img class="hero-logo" src="{{ asset('images/trama-verse-logo.png') }}" alt="Trama Verse">
            <strong>Alur Pembelian</strong>
            <p style="margin:0;color:var(--hero-copy)">Isi data, transfer dan konfirmasi pembayaran. Akun dibuat otomatis dan informasi login dikirim melalui email setelah disetujui admin.</p>
        </div>
    </section>

    <main class="main">
        <section class="section grid split">
            <div>
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Pilihan Paket</span>
                        <h2>Struktur Modul</h2>
                    </div>
                </div>
                <div class="list">
                    @foreach($course->modules as $module)
                        <div class="list-row">
                            <strong>{{ $module->category }} / Modul {{ $module->sort_order }} - {{ $module->title }}</strong>
                            <span class="muted">{{ $module->duration_minutes }} menit / {{ $module->lessons->count() }} lesson</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Registrasi Peserta</span>
                        <h2>Buat Akun Kelas</h2>
                    </div>
                </div>

                @if($errors->any())
                    <div class="list-row" style="border-color:var(--danger);background:var(--accent-soft);margin-bottom:14px">
                        Data belum lengkap. Periksa field yang ditandai.
                    </div>
                @endif

                <form class="card" method="POST" action="{{ route('purchase.store', $course) }}">
                    @csrf
                    <div class="form-grid" style="grid-template-columns:1fr">
                        <label>
                            <span>Nama Lengkap</span>
                            <input name="name" value="{{ old('name') }}" required>
                            @error('name') <small>{{ $message }}</small> @enderror
                        </label>
                        <label>
                            <span>Email Aktif</span>
                            <input type="email" name="email" value="{{ old('email') }}" required>
                            <small>Password dan tautan akses akan dikirim ke alamat ini setelah pembayaran disetujui.</small>
                            @error('email') <small>{{ $message }}</small> @enderror
                        </label>
                        <label>
                            <span>WhatsApp</span>
                            <input name="phone" value="{{ old('phone') }}" required>
                            @error('phone') <small>{{ $message }}</small> @enderror
                        </label>
                        <label>
                            <span>Perusahaan / Instansi</span>
                            <input name="company" value="{{ old('company') }}">
                            @error('company') <small>{{ $message }}</small> @enderror
                        </label>
                    </div>
                    <div class="meta" style="margin-top:18px">
                        <button class="button" type="submit">Buat Akun & Lanjut Pembayaran</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
@endsection
