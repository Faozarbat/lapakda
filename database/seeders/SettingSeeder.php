<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'open_fee_price', 'value' => '20000', 'type' => 'integer', 'description' => 'Biaya buka resto ke-2 dst'],
            ['key' => 'monthly_price', 'value' => '10000', 'type' => 'integer', 'description' => 'Biaya langganan normal per bulan'],
            ['key' => 'discount_price', 'value' => '5000', 'type' => 'integer', 'description' => 'Biaya langganan diskon 50%'],
            ['key' => 'grace_period_days', 'value' => '3', 'type' => 'integer', 'description' => 'Masa tenggang sebelum toko diarsipkan'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}