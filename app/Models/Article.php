<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'thumbnail',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
