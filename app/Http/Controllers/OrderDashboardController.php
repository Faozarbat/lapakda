<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Store;
use Illuminate\Http\Request;

class OrderDashboardController extends Controller
{
    public function index(Store $store)
    {
        $this->authorize('view', $store);

        $orders = $store->orders()->with('items')->latest()->paginate(20);

        return view('dashboard.order.index', compact('store', 'orders'));
    }

    public function updateStatus(Request $request, Store $store, Order $order)
    {
        $this->authorize('update', $order);

        // Guard tambahan: pastikan order ini benar milik $store di URL,
        // bukan cuma milik owner yang sama (jaga-jaga kalau owner punya >1 toko)
        abort_if($order->store_id !== $store->id, 404);

        $validated = $request->validate([
            'status' => 'required|in:menunggu_konfirmasi,dikonfirmasi,selesai,dibatalkan',
        ]);

        $order->update(['status' => $validated['status']]);

        return back()->with('success', 'Status order #' . $order->nomor_order . ' berhasil diperbarui.');
    }
}