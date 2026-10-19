<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Panggil seeder secara berurutan dari atas ke bawah
        $this->call([
            KecamatanSeeder::class,
            DemoDataSeeder::class,
            SettingSeeder::class,
            VoucherSeeder::class,
        ]);
        
    }
}