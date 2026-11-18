@extends('admin.layouts.app')

@section('title', 'Kelola Artikel - Admin FunFarm')

@section('content')
    <div class="flex flex-wrap items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-stone-900">Artikel</h1>
            <p class="text-sm text-stone-500">{{ $articles->total() }} artikel (termasuk draft)</p>
        </div>
        <form method="GET" class="ml-auto flex gap-2">
            <input type="search" name="q" value="{{ $search }}" placeholder="Cari judul..." class="rounded-xl border border-stone-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-emerald-600">
            <button class="rounded-xl border border-stone-300 bg-white px-4 py-2 text-sm font-medium hover:bg-stone-50">Cari</button>
        </form>
        <a href="{{ route('admin.articles.create') }}" class="rounded-xl bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">+ Artikel Baru</a>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
                    <th class="px-5 py-3 font-semibold">Judul</th>
                    <th class="hidden px-5 py-3 font-semibold md:table-cell">Kategori</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="hidden px-5 py-3 font-semibold lg:table-cell">Terakhir diubah</th>
                    <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($articles as $article)
                    <tr>
                        <td class="px-5 py-3.5">
                            <span class="font-medium text-stone-900">{{ $article->title }}</span>
                            <span class="block text-xs text-stone-400">/blog/{{ $article->slug }}</span>
                        </td>
                        <td class="hidden px-5 py-3.5 text-stone-600 md:table-cell">{{ $article->category?->name ?? '-' }}</td>
                        <td class="px-5 py-3.5">
                            @if ($article->status === 'published')
                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">Published</span>
                            @else
                                <span class="rounded-full bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-600">Draft</span>
                            @endif
                        </td>
                        <td class="hidden px-5 py-3.5 text-stone-500 lg:table-cell">{{ $article->updated_at->translatedFormat('d M Y, H:i') }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-2">
                                @if ($article->status === 'published')
                                    <a href="{{ route('blog.show', $article->slug) }}" target="_blank" rel="noopener" class="rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-medium hover:bg-stone-50">Lihat</a>
                                @endif
                                <a href="{{ route('admin.articles.edit', $article) }}" class="rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-medium hover:bg-stone-50">Edit</a>
                                <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" onsubmit="return confirm('Hapus artikel ini? Tindakan ini tidak bisa dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-stone-500">Belum ada artikel.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $articles->links() }}</div>
@endsection
