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

    public function exportCsv()
    {
        $stores = Store::with('owner', 'kecamatan')->latest()->get();
        $filename = 'LapakDA_Daftar_Toko_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Nama Toko', 'Kategori', 'Kecamatan', 'Pemilik', 'No HP', 'Berlaku Hingga', 'Status Langganan', 'Status Buka'];

        $callback = function() use($stores, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($stores as $store) {
                fputcsv($file, [
                    $store->id,
                    $store->nama,
                    $store->kategori,
                    $store->kecamatan->nama ?? '-',
                    $store->owner->name ?? '-',
                    $store->owner->nomor_hp ?? '-',
                    $store->active_until ? $store->active_until->format('Y-m-d') : '-',
                    $store->subscription_status,
                    $store->status_aktif ? 'Buka' : 'Tutup'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}