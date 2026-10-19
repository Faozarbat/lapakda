<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Catatan: TIDAK implements MustVerifyEmail — verifikasi via email tidak
// dipakai di alur ini (login berbasis nomor HP). Saat OTP nanti aktif,
// verifikasi berpindah ke `phone_verified_at`, bukan `email_verified_at`.
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nomor_hp',
        'email',
        'password',
        'last_latitude',
        'last_longitude',
        'last_delivery_note',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class, 'owner_id');
    }
}