<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Support\PhoneNumber;

class Store extends Model
{
    protected $fillable = [
        'owner_id',
        'kecamatan_id',
        'nama',
        'foto_profil',
        'foto_background',
        'slug',
        'kategori',
        'nomor_wa',
        'alamat',
        'jam_operasional',
        'latitude',
        'longitude',
        'has_table_number',
        'status_aktif',
        // --- KOLOM BARU LANGGANAN ---
        'active_until',
        'subscription_status',
        'open_fee_paid',
        'voucher_id',
        'subscription_started_at',
    ];

    protected $casts = [
        'jam_operasional' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'has_table_number' => 'boolean',
        'status_aktif' => 'boolean',
        // --- CASTS BARU ---
        'active_until' => 'datetime',
        'open_fee_paid' => 'boolean',
        'subscription_started_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    
    // --- RELASI BARU ---
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // --- SCOPES ---
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    public function scopeTampilDiPublik($query)
    {
        return $query->where('status_aktif', true)
                     ->where('open_fee_paid', true)
                     ->where('subscription_status', '!=', 'archived');
    }

    // Cek apakah toko masih dalam masa aktif (termasuk grace period)
    public function isActiveSubscribed(): bool
    {
        if ($this->subscription_status === 'archived') {
            return false;
        }
        return $this->active_until === null || $this->active_until->isFuture();
    }

    public static function normalisasiNomorWa(string $nomor): string
    {
        return PhoneNumber::normalize($nomor);
    }

    public function getRatingAttribute()
    {
        return $this->reviews()->avg('rating') ? round($this->reviews()->avg('rating'), 1) : 0;
    }

    public function getTotalTerjualAttribute()
    {
        return \App\Models\OrderItem::whereHas('order', function($q) {
            $q->where('store_id', $this->id)
              ->where('status', 'selesai');
        })->sum('qty');
    }

    public function isOpenNow()
    {
        if (empty($this->jam_operasional)) {
            return true; // Jika tidak ada setting, anggap buka
        }

        $waktuSekarang = now('Asia/Jakarta');
        
        // Mapping index hari (0 = Minggu, 1 = Senin, dst) ke bahasa Indonesia. 
        // Ini jauh lebih aman daripada pakai translatedFormat() yang bergantung setting bahasa.
        $days = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        $hariIni = $days[$waktuSekarang->dayOfWeek]; 
        
        $jadwalHariIni = $this->jam_operasional[$hariIni] ?? null;

        if (!$jadwalHariIni || ($jadwalHariIni['libur'] ?? false)) {
            return false; // Tutup jika libur / jadwal kosong
        }

        $jamBuka = $jadwalHariIni['buka'] ?? '00:00';
        $jamTutup = $jadwalHariIni['tutup'] ?? '23:59';
        $jamSekarang = $waktuSekarang->format('H:i');

        if ($jamBuka <= $jamTutup) {
            return $jamSekarang >= $jamBuka && $jamSekarang <= $jamTutup;
        } else {
            // Handle jika tutup lewat tengah malam (misal 18:00 - 02:00)
            return $jamSekarang >= $jamBuka || $jamSekarang <= $jamTutup;
        }
    }
}