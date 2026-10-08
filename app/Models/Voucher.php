<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'discount_percent',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'discount_percent' => 'integer',
        'expires_at'       => 'datetime',
        'is_active'        => 'boolean',
    ];

    public function claims(): HasMany
    {
        return $this->hasMany(VoucherClaim::class);
    }

    public function isExpired(): bool
    {
        return now()->gt($this->expires_at);
    }

    public function isValid(): bool
    {
        return $this->is_active && !$this->isExpired();
    }
}
