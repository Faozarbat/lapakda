<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function create()
    {
        $kecamatans = Kecamatan::orderBy('nama')->get();

        return view('store.create', compact('kecamatans'));
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
        ]);

        $slug = Str::slug($validated['nama']);

        $sudahAda = Store::where('kecamatan_id', $validated['kecamatan_id'])
            ->where('slug', $slug)
            ->exists();

        if ($sudahAda) {
            return back()
                ->withErrors(['nama' => 'Nama toko ini sudah dipakai di kecamatan yang sama. Coba nama lain.'])
                ->withInput();
        }

        $store = Store::create([
            'owner_id' => auth()->id(),
            'kecamatan_id' => $validated['kecamatan_id'],
            'nama' => $validated['nama'],
            'slug' => $slug,
            'kategori' => $validated['kategori'],
            'nomor_wa' => Store::normalisasiNomorWa($validated['nomor_wa']),
            'alamat' => $validated['alamat'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'has_table_number' => false,
            'status_aktif' => true,
        ]);

        return redirect()
            ->route('store.show', [$store->kecamatan->slug, $store->slug])
            ->with('success', 'Toko berhasil didaftarkan!');
    }

    /**
     * Form edit info toko — melengkapi field yang sengaja dilewati saat
     * registrasi awal (jam operasional, nomor meja) + field yang mungkin
     * berubah seiring waktu (alamat, nomor WA, lokasi, status aktif).
     */
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
        ]);

        $hariList = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        $jamOperasional = [];

        foreach ($hariList as $hari) {
            $libur = $request->boolean("jam.$hari.libur");
            $jamOperasional[$hari] = $libur
                ? ['libur' => true]
                : [
                    'libur' => false,
                    'buka' => $request->input("jam.$hari.buka", '08:00'),
                    'tutup' => $request->input("jam.$hari.tutup", '22:00'),
                ];
        }

        $store->update([
            'alamat' => $validated['alamat'],
            'nomor_wa' => Store::normalisasiNomorWa($validated['nomor_wa']),
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'has_table_number' => $request->boolean('has_table_number'),
            'status_aktif' => $request->boolean('status_aktif'),
            'jam_operasional' => $jamOperasional,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Info toko berhasil diperbarui.');
    }

    public function preview(Store $store)
    {
        // Pastikan yang buka preview adalah pemilik toko
        $this->authorize('update', $store);

        // Load data persis seperti di PublicStoreController
        $store->load([
            'products' => fn ($query) => $query->tersedia(),
            'reviews' => fn ($query) => $query->with('user')->latest()
        ]);

        // Kirim flag isPreview = true ke view
        return view('store.show', [
            'store' => $store,
            'isPreview' => true, // <--- INI KUNCINYA
        ]);
    }
}