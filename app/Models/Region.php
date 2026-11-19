<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['kode', 'nama', 'level', 'parent_kode'])]
class Region extends Model
{
    protected $primaryKey = 'kode';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    public const LEVEL_PROVINCE = 1;

    public const LEVEL_REGENCY = 2;

    public const LEVEL_DISTRICT = 3;

    public const LEVEL_VILLAGE = 4;

    /** Anak langsung dari kode induk; tanpa induk = daftar provinsi. */
    public function scopeChildrenOf(Builder $query, ?string $parentKode): Builder
    {
        return $parentKode
            ? $query->where('parent_kode', $parentKode)
            : $query->where('level', self::LEVEL_PROVINCE);
    }
}
