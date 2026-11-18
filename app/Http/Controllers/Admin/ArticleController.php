<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $articles = Article::with('category')
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.articles.index', compact('articles', 'search'));
    }

    public function create()
    {
        return view('admin.articles.form', [
            'article' => new Article(['status' => 'draft']),
            'categories' => ArticleCategory::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $article = new Article;
        $this->save($request, $article);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel "'.$article->title.'" berhasil dibuat.');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.form', [
            'article' => $article,
            'categories' => ArticleCategory::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Article $article)
    {
        $this->save($request, $article);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel "'.$article->title.'" berhasil diperbarui.');
    }

    public function destroy(Article $article)
    {
        $this->deleteStoredFile($article->thumbnail);

        foreach ($article->blocks ?? [] as $block) {
            if (($block['type'] ?? null) === 'image') {
                $this->deleteStoredFile($block['src'] ?? null);
            }
        }

        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Artikel "'.$article->title.'" berhasil dihapus.');
    }

    private function save(Request $request, Article $article): void
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'article_category_id' => ['required', 'exists:article_categories,id'],
            'status' => ['required', 'in:draft,published'],
            'excerpt' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'remove_thumbnail' => ['nullable'],
            'blocks' => ['required', 'string'],
        ]);

        $blocks = $this->sanitizeBlocks(
            json_decode($validated['blocks'], true) ?: [],
            $request->file('block_images', [])
        );

        if ($blocks === []) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'blocks' => 'Artikel harus punya minimal satu block yang terisi.',
            ]);
        }

        // Teks polos dari block: dipakai kolom content untuk pencarian,
        // meta description, dan estimasi waktu baca di viewer.
        $plainText = collect($blocks)->map(function (array $block) {
            return match ($block['type']) {
                'heading', 'paragraph', 'quote' => $block['text'],
                'list' => implode("\n", $block['items']),
                default => '',
            };
        })->filter()->implode("\n\n");

        $article->title = $validated['title'];
        $article->article_category_id = $validated['article_category_id'];
        $article->status = $validated['status'];
        $article->blocks = $blocks;
        $article->content = $plainText;
        $article->excerpt = trim((string) ($validated['excerpt'] ?? '')) !== ''
            ? $validated['excerpt']
            : Str::limit($plainText, 200, '');

        $slugInput = trim((string) ($validated['slug'] ?? ''));
        $article->slug = Article::uniqueSlug($slugInput !== '' ? $slugInput : $validated['title'], $article->id);

        if ($article->status === 'published' && empty($article->published_at)) {
            $article->published_at = now();
        }

        if ($request->hasFile('thumbnail')) {
            $this->deleteStoredFile($article->thumbnail);
            $article->thumbnail = $request->file('thumbnail')->store('thumbnails', 'public');
        } elseif ($request->boolean('remove_thumbnail')) {
            $this->deleteStoredFile($article->thumbnail);
            $article->thumbnail = null;
        }

        $article->save();
    }

    /**
     * Saring block dari editor menjadi struktur yang aman & rapi.
     * $uploads: file gambar per index block (dari input block_images[i]).
     */
    private function sanitizeBlocks(array $rawBlocks, array $uploads): array
    {
        $clean = [];

        foreach ($rawBlocks as $i => $block) {
            $type = $block['type'] ?? '';

            switch ($type) {
                case 'heading':
                    $text = trim((string) ($block['text'] ?? ''));
                    if ($text === '') {
                        break;
                    }
                    $clean[] = [
                        'type' => 'heading',
                        'level' => in_array((int) ($block['level'] ?? 2), [2, 3], true) ? (int) $block['level'] : 2,
                        'text' => $text,
                    ];
                    break;

                case 'paragraph':
                    $text = trim((string) ($block['text'] ?? ''));
                    if ($text === '') {
                        break;
                    }
                    $clean[] = ['type' => 'paragraph', 'text' => $text];
                    break;

                case 'image':
                    $src = trim((string) ($block['src'] ?? ''));
                    if (isset($uploads[$i])) {
                        $src = $uploads[$i]->store('articles', 'public');
                    }
                    if ($src === '') {
                        break;
                    }
                    $clean[] = [
                        'type' => 'image',
                        'src' => $src,
                        'caption' => trim((string) ($block['caption'] ?? '')),
                    ];
                    break;

                case 'list':
                    $items = collect($block['items'] ?? [])
                        ->map(fn ($item) => trim((string) $item))
                        ->filter()
                        ->values()
                        ->all();
                    if ($items === []) {
                        break;
                    }
                    $clean[] = [
                        'type' => 'list',
                        'ordered' => (bool) ($block['ordered'] ?? false),
                        'items' => $items,
                    ];
                    break;

                case 'quote':
                    $text = trim((string) ($block['text'] ?? ''));
                    if ($text === '') {
                        break;
                    }
                    $clean[] = [
                        'type' => 'quote',
                        'text' => $text,
                        'source' => trim((string) ($block['source'] ?? '')),
                    ];
                    break;
            }
        }

        return $clean;
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path && ! Str::startsWith($path, ['http://', 'https://', '/'])) {
            Storage::disk('public')->delete($path);
        }
    }
}
