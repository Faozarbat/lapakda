<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\Store;

class PublicStoreController extends Controller
{
    public function show(string $kecamatan, string $toko)
    {
        $kecamatanModel = Kecamatan::where('slug', $kecamatan)->firstOrFail();

        $store = Store::where('kecamatan_id', $kecamatanModel->id)
            ->where('slug', $toko)
            ->with([
                'products' => fn ($query) => $query->tersedia(),
                'reviews' => fn ($query) => $query->with('user')->latest()
            ])
            ->firstOrFail();

        // Cek apakah toko dinonaktifkan manual, belum lunas biaya buka, atau sudah diarsipkan sistem
        if (! $store->status_aktif || ! $store->open_fee_paid || $store->subscription_status === 'archived') {
            return view('store.nonaktif', compact('store'));
        }

        return view('store.show', [
            'store' => $store,
        ]);
    }
}