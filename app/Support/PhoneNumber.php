<?php

namespace App\Support;

class PhoneNumber
{
    /**
     * Normalisasi nomor HP Indonesia ke format internasional (62xxx) tanpa
     * simbol atau spasi. Dipakai KONSISTEN di seluruh aplikasi (akun user
     * maupun nomor WA toko) — satu sumber logic, bukan duplikat di banyak
     * tempat, supaya gampang disesuaikan nanti kalau format berubah atau
     * saat integrasi provider OTP (yang biasanya juga butuh format E.164).
     */
    public static function normalize(string $nomor): string
    {
        $bersih = preg_replace('/[^\d+]/', '', $nomor);
        $bersih = ltrim($bersih, '+');

        if (str_starts_with($bersih, '0')) {
            return '62' . substr($bersih, 1);
        }

        if (str_starts_with($bersih, '62')) {
            return $bersih;
        }

        return '62' . $bersih;
    }
}