<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Kecamatan;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('q');
        $kecamatans = Kecamatan::orderBy('nama')->get();

        if ($keyword) {
            $query = Store::tampilDiPublik()
                ->with('kecamatan')
                ->where(function ($q) use ($keyword) {
                    // Cari berdasarkan Nama Toko ATAU Nama Produk di dalam toko tersebut
                    $q->where('nama', 'like', "%{$keyword}%")
                      ->orWhereHas('products', function ($pq) use ($keyword) {
                          $pq->where('nama', 'like', "%{$keyword}%");
                      });
                });

            if ($request->filled('kecamatan_id')) {
                $query->where('kecamatan_id', $request->kecamatan_id);
            }

            $stores = $query->latest()->paginate(12)->withQueryString();
        } else {
            $stores = collect(); // Kosongkan jika belum mengetik
        }

        return view('search.index', compact('stores', 'keyword', 'kecamatans'));
    }
}