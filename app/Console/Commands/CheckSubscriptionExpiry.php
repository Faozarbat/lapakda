<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Store;
use App\Models\Setting;
use Carbon\Carbon;

class CheckSubscriptionExpiry extends Command
{
    // Nama perintah yang akan dipanggil di terminal/scheduler
    protected $signature = 'subscriptions:check-expiry';
    protected $description = 'Mengarsipkan toko yang masa aktifnya sudah lewat masa tenggang';

    public function handle()
    {
        $graceDays = Setting::getVal('grace_period_days', 3);
        $batasWaktu = Carbon::now()->subDays($graceDays);

        // Cari toko yang active_until-nya sudah lewat dari batas waktu grace period
        // dan statusnya belum 'archived'
        $stores = Store::where('subscription_status', '!=', 'archived')
            ->whereNotNull('active_until')
            ->where('active_until', '<', $batasWaktu)
            ->get();

        $count = 0;
        foreach ($stores as $store) {
            $store->update([
                'subscription_status' => 'archived',
                'status_aktif' => false, // Matikan juga status bukanya agar aman
            ]);
            $count++;
        }

        $this->info("Selesai! {$count} toko telah diarsipkan karena melewati masa tenggang.");
    }
}