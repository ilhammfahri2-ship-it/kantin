<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'image',
        'price',
        'category',
        'stock',
        'is_available',
        'is_featured',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'is_available' => 'boolean',
        'is_featured'  => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('stock', '>', 0);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ─── Accessors / Helpers ──────────────────────────────────────────────────

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            // Cek apakah ada file lokal berdasarkan slug
            $slugImage = 'images/products/' . $this->slug . '.jpg';
            if (file_exists(public_path($slugImage))) {
                return asset($slugImage);
            }
            return asset('images/default-product.svg');
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        if (str_starts_with($this->image, 'images/')) {
            return asset($this->image);
        }

        if (file_exists(public_path('images/products/' . $this->image))) {
            return asset('images/products/' . $this->image);
        }

        return asset('storage/' . $this->image);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0 || ! $this->is_available) {
            return 'habis';
        }
        if ($this->stock <= 5) {
            return 'hampir habis';
        }
        return 'tersedia';
    }

    /**
     * Label kategori dalam Bahasa Indonesia.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'makanan_berat'  => 'Makanan Berat',
            'makanan_ringan' => 'Makanan Ringan',
            'minuman'        => 'Minuman',
            'dessert'        => 'Dessert',
            default          => 'Lainnya',
        };
    }
}
