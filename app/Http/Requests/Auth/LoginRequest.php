<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_hp' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $nomor = \App\Support\PhoneNumber::normalize($this->nomor_hp);
        $user = User::where('nomor_hp', $nomor)->first();

        if (! $user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'nomor_hp' => 'Nomor HP belum terdaftar.',
            ]);
        }

        // CEK PASSWORD KHUSUS ADMIN
        if ($user->is_admin) {
            if (! \Illuminate\Support\Facades\Hash::check($this->password, $user->password)) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'password' => 'Password admin salah.',
                ]);
            }
        }

        // TANGKAP ID SESSION LAMA SEBELUM LOGIN
        $oldSessionId = $this->session()->getId();

        Auth::login($user);

        // PINDAHKAN RIWAYAT GUEST KE USER INI
        \App\Models\Order::where('session_id', $oldSessionId)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);

        RateLimiter::clear($this->throttleKey());
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }
        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());
        throw ValidationException::withMessages([
            'nomor_hp' => trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('nomor_hp')).'|'.$this->ip());
    }
}