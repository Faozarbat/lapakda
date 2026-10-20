<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Support\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GuestAuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Validasi input dari popup
        $request->validate([
            'name' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:20',
        ]);

        // 2. Normalisasi nomor HP (format 08xx otomatis jadi 62xx)
        $nomor = PhoneNumber::normalize($request->nomor_hp);

        // 3. Cek apakah nomor sudah terdaftar
        $user = User::where('nomor_hp', $nomor)->first();

        if ($user) {
            // JIKA SUDAH ADA: Tolak login otomatis. Paksa user login manual untuk keamanan.
            return response()->json([
                'success' => false, 
                'message' => 'Nomor WA ini sudah terdaftar. Silakan login melalui menu Masuk.'
            ], 403);
        }

        // Jika belum ada, buat akun baru (password acak)
        $user = User::create([
            'name' => $request->name,
            'nomor_hp' => $nomor,
            'password' => Hash::make(Str::random(16)),
        ]);

        // 4. TANGKAP ID SESSION LAMA SEBELUM LOGIN
        // Ini kunci agar riwayat guest tidak hilang saat session diganti Laravel
        $oldSessionId = $request->session()->getId();

        // 5. Login-kan user
        Auth::login($user);

        // 6. PINDAHKAN RIWAYAT GUEST KE USER INI
        // Mengubah data pesanan yang tadinya milik session lama, jadi milik user_id baru
        Order::where('session_id', $oldSessionId)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);

        return response()->json([
            'success' => true, 
            'message' => 'Berhasil masuk! Pesananmu sekarang tersimpan.'
        ]);
    }
}