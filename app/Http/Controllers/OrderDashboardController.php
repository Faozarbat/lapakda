<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderDashboardController extends Controller
{
    public function index(Request $request, Store $store)
    {
        $this->authorize('view', $store);

        $statusFilter = $request->query('status', 'semua');

        // Hitung jumlah pesanan per status untuk Badge di Tab
        $counts = $store->orders()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalSemua = array_sum($counts);

        // Query pesanan
        $query = $store->orders()
            ->with(['items', 'user'])
            ->withCount(['messages as unread_count' => function($q) {
                $q->where('is_read', false)
                  ->where(function($sub) {
                      $sub->where('user_id', '!=', auth()->id())->orWhereNull('user_id');
                  });
            }])
            ->latest();

        if ($statusFilter !== 'semua') {
            $query->where('status', $statusFilter);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('dashboard.order.index', compact('store', 'orders', 'statusFilter', 'counts', 'totalSemua'));
    }

        public function allOrders(Request $request)
    {
        // Ambil ID toko milik user ini
        $storeIds = auth()->user()->stores()->pluck('id');

        // Query pesanan dari semua toko tersebut
        $query = Order::whereIn('store_id', $storeIds)
            ->with(['items', 'store', 'user'])
            ->withCount(['messages as unread_count' => function($q) {
                $q->where('is_read', false)
                  ->where(function($sub) {
                      $sub->where('user_id', '!=', auth()->id())->orWhereNull('user_id');
                  });
            }])
            ->latest();

        // Filter status (opsional, biar rapi)
        $statusFilter = $request->query('status', 'semua');
        if ($statusFilter !== 'semua') {
            $query->where('status', $statusFilter);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('dashboard.order.all', compact('orders', 'statusFilter'));
    }

    public function updateStatus(Request $request, Store $store, Order $order)
    {
        $this->authorize('update', $order);
        abort_if($order->store_id !== $store->id, 404);

        $validated = $request->validate([
            'status' => 'required|in:menunggu_konfirmasi,dikonfirmasi,selesai,dibatalkan',
        ]);

        $order->update(['status' => $validated['status']]);

        return back()->with('success', 'Status order #' . $order->nomor_order . ' berhasil diperbarui.');
    }
}