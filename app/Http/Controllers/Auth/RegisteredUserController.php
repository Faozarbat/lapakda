<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WhatsAppOtpService;
use App\Support\PhoneNumber;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $oldSessionId = $request->session()->getId(); // <-- TANGKAP DULU DI ATAS

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nomor_hp' => ['required', 'string', 'max:20'],
        ]);

        $nomor = \App\Support\PhoneNumber::normalize($request->nomor_hp);

        if (\App\Models\User::where('nomor_hp', $nomor)->exists()) {
            return back()->withErrors(['nomor_hp' => 'Nomor HP ini sudah terdaftar. Silakan login.']);
        }

        $user = \App\Models\User::create([
            'name' => $request->name,
            'nomor_hp' => $nomor,
            'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
        ]);

        // KLAIM RIWAYAT GUEST
        \App\Models\Order::where('session_id', $oldSessionId)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);

        event(new \Illuminate\Auth\Events\Registered($user));

        \Illuminate\Support\Facades\Auth::login($user);

        return redirect(route('home', absolute: false));
    }
}
