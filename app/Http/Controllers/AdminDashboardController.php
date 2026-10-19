<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use App\Models\Order;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Data statistik ringkas untuk Admin
        $totalStores = Store::count();
        $totalUsers = User::count();
        $totalOrders = Order::count();
        
        // Ambil 5 toko terbaru
        $recentStores = Store::with('owner', 'kecamatan')->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalStores', 'totalUsers', 'totalOrders', 'recentStores'));
    }
}