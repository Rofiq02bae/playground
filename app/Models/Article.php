<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'article_category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'blocks',
        'thumbnail',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'blocks' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Article $article) {
            if (empty($article->slug)) {
                $article->slug = static::uniqueSlug($article->title);
            }
        });
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'artikel';
        $slug = $base;
        $suffix = 2;

        while (static::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function (Builder $q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function getReadingTimeAttribute(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->content)) / 180));
    }

    /**
     * URL thumbnail siap pakai untuk <img>, atau null bila tidak ada.
     * Dipakai blog/show.blade.php dan komponen article-card.
     */
    protected function thumbnailUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (empty($this->thumbnail)) {
                return null;
            }

            if (Str::startsWith($this->thumbnail, ['http://', 'https://', '/'])) {
                return $this->thumbnail;
            }

            return asset('storage/'.$this->thumbnail);
        });
    }

    /**
     * Deskripsi pendek (maks 160 char) untuk meta description / Open Graph.
     * Dipakai blog/show.blade.php.
     */
    protected function metaDescriptionValue(): Attribute
    {
        return Attribute::get(function (): string {
            $source = trim(strip_tags($this->excerpt ?: $this->content));

            return Str::limit($source, 160, '');
        });
    }

    /**
     * Isi artikel sebagai HTML yang aman dirender dengan {!! !!}.
     * - Konten teks polos (dari seeder / input admin sederhana) diubah
     *   menjadi paragraf <p> yang sudah di-escape.
     * - Konten yang sudah berisi HTML disaring: hanya tag dasar yang
     *   diizinkan, atribut event (onclick dll) dan skema javascript:
     *   dibuang.
     * Dipakai blog/show.blade.php.
     */
    protected function safeContent(): Attribute
    {
        return Attribute::get(function (): string {
            $content = trim((string) $this->content);

            if ($content === '') {
                return '';
            }

            $looksLikeHtml = (bool) preg_match('/<(p|h2|h3|h4|ul|ol|li|strong|em|blockquote|a|br)[\s>\/]/i', $content);

            if (! $looksLikeHtml) {
                $paragraphs = preg_split('/\R{2,}/', $content) ?: [];

                return collect($paragraphs)
                    ->map(fn (string $p) => '<p>'.nl2br(e(trim($p))).'</p>')
                    ->implode("\n");
            }

            $allowed = '<p><br><strong><b><em><i><u><ul><ol><li><h2><h3><h4><blockquote><a>';
            $clean = strip_tags($content, $allowed);
            $clean = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean) ?? $clean;
            $clean = preg_replace('/href\s*=\s*("|\')\s*javascript:[^"\']*\1/i', 'href="#"', $clean) ?? $clean;

            return $clean;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
