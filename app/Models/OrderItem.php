<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'nama_produk_saat_beli',
        'harga_saat_beli',
        'qty',
        'consumption_type',
        'table_number',
    ];

    protected $casts = [
        'harga_saat_beli' => 'integer',
        'qty' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Nullable — produk sumbernya mungkin sudah dihapus (lihat nullOnDelete di migrasi).
     * Jangan andalkan relasi ini untuk tampilkan nama/harga; selalu pakai kolom snapshot.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
