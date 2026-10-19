<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class AdminStoreController extends Controller
{
    public function index()
    {
        $stores = Store::with('owner', 'kecamatan')->latest()->paginate(10);
        return view('admin.store.index', compact('stores'));
    }

    public function toggleStatus(Store $store)
    {
        // Toggle status_aktif (Buka/Tutup)
        $store->update([
            'status_aktif' => !$store->status_aktif
        ]);

        $status = $store->status_aktif ? 'diaktifkan kembali' : 'ditangguhkan (Suspend)';
        return back()->with('success', "Toko {$store->nama} berhasil {$status}.");
    }
}