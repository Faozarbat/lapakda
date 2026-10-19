<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use App\Models\Order;
use App\Models\Subscription;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Statistik Utama
        $totalStores = Store::count();
        $totalUsers = User::count();
        $totalOrders = Order::count();
        
        // Pendapatan Langganan Bulan Ini
        $revenueThisMonth = Subscription::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // Grafik Pendaftaran Toko 7 Hari Terakhir
        $chartData = [];
        $chartLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Store::whereDate('created_at', $date)->count();
            $chartLabels[] = $date->format('d M');
            $chartData[] = $count;
        }

        // Toko Terbaru
        $recentStores = Store::with('owner', 'kecamatan')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalStores', 'totalUsers', 'totalOrders', 'revenueThisMonth', 
            'chartData', 'chartLabels', 'recentStores'
        ));
    }
}