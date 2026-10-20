<?php

namespace App\Support;

class KategoriUsaha
{
    /**
     * Daftar kategori yang ditampilkan sebagai tile di homepage.
     * Untuk menambah kategori baru: tambah 1 baris di sini saja.
     *
     * - 'aktif' => true  : kategori sudah dikembangkan, klik masuk ke direktori toko.
     * - 'aktif' => false : kategori belum dikembangkan, klik masuk ke halaman "Segera Hadir".
     * - 'kategori_db'    : nilai kolom `stores.kategori` yang cocok (null kalau belum ada di skema).
     */
    public static function semua(): array
    {
        return [
            'resto' => [
                'label' => 'Resto',
                'icon' => '🍽️',
                'aktif' => true,
                'kategori_db' => 'resto',
            ],
            'toko' => [
                'label' => 'Toko',
                'icon' => '🛒',
                'aktif' => true,
                'kategori_db' => 'store',
            ],
            'jasa' => [
                'label' => 'Jasa',
                'icon' => '🧰',
                'aktif' => false,
                'kategori_db' => null,
            ],
            'lainnya' => [
                'label' => 'Lainnya',
                'icon' => '✨',
                'aktif' => false,
                'kategori_db' => null,
            ],
        ];
    }
}