<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'logo',
        'banner',
        'location',
        'status',
        'open_at',
        'close_at',
    ];

    protected $casts = [
        'open_at'  => 'datetime:H:i',
        'close_at' => 'datetime:H:i',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ─── Accessors / Helpers ──────────────────────────────────────────────────

    /**
     * Cek apakah tenant sedang buka berdasarkan jam saat ini.
     */
    public function isOpen(): bool
    {
        if (! $this->open_at || ! $this->close_at) {
            return $this->status === 'active';
        }

        $now      = now()->format('H:i');
        $openAt   = substr((string) $this->open_at, 0, 5);
        $closeAt  = substr((string) $this->close_at, 0, 5);

        return $now >= $openAt && $now <= $closeAt && $this->status === 'active';
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : asset('images/default-tenant.svg');
    }
}
