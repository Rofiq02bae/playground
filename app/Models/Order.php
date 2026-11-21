<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'user_id', 'status', 'subtotal', 'shipping_cost', 'total', 'recipient_name', 'recipient_phone', 'province_code', 'regency_code', 'district_code', 'village_code', 'hamlet', 'rt', 'rw', 'address_text', 'postal_code', 'courier_company', 'courier_service', 'waybill', 'biteship_order_id', 'payment_status', 'payment_type', 'snap_token', 'paid_at', 'expires_at', 'notes'])]
class Order extends Model
{
    public const STATUS_MENUNGGU = 'menunggu_pembayaran';

    public const STATUS_DIBAYAR = 'dibayar';

    public const STATUS_DIPROSES = 'diproses';

    public const STATUS_DIKIRIM = 'dikirim';

    public const STATUS_SELESAI = 'selesai';

    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const STATUSES = [
        self::STATUS_MENUNGGU => 'Menunggu Pembayaran',
        self::STATUS_DIBAYAR => 'Dibayar',
        self::STATUS_DIPROSES => 'Diproses',
        self::STATUS_DIKIRIM => 'Dikirim',
        self::STATUS_SELESAI => 'Selesai',
        self::STATUS_DIBATALKAN => 'Dibatalkan',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'shipping_cost' => 'integer',
            'total' => 'integer',
            'paid_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }

    public function hasBarang(): bool
    {
        return $this->items->contains(fn (OrderItem $item) => $item->product_type === Product::TYPE_BARANG);
    }

    public function hasJasa(): bool
    {
        return $this->items->contains(fn (OrderItem $item) => $item->product_type === Product::TYPE_JASA);
    }

    /** Kode pesanan unik: FF-YYYYMMDD-XXXX (dipakai juga sebagai order_id Midtrans). */
    public static function generateCode(int $id): string
    {
        return 'FF-'.now()->format('Ymd').'-'.str_pad((string) $id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Batalkan pesanan dan kembalikan stok barang ke katalog.
     * Hanya dari status yang belum final; pemanggil wajib memeriksa status.
     */
    public function cancelAndRestoreStock(): void
    {
        foreach ($this->items as $item) {
            if ($item->product_type === Product::TYPE_BARANG && $item->product_id) {
                Product::query()->where('id', $item->product_id)->increment('stock', $item->quantity);
            }
        }

        $this->update(['status' => self::STATUS_DIBATALKAN]);
    }
}
