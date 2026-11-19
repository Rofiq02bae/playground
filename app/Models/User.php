<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'username', 'phone', 'address', 'is_member'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // RBAC Fase 2 — admin = perusahaan (semua akses, termasuk blog & webapp).
    public const ROLE_ADMIN = 'admin';

    public const ROLE_PEMILIK = 'pemilik';

    public const ROLE_PENGGARAP = 'penggarap';

    public const ROLES = [self::ROLE_ADMIN, self::ROLE_PEMILIK, self::ROLE_PENGGARAP];

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

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Admin (Perusahaan)',
            self::ROLE_PEMILIK => 'Pemilik Sawah',
            self::ROLE_PENGGARAP => 'Penggarap Sawah',
            default => ucfirst((string) $this->role),
        };
    }

    public function membershipLabel(): string
    {
        return $this->is_member ? 'Membership' : 'Bukan Membership';
    }
}
