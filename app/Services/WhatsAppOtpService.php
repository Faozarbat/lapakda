<?php

namespace App\Services;

use App\Support\PhoneNumber;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppOtpService
{
    private const TTL_MENIT = 5;
    private const MAKS_PERCOBAAN = 5;

    /**
     * Generate kode 6 digit, simpan di Cache (TTL 5 menit), lalu kirim via
     * service Node.js. Return true kalau pesan berhasil dikirim.
     */
    public function kirimOtpKe(string $nomorHp): bool
    {
        $nomorHp = PhoneNumber::normalize($nomorHp);
        $kode = (string) random_int(100000, 999999);

        Cache::put($this->cacheKeyKode($nomorHp), $kode, now()->addMinutes(self::TTL_MENIT));
        Cache::put($this->cacheKeyPercobaan($nomorHp), 0, now()->addMinutes(self::TTL_MENIT));

        $pesan = "Kode OTP LapakDA kamu: *{$kode}*\n\n"
            . "Jangan bagikan kode ini ke siapapun, termasuk yang mengaku dari pihak LapakDA. "
            . 'Kode berlaku ' . self::TTL_MENIT . ' menit.';

        return $this->kirimViaService($nomorHp, $pesan);
    }

    /**
     * Cek kode yang diinput user. Return ['valid' => bool, 'pesan' => string].
     * Otomatis membatasi maksimal percobaan salah supaya tidak bisa ditebak
     * brute-force (6 digit = 1 juta kemungkinan, tapi tetap dibatasi).
     */
    public function verifikasi(string $nomorHp, string $kodeInput): array
    {
        $nomorHp = PhoneNumber::normalize($nomorHp);
        $kodeTersimpan = Cache::get($this->cacheKeyKode($nomorHp));

        if (! $kodeTersimpan) {
            return ['valid' => false, 'pesan' => 'Kode OTP sudah kedaluwarsa atau belum pernah dikirim. Minta kode baru.'];
        }

        $percobaan = (int) Cache::get($this->cacheKeyPercobaan($nomorHp), 0);

        if ($percobaan >= self::MAKS_PERCOBAAN) {
            Cache::forget($this->cacheKeyKode($nomorHp));
            return ['valid' => false, 'pesan' => 'Terlalu banyak percobaan salah. Minta kode baru.'];
        }

        if ($kodeInput !== $kodeTersimpan) {
            Cache::increment($this->cacheKeyPercobaan($nomorHp));
            return ['valid' => false, 'pesan' => 'Kode OTP salah. Coba lagi.'];
        }

        // Kode benar -> langsung dibuang supaya tidak bisa dipakai 2 kali
        Cache::forget($this->cacheKeyKode($nomorHp));
        Cache::forget($this->cacheKeyPercobaan($nomorHp));

        return ['valid' => true, 'pesan' => 'OK'];
    }

    private function kirimViaService(string $nomorHp, string $pesan): bool
    {
        $baseUrl = config('services.whatsapp_otp.url');
        $apiKey = config('services.whatsapp_otp.key');

        if (! $baseUrl || ! $apiKey) {
            Log::error('WA_OTP_SERVICE_URL atau WA_OTP_SERVICE_KEY belum di-set di .env.');
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-API-KEY' => $apiKey])
                ->post(rtrim($baseUrl, '/') . '/send-otp', [
                    'phone' => $nomorHp,
                    'message' => $pesan,
                ]);

            if (! $response->successful() || $response->json('success') !== true) {
                Log::warning('Gagal kirim OTP WhatsApp', ['response' => $response->body()]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Exception saat kirim OTP WhatsApp: ' . $e->getMessage());
            return false;
        }
    }

    private function cacheKeyKode(string $nomorHp): string
    {
        return "otp:kode:{$nomorHp}";
    }

    private function cacheKeyPercobaan(string $nomorHp): string
    {
        return "otp:percobaan:{$nomorHp}";
    }
}
