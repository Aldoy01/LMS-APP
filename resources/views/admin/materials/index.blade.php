@extends('layouts.lms', ['title' => 'Kelola Materi'])

@section('content')
    <style>
        .admin-toggle {
            margin-top: 12px;
            overflow: hidden;
            border: 1px solid rgba(47,123,255,.14);
            border-radius: 12px;
            background: #ffffff;
        }
        .admin-toggle > summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 13px 15px;
            color: #3157dc;
            background: #f7faff;
            cursor: pointer;
            list-style: none;
            font-size: 13px;
            font-weight: 900;
        }
        .admin-toggle > summary::-webkit-details-marker { display: none; }
        .admin-toggle > summary::after { content: "+"; font-size: 20px; line-height: 1; }
        .admin-toggle[open] > summary::after { content: "−"; }
        .admin-toggle-body { padding: 15px; border-top: 1px solid rgba(47,123,255,.1); }
        .material-summary {
            display: grid;
            grid-template-columns: minmax(0,1fr) auto;
            gap: 15px;
            align-items: center;
        }
        .material-summary h4 { margin: 0; color: #07164d; font-size: 15px; }
        .material-summary p { margin: 5px 0 0; color: #64748b; font-size: 12px; }
        .material-actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .material-actions .button { min-height: 36px; padding: 8px 12px; font-size: 11px; }
        @media (max-width: 680px) {
            .material-summary { grid-template-columns: 1fr; }
        }
    </style>
    <main class="main">
        <section class="section">
            <div class="section-head">
                <div>
                    <span class="eyebrow">Upload Materi LMS</span>
                    <h2>{{ $course->title }}</h2>
                </div>
                <a class="button" style="background:var(--night)" href="{{ route('admin.courses.index') }}">Kembali</a>
            </div>

            @if(session('status'))
                <div class="list-row" style="border-color:var(--teal);background:var(--teal-soft);margin-bottom:14px">
                    {{ session('status') }}
                </div>
            @endif
            @if($errors->any())
                <div class="list-row" style="border-color:var(--danger);background:var(--accent-soft);margin-bottom:14px">
                    Data belum lengkap. Maksimal upload file 50 MB.
                </div>
            @endif

            <form method="POST" action="{{ route('admin.modules.store', $course) }}" class="card" style="margin-bottom:18px">
                @csrf
                <span class="eyebrow">Buat Modul Baru</span>
                <h3>Tambah Struktur Modul</h3>
                <div class="form-grid">
                    <label>
                        <span>Judul Modul</span>
                        <input name="title" placeholder="Contoh: Intro Cyber Security" required>
                    </label>
                    <label>
                        <span>Kategori</span>
                        <select name="category" required>
                            <option value="Basic">Basic</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Practical">Practical</option>
                        </select>
                    </label>
                    <label>
                        <span>Durasi Estimasi (menit)</span>
                        <input type="number" name="duration_minutes" value="60" min="0" required>
                    </label>
                    <label>
                        <span>Urutan Modul</span>
                        <input type="number" name="sort_order" value="{{ $course->modules->count() + 1 }}" min="0" required>
                    </label>
                    <label class="wide">
                        <span>Ringkasan Modul</span>
                        <textarea name="summary" rows="3" placeholder="Jelaskan tujuan modul secara singkat"></textarea>
                    </label>
                </div>
                <div class="meta" style="margin-top:18px">
                    <button class="button" type="submit">Tambah Modul</button>
                </div>
            </form>

            @if($course->modules->isEmpty())
                <div class="card">
                    <h3>Course ini belum punya modul</h3>
                    <p class="muted">Buat modul pertama dulu, lalu tambahkan lesson. Setelah lesson tersedia, form upload PDF, video, tools list, dan resource link akan muncul.</p>
                </div>
            @endif

            <div class="list">
                @foreach($course->modules as $module)
                    <article class="card">
                        <div class="section-head">
                            <div>
                                <span class="eyebrow">{{ $module->category }} / Modul {{ $module->sort_order }}</span>
                                <h3>{{ $module->title }}</h3>
                                <p>{{ $module->summary }}</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.modules.update', $module) }}" class="form-grid">
                            @csrf
                            @method('PUT')
                            <label>
                                <span>Judul Modul</span>
                                <input name="title" value="{{ $module->title }}" required>
                            </label>
                            <label>
                                <span>Kategori</span>
                                <select name="category" required>
                                    @foreach(['Basic', 'Intermediate', 'Practical'] as $category)
                                        <option value="{{ $category }}" @selected($module->category === $category)>{{ $category }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>
                                <span>Durasi Modul (menit)</span>
                                <input type="number" name="duration_minutes" min="0" value="{{ $module->duration_minutes }}" required>
                            </label>
                            <label>
                                <span>Urutan Modul</span>
                                <input type="number" name="sort_order" min="0" value="{{ $module->sort_order }}" required>
                            </label>
                            <label class="wide">
                                <span>Ringkasan Modul</span>
                                <textarea name="summary" rows="3">{{ $module->summary }}</textarea>
                            </label>
                            <div class="meta wide" style="align-items:end">
                                <button class="button" type="submit">Simpan Perubahan Modul</button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('admin.modules.destroy', $module) }}" style="margin-top:12px" onsubmit="return confirm('Hapus modul beserta semua lesson dan materinya? Tindakan ini tidak dapat dibatalkan.')">
                            @csrf
                            @method('DELETE')
                            <button class="button" style="background:var(--danger)" type="submit">Hapus Modul</button>
                        </form>

                        <form method="POST" action="{{ route('admin.lessons.store', $module) }}" class="card" style="margin-top:18px">
                            @csrf
                            <span class="eyebrow">Tambah Lesson</span>
                            <div class="form-grid">
                                <label>
                                    <span>Judul Lesson</span>
                                    <input name="title" placeholder="Contoh: Fondasi Cyber Security" required>
                                </label>
                                <label>
                                    <span>Tipe Konten</span>
                                    <select name="content_type" required>
                                        <option value="video">Video</option>
                                        <option value="pdf">PDF</option>
                                        <option value="ebook">Ebook</option>
                                        <option value="checklist">Checklist</option>
                                        <option value="worksheet">Worksheet</option>
                                        <option value="lab">Lab</option>
                                    </select>
                                </label>
                                <label>
                                    <span>Durasi Lesson (menit)</span>
                                    <input type="number" name="duration_minutes" value="30" min="0" required>
                                </label>
                                <label>
                                    <span>Urutan Lesson</span>
                                    <input type="number" name="sort_order" value="{{ $module->lessons->count() + 1 }}" min="0" required>
                                </label>
                                <label class="wide">
                                    <span>Ringkasan Lesson</span>
                                    <textarea name="summary" rows="3" placeholder="Jelaskan isi lesson secara singkat"></textarea>
                                </label>
                                <label style="display:flex;align-items:center;gap:8px">
                                    <input type="checkbox" name="is_preview" value="1" style="width:auto">
                                    <span>Preview gratis</span>
                                </label>
                            </div>
                            <div class="meta" style="margin-top:18px">
                                <button class="button" type="submit">Tambah Lesson</button>
                            </div>
                        </form>

                        <div class="list" style="margin-top:18px">
                            @foreach($module->lessons as $lesson)
                                <div class="list-row">
                                    <strong>{{ $lesson->title }}</strong>
                                    <span class="muted">{{ ucfirst($lesson->content_type) }} / {{ $lesson->duration_minutes }} menit</span>

                                    <details class="admin-toggle">
                                        <summary>Tambah Materi Baru</summary>
                                        <div class="admin-toggle-body">
                                            <form method="POST" action="{{ route('admin.materials.store', $lesson) }}" enctype="multipart/form-data" class="form-grid">
                                                @csrf
                                                <label>
                                                    <span>Judul Materi</span>
                                                    <input name="title" required>
                                                </label>
                                                <label>
                                                    <span>Tipe</span>
                                                    <select name="type" required>
                                                        <option value="pdf">PDF Materi</option>
                                                        <option value="pdf-slide">PDF Slide</option>
                                                        <option value="video-upload">Upload Video</option>
                                                        <option value="video-embed">Embed Video</option>
                                                        <option value="tool">Tools List</option>
                                                        <option value="resource">Resource Link</option>
                                                    </select>
                                                </label>
                                                <label>
                                                    <span>Urutan</span>
                                                    <input type="number" name="sort_order" value="{{ $lesson->materials->count() + 1 }}" min="0" required>
                                                </label>
                                                <label>
                                                    <span>Upload File (maksimal 50 MB)</span>
                                                    <input type="file" name="file">
                                                    <small>Server menerima request hingga 64 MB untuk metadata upload.</small>
                                                </label>
                                                <label class="wide">
                                                    <span>URL Video / Resource</span>
                                                    <input name="external_url" placeholder="Tempel URL YouTube, PDF, atau resource">
                                                    <small>URL YouTube dan PDF ditampilkan langsung pada lesson.</small>
                                                </label>
                                                <label style="display:flex;align-items:center;gap:8px">
                                                    <input type="checkbox" name="downloadable" value="1" style="width:auto">
                                                    <span>Bisa diunduh</span>
                                                </label>
                                                <div class="meta">
                                                    <button class="button" type="submit">Simpan Materi Baru</button>
                                                </div>
                                            </form>
                                        </div>
                                    </details>

                                    <div class="list" style="margin-top:12px">
                                        @foreach($lesson->materials as $material)
                                            <div class="list-row">
                                                <div class="material-summary">
                                                    <div>
                                                        <h4>{{ $material->title }}</h4>
                                                        <p>{{ strtoupper(str_replace('-', ' ', $material->type)) }} · Urutan {{ $material->sort_order }}{{ $material->downloadable ? ' · Bisa diunduh' : '' }}</p>
                                                    </div>
                                                    <div class="material-actions">
                                                        <a class="button" style="background:var(--night)" href="{{ route('materials.show', $material) }}" target="_blank" rel="noopener">Preview</a>
                                                        <form method="POST" action="{{ route('admin.materials.destroy', $material) }}" onsubmit="return confirm('Hapus materi ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="button" style="background:var(--danger)" type="submit">Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>

                                                <details class="admin-toggle">
                                                    <summary>Edit Materi</summary>
                                                    <div class="admin-toggle-body">
                                                        <form method="POST" action="{{ route('admin.materials.update', $material) }}" enctype="multipart/form-data" class="form-grid">
                                                            @csrf
                                                            @method('PUT')
                                                            <label>
                                                                <span>Judul</span>
                                                                <input name="title" value="{{ $material->title }}" required>
                                                            </label>
                                                            <label>
                                                                <span>Tipe</span>
                                                                <select name="type" required>
                                                                    @foreach(['pdf' => 'PDF Materi', 'pdf-slide' => 'PDF Slide', 'video-upload' => 'Upload Video', 'video-embed' => 'Embed Video', 'tool' => 'Tools List', 'resource' => 'Resource Link'] as $value => $label)
                                                                        <option value="{{ $value }}" @selected($material->type === $value)>{{ $label }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </label>
                                                            <label>
                                                                <span>Urutan</span>
                                                                <input type="number" name="sort_order" value="{{ $material->sort_order }}" min="0" required>
                                                            </label>
                                                            <label>
                                                                <span>Ganti File</span>
                                                                <input type="file" name="file">
                                                            </label>
                                                            <label class="wide">
                                                                <span>Ganti URL Video / Resource</span>
                                                                <input name="external_url" value="{{ filter_var($material->url, FILTER_VALIDATE_URL) ? $material->url : '' }}">
                                                                <small>Kosongkan jika file atau URL lama tidak ingin diubah.</small>
                                                            </label>
                                                            <label style="display:flex;align-items:center;gap:8px">
                                                                <input type="checkbox" name="downloadable" value="1" style="width:auto" @checked($material->downloadable)>
                                                                <span>Bisa diunduh</span>
                                                            </label>
                                                            <div class="meta">
                                                                <button class="button" type="submit">Simpan Perubahan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </details>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </main>
@endsection
