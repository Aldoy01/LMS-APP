@extends('layouts.lms', ['title' => $course->exists ? 'Edit Course' : 'Tambah Course'])

@section('content')
    <main class="main">
        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Admin LMS</span>
                    <h2>{{ $course->exists ? 'Edit Course' : 'Tambah Course' }}</h2>
                </div>
                <a class="button" style="background:var(--night)" href="{{ route('admin.courses.index') }}">Kembali</a>
            </div>

            @if($errors->any())
                <div class="list-row" style="border-color:var(--danger);background:var(--accent-soft);margin-bottom:14px">
                    Data belum lengkap. Periksa field yang ditandai.
                </div>
            @endif

            <form class="card" method="POST" action="{{ $action }}">
                @csrf
                @if($method === 'PUT')
                    @method('PUT')
                @endif

                <div class="form-grid">
                    <label>
                        <span>Judul Course</span>
                        <input name="title" value="{{ old('title', $course->title) }}" required>
                        @error('title') <small>{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Harga Jual</span>
                        <input type="number" name="price" min="0" value="{{ old('price', $course->price ?? 0) }}" required>
                        <small>Isi 0 jika course gratis.</small>
                        @error('price') <small>{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Harga Coret</span>
                        <input type="number" name="original_price" min="0" value="{{ old('original_price', $course->original_price) }}" placeholder="Contoh: 650000">
                        <small>Opsional. Harus sama atau lebih besar dari harga jual.</small>
                        @error('original_price') <small>{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Kategori Kelas</span>
                        <select name="category" required>
                            @foreach(['Cyber Security', 'Programming', 'AI & Automation'] as $category)
                                <option value="{{ $category }}" @selected(old('category', $course->category) === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                        @error('category') <small>{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Level</span>
                        <select name="level" required>
                            @foreach(['Beginner', 'Intermediate', 'Professional', 'Enterprise'] as $level)
                                <option value="{{ $level }}" @selected(old('level', $course->level) === $level)>{{ $level }}</option>
                            @endforeach
                        </select>
                        @error('level') <small>{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Status Course</span>
                        <select name="status" required>
                            @php $selectedStatus = old('status', $course->status); @endphp
                            <option value="published" @selected($selectedStatus === 'published')>Aktif</option>
                            <option value="draft" @selected(in_array($selectedStatus, ['draft', 'archived'], true))>Nonaktif</option>
                        </select>
                        <small>Course aktif tampil pada katalog dan dapat dipilih peserta. Course nonaktif hanya dapat dikelola admin.</small>
                        @error('status') <small>{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Mentor</span>
                        <select name="mentor_id">
                            <option value="">Belum dipilih</option>
                            @foreach($mentors as $mentor)
                                <option value="{{ $mentor->id }}" @selected((int) old('mentor_id', $course->mentor_id) === $mentor->id)>{{ $mentor->name }}</option>
                            @endforeach
                        </select>
                        @error('mentor_id') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="wide">
                        <span>Ringkasan</span>
                        <textarea name="summary" rows="4" required>{{ old('summary', $course->summary) }}</textarea>
                        @error('summary') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="wide">
                        <span>Deskripsi</span>
                        <textarea name="description" rows="7">{{ old('description', $course->description) }}</textarea>
                        @error('description') <small>{{ $message }}</small> @enderror
                    </label>
                </div>

                <div class="meta" style="margin-top:18px">
                    <button class="button" type="submit">Simpan Course</button>
                </div>
            </form>
        </section>
    </main>
@endsection
