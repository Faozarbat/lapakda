<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'open_fee_price' => Setting::getVal('open_fee_price', 20000),
            'monthly_price' => Setting::getVal('monthly_price', 10000),
            'discount_price' => Setting::getVal('discount_price', 5000),
            'grace_period_days' => Setting::getVal('grace_period_days', 3),
        ];

        return view('admin.setting.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'open_fee_price' => 'required|integer|min:0',
            'monthly_price' => 'required|integer|min:0',
            'discount_price' => 'required|integer|min:0',
            'grace_period_days' => 'required|integer|min:0',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => (string) $value, 'type' => 'integer']
            );
        }

        return back()->with('success', 'Pengaturan harga berhasil diperbarui.');
    }
}