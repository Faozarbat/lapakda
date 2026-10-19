<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Membuat 1 user, 1 toko, dan 2 produk contoh — HANYA untuk keperluan
     * testing tampilan selama development. Hapus/skip seeder ini nanti
     * kalau sudah masuk tahap production.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Pemilik Warung Bahagia',
            'nomor_hp' => '6281999263536',
            'email' => null, // sekarang opsional
        ]);

        $kecamatan = Kecamatan::where('slug', 'nongsa')->first();

        $store = Store::create([
            'owner_id' => $user->id,
            'kecamatan_id' => $kecamatan->id,
            'nama' => 'Warung Bahagia',
            'slug' => 'warung-bahagia',
            'kategori' => 'resto',
            'nomor_wa' => '6281999263536',
            'alamat' => 'Jl. Testing No. 1, Nongsa',
            'latitude' => 1.1855,
            'longitude' => 104.0305,
            'has_table_number' => false,
            'status_aktif' => true,
        ]);

        Product::create([
            'store_id' => $store->id,
            'nama' => 'Nasi Goreng Spesial',
            'harga' => 25000,
            'status_tersedia' => true,
        ]);

        Product::create([
            'store_id' => $store->id,
            'nama' => 'Es Teh Manis',
            'harga' => 5000,
            'status_tersedia' => true,
        ]);
    }
}