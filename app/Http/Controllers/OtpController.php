<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\WhatsAppOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    public function show(Request $request)
    {
        $userId = $request->session()->get('otp_user_id');

        if (! $userId) {
            return redirect()->route('register');
        }

        $user = User::findOrFail($userId);

        return view('auth.verifikasi-otp', ['nomorHp' => $user->nomor_hp]);
    }

    public function verify(Request $request, WhatsAppOtpService $otpService)
    {
        $userId = $request->session()->get('otp_user_id');

        if (! $userId) {
            return redirect()->route('register');
        }

        $validated = $request->validate([
            'kode' => 'required|string|size:6',
        ]);

        $user = User::findOrFail($userId);
        $hasil = $otpService->verifikasi($user->nomor_hp, $validated['kode']);

        if (! $hasil['valid']) {
            return back()->withErrors(['kode' => $hasil['pesan']]);
        }

        $user->update(['phone_verified_at' => now()]);
        $request->session()->forget('otp_user_id');

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Nomor WhatsApp berhasil diverifikasi! Selamat datang di LapakDA.');
    }

    public function resend(Request $request, WhatsAppOtpService $otpService)
    {
        $userId = $request->session()->get('otp_user_id');

        if (! $userId) {
            return redirect()->route('register');
        }

        $user = User::findOrFail($userId);
        $terkirim = $otpService->kirimOtpKe($user->nomor_hp);

        if (! $terkirim) {
            return back()->withErrors(['kode' => 'Gagal mengirim ulang kode. Pastikan service WhatsApp aktif, atau coba lagi sebentar.']);
        }

        return back()->with('success', 'Kode baru sudah dikirim ke WhatsApp kamu.');
    }
}
