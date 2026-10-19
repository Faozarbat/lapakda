<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

class AdminSearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('q');

        $stores = [];
        $users = [];

        if ($keyword) {
            $stores = Store::with('kecamatan')
                ->where('nama', 'like', "%{$keyword}%")
                ->latest()
                ->get();

            $users = User::where('name', 'like', "%{$keyword}%")
                ->orWhere('nomor_hp', 'like', "%{$keyword}%")
                ->latest()
                ->get();
        }

        return view('admin.search', compact('keyword', 'stores', 'users'));
    }
}