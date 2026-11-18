<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Sisi viewer: daftar artikel yang sudah diterbitkan.
     * Hanya artikel published yang diambil, jadi grid tidak pernah
     * berisi card kosong / draft.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $activeCategory = $request->query('kategori');

        $articles = Article::published()
            ->with('category')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($activeCategory, function ($query) use ($activeCategory) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $activeCategory));
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        $categories = ArticleCategory::withCount('publishedArticles')
            ->orderBy('name')
            ->get();

        return view('articles.index', compact('articles', 'categories', 'search', 'activeCategory'));
    }

    /**
     * Sisi viewer: detail artikel. Draft / belum terbit = 404.
     */
    public function show(Article $article)
    {
        abort_unless($article->status === 'published'
            && ($article->published_at === null || $article->published_at->isPast()), 404);

        $article->load('category');

        $related = Article::published()
            ->with('category')
            ->where('article_category_id', $article->article_category_id)
            ->where('id', '!=', $article->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('articles.show', compact('article', 'related'));
    }
}
