<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubscriptionController extends Controller
{
    public function index()
    {
        // Ambil semua toko milik user beserta data langganannya
        $stores = auth()->user()->stores()
            ->with(['subscriptions' => function($q) {
                $q->latest();
            }])
            ->get();

        return view('dashboard.subscription.index', compact('stores'));
    }

    public function uploadProof(Request $request, Subscription $subscription)
    {
        // Pastikan tagihan ini milik toko milik user yang login
        if ($subscription->store->owner_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('payment_proof')) {
            // Hapus file lama jika ada
            if ($subscription->payment_proof && Storage::disk('public')->exists($subscription->payment_proof)) {
                Storage::disk('public')->delete($subscription->payment_proof);
            }
            
            $path = $request->file('payment_proof')->store('bukti-transfer', 'public');
            
            $subscription->update([
                'payment_proof' => $path,
                'notes' => ($subscription->notes ? $subscription->notes . ' | ' : '') . 'Bukti transfer diupload pada ' . now()->format('d/m/Y H:i'),
            ]);
        }

        return back()->with('success', 'Bukti transfer berhasil diunggah. Menunggu verifikasi Admin.');
    }
}