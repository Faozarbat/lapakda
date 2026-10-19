<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'qty',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Subtotal baris ini, dihitung dari harga produk TERKINI (bukan snapshot —
     * cart belum final/checkout, jadi wajar ambil harga live dari tabel Product).
     * Snapshot harga baru terjadi nanti saat cart dikonversi ke Order.
     */
    public function getSubtotalAttribute(): int
    {
        return $this->product->harga * $this->qty;
    }
}
