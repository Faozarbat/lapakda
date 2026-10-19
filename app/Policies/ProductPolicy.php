<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Store;
use App\Models\User;

class ProductPolicy
{
    /**
     * Dipakai SEBELUM produk dibuat — cek dulu apakah user berhak menambah
     * produk ke $store tertentu (biasanya dipanggil manual di Controller,
     * bukan lewat route model binding, karena belum ada instance Product).
     */
    public function create(User $user, Store $store): bool
    {
        return $store->owner_id === $user->id;
    }

    /**
     * Ini pengecekan UTAMA yang mencegah resto A edit produk resto B,
     * walau resto A tahu/nebak ID produk resto B (misal lewat URL manual).
     */
    public function update(User $user, Product $product): bool
    {
        return $product->store->owner_id === $user->id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $product->store->owner_id === $user->id;
    }
}
