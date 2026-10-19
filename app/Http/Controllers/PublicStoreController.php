<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\Store;

class PublicStoreController extends Controller
{
    public function show(string $kecamatan, string $toko)
    {
        $kecamatanModel = Kecamatan::where('slug', $kecamatan)->firstOrFail();

        // Dicari TANPA scopeAktif() dulu, supaya kita bisa bedakan antara
        // "toko benar-benar tidak ada" (404 biasa) vs "toko ada tapi nonaktif"
        // (pesan ramah, bukan 404 polos) — sesuai PRODUCT_SPEC.md bagian 12.3.
        $store = Store::where('kecamatan_id', $kecamatanModel->id)
            ->where('slug', $toko)
            ->with([
                'products' => fn ($query) => $query->tersedia(),
                'reviews' => fn ($query) => $query->with('user')->latest()
            ])
            ->firstOrFail();

        if (! $store->status_aktif) {
            return view('store.nonaktif', compact('store'));
        }

        return view('store.show', [
            'store' => $store,
        ]);
    }
}