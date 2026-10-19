<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Tambah produk ke cart (atau tambah qty kalau produk sudah ada di cart).
     * Dipanggil via fetch() dari tombol "Tambah ke Keranjang" — tanpa reload halaman.
     */
    public function add(Request $request, Product $product)
    {
        $sessionId = $request->session()->getId();

        // firstOrCreate memastikan 1 cart per kombinasi session+toko (sesuai unique
        // constraint di migrasi) — kalau sudah ada cart untuk toko ini, dipakai ulang.
        $cart = Cart::firstOrCreate([
            'session_id' => $sessionId,
            'store_id' => $product->store_id,
        ]);

        $request->validate(['qty' => 'nullable|integer|min:0|max:99']);
        $qty = $request->input('qty', 1);

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            if ($qty <= 0) {
                $item->delete();
            } else {
                $item->update(['qty' => $qty]);
            }
        } elseif ($qty > 0) {
            $cart->items()->create([
                'product_id' => $product->id,
                'qty' => $qty,
            ]);
        }

        return response()->json([
            'success' => true,
            'cart_count' => (int) $cart->items()->sum('qty'),
        ]);
    }

    /**
     * Tampilkan SEMUA isi cart (Multi-Toko) untuk sesi ini.
     */
    public function index(Request $request)
    {
        $carts = Cart::where('session_id', $request->session()->getId())
            ->with(['items.product', 'store.kecamatan'])
            ->get();

        return view('cart.index', compact('carts'));
    }
    /**
     * Tampilkan isi cart untuk satu toko tertentu.
     */
    public function show(Request $request, Store $store)
    {
        $sessionId = $request->session()->getId();

        $cart = Cart::where('session_id', $sessionId)
            ->where('store_id', $store->id)
            ->with('items.product')
            ->first();

        return view('cart.show', [
            'store' => $store,
            'cart' => $cart,
        ]);
    }

    public function updateQty(Request $request, CartItem $cartItem)
    {
        $request->validate(['qty' => 'required|integer|min:1|max:99']);

        $this->pastikanMilikSesiIni($request, $cartItem);

        $cartItem->update(['qty' => $request->integer('qty')]);

        return response()->json(['success' => true]);
    }

    public function remove(Request $request, CartItem $cartItem)
    {
        $this->pastikanMilikSesiIni($request, $cartItem);

        $cartItem->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Guest cart tidak punya user login, jadi authorization-nya BUKAN lewat Policy
     * (yang butuh konsep "user"), melainkan mencocokkan session_id cart dengan
     * session yang sedang aktif di request ini. Mencegah orang iseng menebak ID
     * cart_item milik sesi/orang lain lalu mengubah/menghapusnya.
     */
    private function pastikanMilikSesiIni(Request $request, CartItem $cartItem): void
    {
        abort_unless(
            $cartItem->cart->session_id === $request->session()->getId(),
            403,
            'Cart ini bukan milik sesi kamu.'
        );
    }
}