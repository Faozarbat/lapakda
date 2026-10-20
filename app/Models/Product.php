<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'store_id',
        'nama',
        'harga',
        'stok',
        'foto',
        'deskripsi',
        'kategori_produk',
        'status_tersedia',
    ];

    protected $casts = [
        'harga' => 'integer',
        'status_tersedia' => 'boolean',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope: hanya produk yang berstatus tersedia — dipakai di katalog publik.
     */
    public function scopeTersedia($query)
    {
        return $query->where('status_tersedia', true);
    }
}
