<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductDashboardController extends Controller
{
    public function index(Store $store)
    {
        $this->authorize('view', $store);

        $products = $store->products()->latest()->get();

        return view('dashboard.produk.index', compact('store', 'products'));
    }

    public function create(Store $store)
    {
        $this->authorize('create', [Product::class, $store]);

        return view('dashboard.produk.create', compact('store'));
    }

    public function store(Request $request, Store $store)
    {
        $this->authorize('create', [Product::class, $store]);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string|max:1000',
            'kategori_produk' => 'nullable|string|max:100',
            'foto' => 'nullable|image|max:5120', // maksimal 5MB file mentah sebelum dikompres
        ]);

        $namaFoto = $request->hasFile('foto')
            ? $this->prosesDanSimpanFoto($request->file('foto'))
            : null;

        $store->products()->create([
            'nama' => $validated['nama'],
            'harga' => $validated['harga'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'kategori_produk' => $validated['kategori_produk'] ?? null,
            'foto' => $namaFoto,
            'status_tersedia' => true,
        ]);

        return redirect()
            ->route('dashboard.produk.index', $store->id)
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Store $store, Product $product)
    {
        $this->authorize('update', $product);

        return view('dashboard.produk.edit', compact('store', 'product'));
    }

    public function update(Request $request, Store $store, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string|max:1000',
            'kategori_produk' => 'nullable|string|max:100',
            'foto' => 'nullable|image|max:5120',
            'status_tersedia' => 'nullable|boolean',
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama dulu supaya tidak jadi file sampah (orphaned) di storage
            if ($product->foto) {
                Storage::disk('public')->delete($product->foto);
            }
            $validated['foto'] = $this->prosesDanSimpanFoto($request->file('foto'));
        }

        $validated['status_tersedia'] = $request->boolean('status_tersedia');

        $product->update($validated);

        return redirect()
            ->route('dashboard.produk.index', $store->id)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Store $store, Product $product)
    {
        $this->authorize('delete', $product);

        if ($product->foto) {
            Storage::disk('public')->delete($product->foto);
        }

        $product->delete();

        return redirect()
            ->route('dashboard.produk.index', $store->id)
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Resize ke maksimal 800x800, convert ke WebP (kualitas 80), simpan dengan
     * nama UUID (bukan nama asli dari HP user) — sesuai checklist keamanan
     * di PRODUCT_SPEC.md bagian 12.1.
     */
        private function prosesDanSimpanFoto($file): string
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        $image->scaleDown(width: 800, height: 800);
        $image->pad(800, 800, 'ffffff');

        $namaFile = 'produk/' . Str::uuid() . '.webp';

        Storage::disk('public')->put(
            $namaFile,
            (string) $image->toWebp(80)
        );

        return $namaFile;
    }
}