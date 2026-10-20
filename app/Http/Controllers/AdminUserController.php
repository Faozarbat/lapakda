<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua user, urutkan dari yang terbaru
        // Eager load 'stores' untuk melihat apakah dia Seller atau cuma Pembeli
        $users = User::withCount('stores')->latest()->paginate(15);
        
        return view('admin.user.index', compact('users'));
    }
}