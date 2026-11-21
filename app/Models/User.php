<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'username', 'phone', 'nik', 'occupation', 'address', 'is_member', 'province_code', 'regency_code', 'district_code', 'village_code', 'hamlet', 'rt', 'rw'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // RBAC Fase 2 — admin = perusahaan (semua akses, termasuk blog & webapp).
    public const ROLE_ADMIN = 'admin';

    public const ROLE_PEMILIK = 'pemilik';

    public const ROLE_PENGGARAP = 'penggarap';

    // Fase 3 — pembeli marketplace (daftar mandiri dari halaman toko).
    public const ROLE_PEMBELI = 'pembeli';

    public const ROLES = [self::ROLE_ADMIN, self::ROLE_PEMILIK, self::ROLE_PENGGARAP, self::ROLE_PEMBELI];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_member' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isPemilik(): bool
    {
        return $this->role === self::ROLE_PEMILIK;
    }

    public function isPenggarap(): bool
    {
        return $this->role === self::ROLE_PENGGARAP;
    }

    public function isPembeli(): bool
    {
        return $this->role === self::ROLE_PEMBELI;
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Admin (Perusahaan)',
            self::ROLE_PEMILIK => 'Pemilik Sawah',
            self::ROLE_PENGGARAP => 'Penggarap Sawah',
            self::ROLE_PEMBELI => 'Pembeli',
            default => ucfirst((string) $this->role),
        };
    }

    public function membershipLabel(): string
    {
        return $this->is_member ? 'Membership' : 'Bukan Membership';
    }

    public function province(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Region::class, 'province_code', 'kode');
    }

    public function regency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Region::class, 'regency_code', 'kode');
    }

    public function district(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Region::class, 'district_code', 'kode');
    }

    public function village(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Region::class, 'village_code', 'kode');
    }

    /**
     * Susun alamat lengkap dari bagian-bagiannya:
     * "Dusun Krajan, RT 001/RW 002, Balecatur, Kec. Gamping, Kabupaten Sleman, DI Yogyakarta".
     */
    public static function composeAddress(array $parts, array $regionNames): ?string
    {
        $segments = [];

        if (! empty($parts['hamlet'])) {
            $segments[] = 'Dusun '.$parts['hamlet'];
        }

        if (! empty($parts['rt']) || ! empty($parts['rw'])) {
            $segments[] = 'RT '.($parts['rt'] ?: '-').'/RW '.($parts['rw'] ?: '-');
        }

        if (! empty($regionNames['village'])) {
            $segments[] = $regionNames['village'];
        }

        if (! empty($regionNames['district'])) {
            $segments[] = 'Kec. '.$regionNames['district'];
        }

        if (! empty($regionNames['regency'])) {
            $segments[] = $regionNames['regency'];
        }

        if (! empty($regionNames['province'])) {
            $segments[] = $regionNames['province'];
        }

        return $segments ? implode(', ', $segments) : null;
    }
}
