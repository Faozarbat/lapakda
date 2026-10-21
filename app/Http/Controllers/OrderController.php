<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Store;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function checkout(Request $request, Store $store)
    {
        // Cegah Seller checkout di toko sendiri
        if (auth()->check() && auth()->id() === $store->owner_id) {
            return redirect()
                ->route('store.show', [$store->kecamatan->slug, $store->slug])
                ->with('error', 'Anda tidak dapat melakukan checkout di toko Anda sendiri.');
        }

        $cart = $this->ambilCartAtauNull($request, $store);

        if (!$store->isOpenNow()) {
            return redirect()
                ->route('cart.show', $store->id)
                ->with('error', 'Maaf, toko sedang tutup. Silakan pesan kembali pada jam operasional.');
        }

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()
                ->route('store.show', [$store->kecamatan->slug, $store->slug])
                ->with('error', 'Keranjang kamu kosong, tambahkan produk dulu.');
        }

        if ($redirect = $this->cekProdukMasihTersedia($cart, $store)) {
            return $redirect;
        }

        return view('checkout.show', [
            'store' => $store,
            'cart' => $cart,
            'idempotencyKey' => (string) Str::uuid(),
            'savedAddress' => auth()->check() ? auth()->user() : null,
        ]);
    }

    public function store(Request $request, Store $store)
    {
        // Cegah Seller checkout di toko sendiri
        if (auth()->check() && auth()->id() === $store->owner_id) {
            return redirect()
                ->route('store.show', [$store->kecamatan->slug, $store->slug])
                ->with('error', 'Anda tidak dapat melakukan checkout di toko Anda sendiri.');
        }

        $validated = $request->validate([
            'idempotency_key' => 'required|string',
            'fulfillment_type' => 'required|in:self_pickup,delivery',
            'dine_in_qty' => 'required_if:fulfillment_type,self_pickup|array',
            'dine_in_qty.*' => 'integer|min:0',
            'table_number' => 'nullable|string|max:50',
            'delivery_latitude' => 'required_if:fulfillment_type,delivery|nullable|numeric',
            'delivery_longitude' => 'required_if:fulfillment_type,delivery|nullable|numeric',
            'delivery_note' => 'nullable|string|max:255',
        ]);

        $cacheKey = 'checkout_idempotency:' . $validated['idempotency_key'];

        // Kalau token ini SUDAH pernah diproses (submit ganda dari form yang sama),
        // langsung redirect ke hasil yang sudah pernah dibuat sebelumnya —
        // TANPA membuat Order baru lagi.
        $hasilSebelumnya = Cache::get($cacheKey);
        if ($hasilSebelumnya) {
            return redirect()->away($hasilSebelumnya);
        }

        $cart = $this->ambilCartAtauNull($request, $store);

        if (!$store->isOpenNow()) {
            return redirect()
                ->route('cart.show', $store->id)
                ->with('error', 'Maaf, toko sedang tutup. Silakan pesan kembali pada jam operasional.');
        }

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()
                ->route('store.show', [$store->kecamatan->slug, $store->slug])
                ->with('error', 'Keranjang kamu kosong, tambahkan produk dulu.');
        }

        if ($redirect = $this->cekProdukMasihTersedia($cart, $store)) {
            return $redirect;
        }

        $cartItems = array_values($cart->items->all());
        $adaDineIn = false;

        // Jika kategori BUKAN resto, paksa tidak ada dine-in (semua takeaway/pickup)
        if ($store->kategori === 'resto' && $validated['fulfillment_type'] === 'self_pickup') {
            foreach ($cartItems as $index => $item) {
                $dineInQty = max(0, min((int) ($validated['dine_in_qty'][$index] ?? 0), $item->qty));
                if ($dineInQty > 0) {
                    $adaDineIn = true;
                }
            }
        }

        $wajibIsiMeja = $store->has_table_number && $adaDineIn;

        if ($wajibIsiMeja && empty($validated['table_number'])) {
            return back()->withErrors(['table_number' => 'Nomor meja wajib diisi.'])->withInput();
        }

        $order = Order::create([
            'store_id' => $store->id,
            'user_id' => auth()->check() ? auth()->id() : null, // <--- FIX: Simpan user_id jika login
            'session_id' => $request->session()->getId(),
            'nomor_order' => 'TEMP-' . Str::random(10),
            'fulfillment_type' => $validated['fulfillment_type'],
            'delivery_latitude' => $validated['delivery_latitude'] ?? null,
            'delivery_longitude' => $validated['delivery_longitude'] ?? null,
            'delivery_note' => $validated['delivery_note'] ?? null,
            'status' => 'menunggu_konfirmasi',
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
            'platform_fee' => 0,
        ]);

        $order->update([
            'nomor_order' => 'LPK-' . str_pad((string) $order->id, 4, '0', STR_PAD_LEFT),
        ]);

        // SIMPAN ALAMAT TERAKHIR (Jika login dan memilih delivery)
        if (auth()->check() && $validated['fulfillment_type'] === 'delivery') {
            auth()->user()->update([
                'last_latitude' => $validated['delivery_latitude'],
                'last_longitude' => $validated['delivery_longitude'],
                'last_delivery_note' => $validated['delivery_note'],
            ]);
        }

        foreach ($cartItems as $index => $item) {
            if ($validated['fulfillment_type'] === 'delivery') {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'nama_produk_saat_beli' => $item->product->nama,
                    'harga_saat_beli' => $item->product->harga,
                    'qty' => $item->qty,
                    'consumption_type' => null,
                    'table_number' => null,
                ]);

                continue;
            }

            // Jika bukan resto, paksa DineInQty = 0
            $dineInQty = ($store->kategori === 'resto') ? max(0, min((int) ($validated['dine_in_qty'][$index] ?? 0), $item->qty)) : 0;
            $takeawayQty = $item->qty - $dineInQty;

            if ($dineInQty > 0) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'nama_produk_saat_beli' => $item->product->nama,
                    'harga_saat_beli' => $item->product->harga,
                    'qty' => $dineInQty,
                    'consumption_type' => 'dine_in',
                    'table_number' => $wajibIsiMeja ? $validated['table_number'] : null,
                ]);
            }

            if ($takeawayQty > 0) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'nama_produk_saat_beli' => $item->product->nama,
                    'harga_saat_beli' => $item->product->harga,
                    'qty' => $takeawayQty,
                    'consumption_type' => 'takeaway',
                    'table_number' => null,
                ]);
            }
        }

        $order->load('items');
        
        // --- TAMBAHKAN KODE NOTIFIKASI DI SINI ---
        // Kirim Push Notification ke HP milik Seller
        if ($store->owner) {
            $store->owner->notify(new NewOrderNotification($order, $store));
        }
        // ----------------------------------------

        $pesanWA = $this->susunRingkasanWA($order, $store);
        $waUrl = 'https://wa.me/' . $store->nomor_wa . '?text=' . urlencode($pesanWA);

        // Simpan hasil selama 1 jam — kalau token ini muncul lagi (submit ganda),
        // redirect ke URL WA yang SAMA tanpa bikin Order baru.
        Cache::put($cacheKey, $waUrl, now()->addHour());

        $cart->items()->delete();
        $cart->delete();

        return redirect()->away($waUrl);
    }

    public function riwayat(Request $request)
    {
        $myOrders = collect();
        $incomingOrders = collect();

        if (auth()->check()) {
            // Ambil riwayat belanja user (sebagai pembeli)
            // Cek berdasarkan user_id ATAU session_id (jaga-jaga jika pesanan dibuat sebelum login)
            $myOrders = Order::where(function($query) use ($request) {
                    $query->where('user_id', auth()->id())
                          ->orWhere('session_id', $request->session()->getId());
                })
                ->with(['store', 'items', 'review'])
                ->latest()
                ->get();
                
            // Jika user punya toko, ambil pesanan masuk (sebagai penjual)
            if (auth()->user()->stores->isNotEmpty()) {
                $storeIds = auth()->user()->stores->pluck('id');
                $incomingOrders = Order::whereIn('store_id', $storeIds)
                    ->with(['items', 'store', 'user'])
                    ->latest()
                    ->get();
            }
        } else {
            // Jika guest, cari berdasarkan session_id
            $myOrders = Order::where('session_id', $request->session()->getId())
                ->with(['store', 'items', 'review'])
                ->latest()
                ->get();
        }

        return view('order.riwayat', compact('myOrders', 'incomingOrders'));
    }

    private function ambilCartAtauNull(Request $request, Store $store): ?Cart
    {
        return Cart::where('session_id', $request->session()->getId())
            ->where('store_id', $store->id)
            ->with('items.product')
            ->first();
    }

    private function cekProdukMasihTersedia(Cart $cart, Store $store): ?RedirectResponse
    {
        $tidakTersedia = $cart->items->filter(fn ($item) => ! $item->product->status_tersedia);

        if ($tidakTersedia->isEmpty()) {
            return null;
        }

        $namaProduk = $tidakTersedia->pluck('product.nama')->implode(', ');

        return redirect()
            ->route('cart.show', $store->id)
            ->with('error', "Produk berikut sudah tidak tersedia, silakan hapus dari keranjang: {$namaProduk}");
    }

    private function susunRingkasanWA(Order $order, Store $store): string
    {
        $baris = [];
        $baris[] = "Halo {$store->nama}, saya mau pesan (Order #{$order->nomor_order}):";
        $baris[] = '';

        $total = 0;
        foreach ($order->items as $item) {
            $subtotal = $item->harga_saat_beli * $item->qty;
            $total += $subtotal;

            $keterangan = match ($item->consumption_type) {
                'dine_in' => ' (Makan di tempat)',
                'takeaway' => ' (Bungkus)',
                default => '',
            };

            $baris[] = "- {$item->nama_produk_saat_beli} x{$item->qty} = Rp"
                . number_format($subtotal, 0, ',', '.') . $keterangan;
        }

        $baris[] = '';
        $baris[] = 'Total: Rp' . number_format($total, 0, ',', '.');
        $baris[] = '';

        if ($order->fulfillment_type === 'self_pickup') {
            $baris[] = 'Metode: Ambil Sendiri';
            $nomorMeja = $order->items->first(fn ($i) => $i->table_number)?->table_number;
            if ($nomorMeja) {
                $baris[] = "Nomor meja: {$nomorMeja}";
            }
        } else {
            $baris[] = 'Metode: Diantar';
            $baris[] = "Lokasi: https://www.google.com/maps?q={$order->delivery_latitude},{$order->delivery_longitude}";
            if ($order->delivery_note) {
                $baris[] = "Catatan: {$order->delivery_note}";
            }
        }

        return implode("\n", $baris);
    }
}