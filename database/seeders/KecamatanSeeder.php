<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class KecamatanSeeder extends Seeder
{
    /**
     * Daftar kecamatan Kota Batam — data awal supaya dropdown pendaftaran toko
     * tidak kosong. Tambah/koreksi manual kalau ada kecamatan yang terlewat.
     */
    public function run(): void
    {
        $kecamatans = [
            'Batam Kota',
            'Sekupang',
            'Lubuk Baja',
            'Batu Aji',
            'Sagulung',
            'Nongsa',
            'Bengkong',
            'Batu Ampar',
            'Bulang',
            'Galang',
            'Sei Beduk',
            'Belakang Padang',
        ];

        foreach ($kecamatans as $nama) {
            DB::table('kecamatans')->insert([
                'nama' => $nama,
                'slug' => Str::slug($nama),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
