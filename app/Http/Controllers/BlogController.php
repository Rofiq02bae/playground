<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Halaman utama blog.
     * - Tanpa parameter: mode landing (artikel terbaru + testimonial).
     * - ?show=all: mode daftar semua artikel dengan pagination.
     */
    public function index(Request $request)
    {
        $categories = $this->categories();

        if ($request->query('show') === 'all') {
            $articles = Article::published()
                ->with('category')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->paginate(9)
                ->withQueryString();

            return view('blog.index', [
                'isLanding' => false,
                'isSearchPage' => false,
                'articles' => $articles,
                'categories' => $categories,
                'search' => '',
                'activeCategory' => null,
            ]);
        }

        $articles = Article::published()
            ->with('category')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        return view('blog.index', [
            'isLanding' => true,
            'isSearchPage' => false,
            'articles' => $articles,
            'categories' => $categories,
            'search' => '',
            'activeCategory' => null,
            // Tabel testimonial belum ada; view menampilkan pratinjau demo
            // secara eksplisit bila koleksi ini kosong.
            'testimonials' => collect(),
        ]);
    }

    public function search(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        if ($search === '') {
            return redirect()->route('blog.index');
        }

        $articles = Article::published()
            ->with('category')
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        return view('blog.index', [
            'isLanding' => false,
            'isSearchPage' => true,
            'articles' => $articles,
            'categories' => $this->categories(),
            'search' => $search,
            'activeCategory' => null,
        ]);
    }

    public function category(string $slug)
    {
        $category = ArticleCategory::where('slug', $slug)->firstOrFail();

        $articles = Article::published()
            ->with('category')
            ->where('article_category_id', $category->id)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9);

        return view('blog.category', compact('category', 'articles'));
    }

    public function show(string $slug)
    {
        $article = Article::published()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        // Sidebar kanan (1/3): artikel terbaru selain yang sedang dibaca.
        $latestArticles = Article::published()
            ->with('category')
            ->where('id', '!=', $article->id)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('blog.show', [
            'article' => $article,
            'latestArticles' => $latestArticles,
            'categories' => $this->categories(),
        ]);
    }

    private function categories()
    {
        return ArticleCategory::withCount('publishedArticles')
            ->orderBy('name')
            ->get();
    }
}
