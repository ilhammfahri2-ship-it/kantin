<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'tenant_id',
        'order_number',
        'status',
        'payment_status',
        'subtotal',
        'discount',
        'total',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'discount'     => 'decimal:2',
        'total'        => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready']);
    }

    // ─── Accessors / Helpers ──────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'Menunggu Konfirmasi',
            'confirmed'  => 'Dikonfirmasi',
            'preparing'  => 'Sedang Disiapkan',
            'ready'      => 'Siap Diambil',
            'completed'  => 'Selesai',
            'cancelled'  => 'Dibatalkan',
            default      => 'Tidak Diketahui',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'yellow',
            'confirmed'  => 'blue',
            'preparing'  => 'orange',
            'ready'      => 'emerald',
            'completed'  => 'slate',
            'cancelled'  => 'red',
            default      => 'slate',
        };
    }

    /**
     * Generate nomor order unik: ORD-YYYYMMDD-XXXX
     */
    public static function generateOrderNumber(): string
    {
        $date    = now()->format('Ymd');
        $lastId  = static::whereDate('created_at', today())->count() + 1;

        return 'ORD-' . $date . '-' . str_pad($lastId, 4, '0', STR_PAD_LEFT);
    }
}
