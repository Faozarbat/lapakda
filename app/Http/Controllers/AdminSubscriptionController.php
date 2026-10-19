<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSubscriptionController extends Controller
{
    public function index()
    {
        // Tampilkan tagihan yang statusnya pending (belum dibayar / nunggu verifikasi)
        $subscriptions = Subscription::with(['store.owner', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.subscription.index', compact('subscriptions'));
    }

    public function approve(Subscription $subscription)
    {
        DB::transaction(function () use ($subscription) {
            // 1. Ubah status tagihan jadi lunas
            $subscription->update([
                'status' => 'paid',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            $store = $subscription->store;

            // 2. Jika ini biaya buka resto
            if ($subscription->type === 'open_fee') {
                $store->update(['open_fee_paid' => true]);
            } 
            // 3. Jika ini perpanjangan langganan
            elseif ($subscription->type === 'subscription') {
                // Tambah masa aktif 1 bulan dari tanggal expired terakhir (atau dari hari ini jika sudah lewat)
                $newActiveUntil = ($store->active_until && $store->active_until->isFuture()) 
                    ? $store->active_until->addMonth() 
                    : now()->addMonth();

                $store->update([
                    'active_until' => $newActiveUntil,
                    'subscription_status' => 'active',
                ]);
            }
        });

        return back()->with('success', 'Tagihan berhasil diverifikasi dan masa aktif telah diperbarui.');
    }
}