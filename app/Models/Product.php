<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'category', 'type', 'price', 'stock', 'unit', 'photo_path', 'description', 'status', 'harvest_project_id'])]
class Product extends Model
{
    public const CATEGORY_PERTANIAN = 'pertanian';

    public const CATEGORY_PETERNAKAN = 'peternakan';

    public const CATEGORIES = [
        self::CATEGORY_PERTANIAN => 'Pertanian',
        self::CATEGORY_PETERNAKAN => 'Peternakan',
    ];

    public const TYPE_BARANG = 'barang';

    public const TYPE_JASA = 'jasa';

    public const TYPES = [
        self::TYPE_BARANG => 'Barang',
        self::TYPE_JASA => 'Jasa',
    ];

    public const STATUSES = [
        'draft' => 'Draft',
        'aktif' => 'Aktif',
        'arsip' => 'Arsip',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
        ];
    }

    public function harvestProject(): BelongsTo
    {
        return $this->belongsTo(AgriculturalProject::class, 'harvest_project_id');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    public function isJasa(): bool
    {
        return $this->type === self::TYPE_JASA;
    }

    public function isBarang(): bool
    {
        return $this->type === self::TYPE_BARANG;
    }

    /** Barang berstok 0 = habis; jasa tidak pernah habis (stok tidak dibatasi). */
    public function isOutOfStock(): bool
    {
        return $this->isBarang() && $this->stock !== null && $this->stock <= 0;
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst((string) $this->category);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst((string) $this->type);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }

    /** URL foto produk siap pakai untuk <img>, atau null bila belum ada foto. */
    protected function photoUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            if (empty($this->photo_path)) {
                return null;
            }

            if (Str::startsWith($this->photo_path, ['http://', 'https://', '/'])) {
                return $this->photo_path;
            }

            return asset('storage/'.$this->photo_path);
        });
    }

    /** Slug unik dari nama produk (ditambah sufiks angka bila sudah dipakai). */
    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'produk';
        $slug = $base;
        $i = 2;

        while (static::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
