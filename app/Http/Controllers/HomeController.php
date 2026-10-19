<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Support\KategoriUsaha;

class HomeController extends Controller
{
    /**
     * Halaman utama sekarang adalah PEMILIH KATEGORI (Resto, Toko, Jasa, dll),
     * bukan langsung daftar toko. Daftar toko per kategori dipindah ke
     * StoreDirectoryController (route /jelajah/{kategoriSlug}).
     */
    public function index()
    {
        $kategoriList = KategoriUsaha::semua();

        foreach ($kategoriList as $slug => &$kategori) {
            if ($kategori['aktif'] && $kategori['kategori_db']) {
                $kategori['jumlah'] = Store::aktif()->where('kategori', $kategori['kategori_db'])->count();
            }
        }
        unset($kategori);

        return view('home', compact('kategoriList'));
    }
}