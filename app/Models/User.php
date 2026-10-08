<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'classroom',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /** Tenant milik user (jika role = tenant) */
    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class);
    }

    /** Semua pesanan yang dibuat user ini */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** Riwayat klaim voucher promo */
    public function voucherClaims(): HasMany
    {
        return $this->hasMany(VoucherClaim::class);
    }

    // ─── Role Helpers ─────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTenant(): bool
    {
        return $this->role === 'tenant';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Dapatkan kelas pengguna untuk auto-fill data checkout
     */
    public function getDisplayClassroomAttribute(): string
    {
        if (!empty($this->classroom)) {
            return $this->classroom;
        }

        $lastClass = $this->orders()->latest()->value('customer_class');
        if (!empty($lastClass)) {
            return $lastClass;
        }

        return match ($this->role) {
            'admin'  => 'Admin Sekolah',
            'tenant' => 'Pengelola Stand',
            default  => '12 MIPA 1',
        };
    }
}
