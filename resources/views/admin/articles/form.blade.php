@extends('admin.layouts.app')

@section('title', ($article->exists ? 'Edit Artikel' : 'Artikel Baru') . ' - Admin FunFarm')

@section('content')
    <p><a href="{{ route('admin.articles.index') }}" class="text-sm font-medium text-emerald-700 hover:underline">&larr; Semua artikel</a></p>
    <h1 class="mt-2 text-2xl font-bold text-stone-900">{{ $article->exists ? 'Edit Artikel' : 'Artikel Baru' }}</h1>

    @if ($errors->any())
        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
            <p class="font-semibold">Ada yang perlu diperbaiki:</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" enctype="multipart/form-data"
          action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}"
          id="article-form" class="mt-6">
        @csrf
        @if ($article->exists) @method('PUT') @endif

        <div class="grid items-start gap-6 lg:grid-cols-3">
            {{-- Kolom utama: judul, ringkasan, block editor --}}
            <div class="flex min-w-0 flex-col gap-5 lg:col-span-2">
                <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <label for="title" class="mb-1.5 block text-sm font-medium text-stone-700">Judul artikel</label>
                    <input id="title" name="title" value="{{ old('title', $article->title) }}" required maxlength="255"
                           class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-base font-medium outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"
                           placeholder="Tulis judul artikel...">
                    <label for="excerpt" class="mb-1.5 mt-5 block text-sm font-medium text-stone-700">Ringkasan <span class="font-normal text-stone-400">(opsional — otomatis dari isi bila kosong)</span></label>
                    <textarea id="excerpt" name="excerpt" rows="2" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100" placeholder="Ringkasan singkat untuk kartu & meta description...">{{ old('excerpt', $article->exists ? $article->excerpt : '') }}</textarea>
                </div>

                <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-sm font-semibold text-stone-900">Isi Artikel</h2>
                        <span class="text-xs text-stone-400">Tulis langsung di canvas — sorot teks untuk bold/italic/link, seret handle untuk menyusun urutan, tombol + untuk menyisipkan block</span>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2" role="group" aria-label="Tambah block">
                        <button type="button" data-add-block="heading" class="rounded-lg bg-stone-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-stone-900">+ Judul</button>
                        <button type="button" data-add-block="paragraph" class="rounded-lg bg-stone-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-stone-900">+ Paragraf</button>
                        <button type="button" data-add-block="image" class="rounded-lg bg-stone-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-stone-900">+ Foto</button>
                        <button type="button" data-add-block="list" class="rounded-lg bg-stone-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-stone-900">+ List</button>
                        <button type="button" data-add-block="quote" class="rounded-lg bg-stone-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-stone-900">+ Kutipan</button>
                        <button type="button" data-add-block="code" class="rounded-lg bg-stone-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-stone-900">+ Kode</button>
                        <button type="button" data-add-block="video" class="rounded-lg bg-stone-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-stone-900">+ Video</button>
                        <button type="button" data-add-block="separator" class="rounded-lg bg-stone-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-stone-900">+ Garis Pemisah</button>
                    </div>

                    <div id="block-list" class="mt-4 flex flex-col gap-3"></div>
                    <p id="block-empty-hint" class="mt-4 hidden rounded-xl border border-dashed border-stone-300 px-4 py-6 text-center text-sm text-stone-500">Belum ada block. Tambahkan block pertama lewat tombol di atas.</p>

                    <input type="hidden" name="blocks" id="blocks-json" value="">
                    <script type="application/json" id="blocks-initial">{!! json_encode(old('blocks') ? (json_decode(old('blocks'), true) ?: []) : ($article->blocks ?? []), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
                </div>
            </div>

            {{-- Kolom samping: pengaturan --}}
            <div class="flex min-w-0 flex-col gap-5">
                <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <label for="status" class="mb-1.5 block text-sm font-medium text-stone-700">Status</label>
                    <select id="status" name="status" class="w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-600">
                        <option value="draft" @selected(old('status', $article->status) === 'draft')>Draft</option>
                        <option value="published" @selected(old('status', $article->status) === 'published')>Published</option>
                    </select>

                    <label for="article_category_id" class="mb-1.5 mt-4 block text-sm font-medium text-stone-700">Kategori</label>
                    <select id="article_category_id" name="article_category_id" class="w-full rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-600">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) old('article_category_id', $article->article_category_id) === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <label for="slug" class="mb-1.5 mt-4 block text-sm font-medium text-stone-700">Slug <span class="font-normal text-stone-400">(opsional)</span></label>
                    <input id="slug" name="slug" value="{{ old('slug', $article->exists ? $article->slug : '') }}" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600" placeholder="otomatis-dari-judul">
                    <p class="mt-1 text-xs text-stone-400">URL artikel: /blog/{slug}</p>

                    <button type="submit" class="mt-6 w-full rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan Artikel</button>
                    @if ($article->exists && $article->status === 'published')
                        <a href="{{ route('blog.show', $article->slug) }}" target="_blank" rel="noopener" class="mt-3 block text-center text-sm font-medium text-emerald-700 hover:underline">Lihat di blog &rarr;</a>
                    @endif
                </div>

                <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
                    <span class="mb-1.5 block text-sm font-medium text-stone-700">Foto sampul (thumbnail)</span>
                    @if ($article->thumbnail_url)
                        <img src="{{ $article->thumbnail_url }}" alt="" class="mb-3 aspect-[16/9] w-full rounded-xl object-cover">
                        <label class="mb-3 flex items-center gap-2 text-sm text-stone-600">
                            <input type="checkbox" name="remove_thumbnail" value="1" class="rounded border-stone-300"> Hapus foto sampul
                        </label>
                    @endif
                    <input type="file" name="thumbnail" accept="image/*" class="w-full text-sm text-stone-600 file:mr-3 file:rounded-lg file:border-0 file:bg-stone-800 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-stone-900">
                    <p class="mt-2 text-xs text-stone-400">Ditampilkan di kartu artikel & bagian atas halaman artikel. Maks 4 MB.</p>
                </div>
            </div>
        </div>
    </form>
@endsection
