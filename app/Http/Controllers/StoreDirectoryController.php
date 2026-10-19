<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\Store;
use App\Support\KategoriUsaha;
use Illuminate\Http\Request;

class StoreDirectoryController extends Controller
{
    public function index(Request $request, string $kategoriSlug)
    {
        $daftarKategori = KategoriUsaha::semua();

        if (! isset($daftarKategori[$kategoriSlug])) {
            abort(404);
        }

        $kategori = $daftarKategori[$kategoriSlug];

        // Kategori belum dikembangkan -> halaman "Segera Hadir", bukan direktori kosong
        if (! $kategori['aktif']) {
            return view('jelajah.segera-hadir', compact('kategoriSlug', 'kategori'));
        }

        $query = Store::aktif()->with('kecamatan')->where('kategori', $kategori['kategori_db']);

        if ($request->filled('kecamatan_id')) {
            $query->where('kecamatan_id', $request->kecamatan_id);
        }

        $urutBerdasarkanJarak = $request->filled('lat') && $request->filled('lng');

        if ($urutBerdasarkanJarak) {
            $lat = (float) $request->query('lat');
            $lng = (float) $request->query('lng');

            $query->selectRaw(
                'stores.*, (
                    6371 * acos(
                        cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?))
                        + sin(radians(?)) * sin(radians(latitude))
                    )
                ) AS jarak_km',
                [$lat, $lng, $lat]
            )->orderBy('jarak_km');
        } else {
            $query->latest();
        }

        $stores = $query->paginate(12)->withQueryString();
        $kecamatans = Kecamatan::orderBy('nama')->get();

        return view('jelajah.index', [
            'stores' => $stores,
            'kecamatans' => $kecamatans,
            'filterKecamatan' => $request->kecamatan_id,
            'urutBerdasarkanJarak' => $urutBerdasarkanJarak,
            'kategoriSlug' => $kategoriSlug,
            'kategori' => $kategori,
        ]);
    }
}