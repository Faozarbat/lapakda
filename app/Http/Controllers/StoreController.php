<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\Store;
use App\Models\Setting;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function create()
    {
        $kecamatans = Kecamatan::orderBy('nama')->get();
        
        $storeCount = auth()->user()->stores()->count();
        $isFirstStore = $storeCount === 0;
        $openFee = $isFirstStore ? 0 : Setting::getVal('open_fee_price', 20000);
        $monthlyFee = Setting::getVal('monthly_price', 10000);

        return view('store.create', compact('kecamatans', 'isFirstStore', 'openFee', 'monthlyFee'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:resto,store',
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'alamat' => 'required|string|max:255',
            'nomor_wa' => 'required|string|max:20',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'voucher_code' => 'nullable|string|max:50',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'foto_background' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $slug = Str::slug($validated['nama']);
        $sudahAda = Store::where('kecamatan_id', $validated['kecamatan_id'])
            ->where('slug', $slug)->exists();

        if ($sudahAda) {
            return back()->withErrors(['nama' => 'Nama toko ini sudah dipakai di kecamatan yang sama.'])->withInput();
        }

        $user = auth()->user();
        $isFirstStore = $user->stores()->count() === 0;
        $openFeeAmount = $isFirstStore ? 0 : Setting::getVal('open_fee_price', 20000);

        $voucher = null;
        $activeUntil = null;
        $subStatus = 'active';

        if ($request->filled('voucher_code')) {
            $code = strtoupper(trim($request->voucher_code));
            $voucher = Voucher::where('code', $code)->where('is_active', true)->first();

            if (!$voucher) return back()->withErrors(['voucher_code' => 'Kode voucher tidak ditemukan atau tidak aktif.'])->withInput();
            if ($voucher->valid_until && $voucher->valid_until->isPast()) return back()->withErrors(['voucher_code' => 'Kode voucher sudah kedaluwarsa.'])->withInput();
            if (VoucherUsage::where('user_id', $user->id)->exists()) return back()->withErrors(['voucher_code' => 'Anda sudah pernah menggunakan voucher sebelumnya.'])->withInput();

            $activeUntil = now()->addMonths($voucher->free_months);
            $subStatus = 'trial';
        } else {
            $activeUntil = now()->addMonth();
        }

        DB::transaction(function () use ($request, $validated, $slug, $user, $openFeeAmount, $voucher, $activeUntil, $subStatus) {
            
            $fotoProfilPath = $request->hasFile('foto_profil') ? $request->file('foto_profil')->store('stores/profil', 'public') : null;
            $fotoBackgroundPath = $request->hasFile('foto_background') ? $request->file('foto_background')->store('stores/background', 'public') : null;

            $store = Store::create([
                'owner_id' => $user->id,
                'kecamatan_id' => $validated['kecamatan_id'],
                'nama' => $validated['nama'],
                'slug' => $slug,
                'foto_profil' => $fotoProfilPath,
                'foto_background' => $fotoBackgroundPath,
                'kategori' => $validated['kategori'],
                'nomor_wa' => Store::normalisasiNomorWa($validated['nomor_wa']),
                'alamat' => $validated['alamat'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'has_table_number' => false,
                'status_aktif' => true,
                'active_until' => $activeUntil,
                'subscription_status' => $subStatus,
                'subscription_started_at' => now(),
                'voucher_id' => $voucher ? $voucher->id : null,
                'open_fee_paid' => $openFeeAmount === 0,
            ]);

            if ($voucher) {
                VoucherUsage::create([
                    'voucher_id' => $voucher->id, 'user_id' => $user->id, 'store_id' => $store->id,
                ]);
            }

            if ($openFeeAmount > 0) {
                Subscription::create([
                    'store_id' => $store->id, 'user_id' => $user->id, 'type' => 'open_fee',
                    'amount' => $openFeeAmount, 'status' => 'pending', 'notes' => 'Biaya pembukaan resto ke-2 dst.',
                ]);
            }
        });

        $store = Store::where('owner_id', $user->id)->latest()->first();
        return redirect()->route('store.show', [$store->kecamatan->slug, $store->slug])->with('success', 'Toko berhasil didaftarkan!');
    }

    public function edit(Store $store)
    {
        $this->authorize('update', $store);
        $hariList = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        $jamOperasional = $store->jam_operasional ?? [];

        return view('dashboard.store.edit', compact('store', 'hariList', 'jamOperasional'));
    }

    public function update(Request $request, Store $store)
    {
        $this->authorize('update', $store);

        $validated = $request->validate([
            'alamat' => 'required|string|max:255',
            'nomor_wa' => 'required|string|max:20',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'has_table_number' => 'nullable|boolean',
            'status_aktif' => 'nullable|boolean',
            'jam' => 'nullable|array',
            'jam.*.libur' => 'nullable|boolean',
            'jam.*.buka' => 'nullable|string',
            'jam.*.tutup' => 'nullable|string',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'foto_background' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $hariList = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        $jamOperasional = [];
        foreach ($hariList as $hari) {
            $libur = $request->boolean("jam.$hari.libur");
            $jamOperasional[$hari] = $libur ? ['libur' => true] : [
                'libur' => false, 'buka' => $request->input("jam.$hari.buka", '08:00'), 'tutup' => $request->input("jam.$hari.tutup", '22:00'),
            ];
        }

        // Handle Foto Profil
        if ($request->hasFile('foto_profil')) {
            if ($store->foto_profil && Storage::disk('public')->exists($store->foto_profil)) {
                Storage::disk('public')->delete($store->foto_profil);
            }
            $store->foto_profil = $request->file('foto_profil')->store('stores/profil', 'public');
        }

        // Handle Foto Background
        if ($request->hasFile('foto_background')) {
            if ($store->foto_background && Storage::disk('public')->exists($store->foto_background)) {
                Storage::disk('public')->delete($store->foto_background);
            }
            $store->foto_background = $request->file('foto_background')->store('stores/background', 'public');
        }

        $store->update([
            'alamat' => $validated['alamat'],
            'nomor_wa' => Store::normalisasiNomorWa($validated['nomor_wa']),
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'has_table_number' => $request->boolean('has_table_number'),
            'status_aktif' => $request->boolean('status_aktif'),
            'jam_operasional' => $jamOperasional,
            'foto_profil' => $store->foto_profil,
            'foto_background' => $store->foto_background,
        ]);

        return redirect()->route('dashboard')->with('success', 'Info toko berhasil diperbarui.');
    }

    public function preview(Store $store)
    {
        $this->authorize('update', $store);
        $store->load([
            'products' => fn ($query) => $query->tersedia(),
            'reviews' => fn ($query) => $query->with('user')->latest()
        ]);
        return view('store.show', [
            'store' => $store,
            'isPreview' => true,
        ]);
    }
}