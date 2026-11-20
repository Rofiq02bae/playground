<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'name', 'owner_id', 'cultivator_id', 'field_name', 'location', 'area_size',
    'coordinates', 'boundary_geojson', 'crop_type', 'crop_variety', 'start_date',
    'estimated_harvest_date', 'status', 'profit_share_company_percent', 'created_by',
    'rental_type', 'duration_years', 'lease_end_date', 'annual_rental_price',
])]
class AgriculturalProject extends Model
{
    public const STATUSES = ['persiapan', 'berjalan', 'menunggu_panen', 'selesai', 'dibatalkan'];

    public const RENTAL_TYPES = ['bagi_hasil', 'tahunan'];

    protected function casts(): array
    {
        return [
            'area_size' => 'decimal:2',
            'boundary_geojson' => 'array',
            'start_date' => 'date',
            'estimated_harvest_date' => 'date',
            'lease_end_date' => 'date',
            'profit_share_company_percent' => 'integer',
            'duration_years' => 'integer',
            'annual_rental_price' => 'integer',
        ];
    }

    public function agreement(): HasOne
    {
        return $this->hasOne(Agreement::class, 'project_id');
    }

    public function isBagiHasil(): bool
    {
        return $this->rental_type !== 'tahunan';
    }

    public function rentalTypeLabel(): string
    {
        return $this->isBagiHasil() ? 'Sewa Bagi Hasil' : 'Sewa Tahunan';
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function cultivator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cultivator_id');
    }

    public function harvests(): HasMany
    {
        return $this->hasMany(HarvestRecord::class, 'project_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(ProjectExpense::class, 'project_id');
    }

    public function approvedExpenses(): HasMany
    {
        return $this->expenses()->where('status', ProjectExpense::STATUS_APPROVED);
    }

    /** Total biaya resmi: hanya pencatatan yang sudah di-approve perusahaan. */
    public function approvedTotal(): int
    {
        return (int) $this->approvedExpenses()->sum('total_price');
    }

    public function pendingCount(): int
    {
        return $this->expenses()->where('status', ProjectExpense::STATUS_PENDING)->count();
    }

    /** Proyek yang boleh dilihat user: admin semua, pemilik miliknya, penggarap yang ditugaskan. */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isPemilik()) {
            return $query->where('owner_id', $user->id);
        }

        return $query->where('cultivator_id', $user->id);
    }

    public function isVisibleTo(User $user): bool
    {
        return $user->isAdmin()
            || ($user->isPemilik() && $this->owner_id === $user->id)
            || ($user->isPenggarap() && $this->cultivator_id === $user->id);
    }

    /** Yang boleh menambah pencatatan: admin dan penggarap yang ditugaskan. Pemilik view only. */
    public function canAddExpense(User $user): bool
    {
        return $user->isAdmin()
            || ($user->isPenggarap() && $this->cultivator_id === $user->id);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'persiapan' => 'Persiapan',
            'berjalan' => 'Berjalan',
            'menunggu_panen' => 'Menunggu Panen',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst((string) $this->status),
        };
    }
}
