<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingredient extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'category',
        'stock',
        'unit',
        'min_stock',
        'cost_per_unit',
        'supplier',
        'notes',
    ];

    protected $casts = [
        'stock'         => 'float',
        'min_stock'     => 'float',
        'cost_per_unit' => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->stock <= 0;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock > 0 && $this->stock <= $this->min_stock;
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'Habis';
        }
        if ($this->stock <= $this->min_stock) {
            return 'Menipis';
        }
        return 'Aman';
    }

    public function getFormattedStockAttribute(): string
    {
        // Format angka desimal jika ada pecahan (misal 2.5 kg) atau bilangan bulat (misal 10 kg)
        $val = (float) $this->stock;
        $formatted = (floor($val) == $val) ? number_format($val, 0, ',', '.') : number_format($val, 1, ',', '.');
        return "{$formatted} {$this->unit}";
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'bahan_pokok'  => 'Bahan Pokok',
            'daging_telur' => 'Daging & Telur',
            'bumbu_dapur'  => 'Bumbu Dapur',
            'sayuran'      => 'Sayuran Segar',
            'minuman'      => 'Bahan Minuman',
            'kemasan'      => 'Kemasan & Cup',
            default        => 'Lainnya',
        };
    }
}
