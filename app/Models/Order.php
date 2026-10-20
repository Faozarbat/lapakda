<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'store_id',
        'session_id',
        'user_id',
        'nomor_order',
        'fulfillment_type',
        'delivery_latitude',
        'delivery_longitude',
        'delivery_note',
        'status',
        'payment_status',
        'payment_method',
        'platform_fee',
    ];

    protected $casts = [
        'delivery_latitude' => 'decimal:7',
        'delivery_longitude' => 'decimal:7',
        'platform_fee' => 'integer',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Total keseluruhan order, dihitung dari harga_saat_beli (snapshot) tiap item —
     * BUKAN dari harga produk terkini, karena harga produk bisa berubah setelah order dibuat.
     */
    public function getTotalAttribute(): int
    {
        return $this->items->sum(fn ($item) => $item->harga_saat_beli * $item->qty);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
