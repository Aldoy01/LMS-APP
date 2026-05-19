@extends('layouts.lms', ['title' => $course->exists ? 'Edit Course' : 'Tambah Course'])

@section('content')
    <main class="main">
        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Admin LMS</span>
                    <h2>{{ $course->exists ? 'Edit Course' : 'Tambah Course' }}</h2>
                </div>
                <a class="button" style="background:#172033" href="{{ route('admin.courses.index') }}">Kembali</a>
            </div>

            @if($errors->any())
                <div class="list-row" style="border-color:#b42318;background:#fff4f2;margin-bottom:14px">
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
                        <span>Harga</span>
                        <input type="number" name="price" min="0" value="{{ old('price', $course->price ?? 0) }}" required>
                        @error('price') <small>{{ $message }}</small> @enderror
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
                        <span>Status</span>
                        <select name="status" required>
                            @foreach(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $course->status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
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
