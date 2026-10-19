<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'session_id',
        'store_id',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Scope: cari cart milik session tertentu untuk toko tertentu.
     * Dipakai supaya tiap toko punya cart terpisah per sesi guest.
     */
    public function scopeUntukSesiDanToko($query, string $sessionId, int $storeId)
    {
        return $query->where('session_id', $sessionId)->where('store_id', $storeId);
    }
}
