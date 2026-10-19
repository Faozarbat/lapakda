<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Resto cuma boleh lihat order yang MASUK KE TOKONYA SENDIRI,
     * bukan order yang masuk ke toko lain di platform ini.
     */
    public function view(User $user, Order $order): bool
    {
        return $order->store->owner_id === $user->id;
    }

    /**
     * Dipakai saat resto mengubah status order (menunggu_konfirmasi -> dikonfirmasi -> selesai, dst).
     * Konsumen (guest, tanpa login) TIDAK melewati policy ini — order dibuat lewat
     * proses checkout publik yang terpisah, bukan lewat endpoint yang di-guard policy ini.
     */
    public function update(User $user, Order $order): bool
    {
        return $order->store->owner_id === $user->id;
    }
}
