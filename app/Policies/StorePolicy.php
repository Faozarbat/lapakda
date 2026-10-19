<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;

class StorePolicy
{
    /**
     * Hanya pemilik toko yang boleh melihat dashboard/data internal tokonya.
     * (Halaman katalog PUBLIK tidak lewat policy ini — itu memang untuk umum.)
     */
    public function view(User $user, Store $store): bool
    {
        return $store->owner_id === $user->id;
    }

    public function update(User $user, Store $store): bool
    {
        return $store->owner_id === $user->id;
    }

    public function delete(User $user, Store $store): bool
    {
        return $store->owner_id === $user->id;
    }
}
