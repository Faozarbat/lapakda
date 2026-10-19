<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Order $order)
    {
        // 1. Validasi input
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        // 2. Pastikan order ini milik user yang sedang login
        abort_if($order->user_id !== auth()->id(), 403, 'Ini bukan pesananmu.');

        // 3. Pastikan status order sudah 'selesai'
        abort_if($order->status !== 'selesai', 403, 'Kamu hanya bisa memberi ulasan jika pesanan sudah selesai.');

        // 4. Pastikan belum pernah di-review
        abort_if($order->review()->exists(), 403, 'Kamu sudah memberi ulasan untuk pesanan ini.');

        // 5. Simpan review
        Review::create([
            'order_id' => $order->id,
            'store_id' => $order->store_id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Terima kasih atas ulasanmu!');
    }
}