<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Support\PhoneNumber;

class Store extends Model
{
    protected $fillable = [
        'owner_id',
        'kecamatan_id',
        'nama',
        'slug',
        'kategori',
        'nomor_wa',
        'alamat',
        'jam_operasional',
        'latitude',
        'longitude',
        'has_table_number',
        'status_aktif',
    ];

    protected $casts = [
        'jam_operasional' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'has_table_number' => 'boolean',
        'status_aktif' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    public static function normalisasiNomorWa(string $nomor): string
    {
        return PhoneNumber::normalize($nomor);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Opsional: Accessor untuk rating rata-rata
    public function getRatingAttribute()
    {
        return $this->reviews()->avg('rating') ? round($this->reviews()->avg('rating'), 1) : 0;
    }

        public function getTotalTerjualAttribute()
    {
        return \App\Models\OrderItem::whereHas('order', function($q) {
            $q->where('store_id', $this->id)
              ->where('status', 'selesai');
        })->sum('qty');
    }
}