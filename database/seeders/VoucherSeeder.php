<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Voucher;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        Voucher::updateOrCreate(
            ['code' => 'UNDANGAN'],
            [
                'description' => 'Promo Undangan Spesial (Gratis 3 Bulan)',
                'free_months' => 3,
                'valid_until' => '2026-12-31 23:59:59',
                'is_active' => true,
            ]
        );
    }
}