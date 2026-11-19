<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_id', 'service_id', 'category', 'name', 'item_type', 'quantity',
    'unit', 'unit_price', 'total_price', 'date', 'notes', 'status',
    'created_by', 'approved_by', 'approved_at', 'rejection_note',
])]
class ProjectExpense extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const CATEGORIES = [
        'sewa_drone' => 'Sewa Drone Semprot',
        'sewa_mesin_tanam' => 'Sewa Mesin Tanam',
        'pupuk' => 'Pupuk',
        'obat' => 'Obat / Pestisida',
        'tenaga_kerja' => 'Tenaga Kerja',
        'benih' => 'Benih',
        'transportasi' => 'Transportasi',
        'lainnya' => 'Lainnya',
    ];

    /** Kategori sewa yang tarifnya diambil dari master layanan. */
    public const RENTAL_CATEGORIES = [
        'sewa_drone' => 'drone-semprot',
        'sewa_mesin_tanam' => 'mesin-tanam',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'integer',
            'total_price' => 'integer',
            'date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(AgriculturalProject::class, 'project_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst((string) $this->category);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            default => 'Menunggu Persetujuan',
        };
    }

    public function isRental(): bool
    {
        return array_key_exists($this->category, self::RENTAL_CATEGORIES);
    }
}
