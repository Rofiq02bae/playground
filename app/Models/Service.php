<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'slug', 'branch', 'unit', 'price_normal', 'price_member', 'description', 'is_active'])]
class Service extends Model
{
    protected function casts(): array
    {
        return [
            'price_normal' => 'integer',
            'price_member' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /** Tarif yang berlaku untuk pemilik tertentu (member dapat potongan). */
    public function priceFor(?User $owner): int
    {
        return ($owner && $owner->is_member) ? $this->price_member : $this->price_normal;
    }

    public function branchLabel(): string
    {
        return $this->branch === 'peternakan' ? 'Peternakan' : 'Pertanian';
    }
}
