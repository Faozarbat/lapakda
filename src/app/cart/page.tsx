'use client';

import { useState, useEffect } from 'react';
import { useAuth } from '@/context/AuthContext';
import { getCartItems, updateCartItemQuantity, removeFromCart } from '@/lib/firebase/services';
import { CartItem, Product } from '@/types';
import { useRouter } from 'next/navigation';
import Link from 'next/link';

export default function CartPage() {
  const [cartItems, setCartItems] = useState<(CartItem & { product: Product })[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const { user } = useAuth();
  const router = useRouter();

  useEffect(() => {
    if (!user) {
      router.push('/login');
      return;
    }

    const loadCart = async () => {
      try {
        const items = await getCartItems(user.uid);
        setCartItems(items);
      } catch (error) {
        console.error('Error loading cart:', error);
        setError('Gagal memuat keranjang');
      } finally {
        setLoading(false);
      }
    };

    loadCart();
  }, [user, router]);

  const handleUpdateQuantity = async (cartItemId: string, newQuantity: number) => {
    try {
      await updateCartItemQuantity(cartItemId, newQuantity);
      // Refresh cart items
      const updatedItems = await getCartItems(user!.uid);
      setCartItems(updatedItems);
    } catch (error) {
      console.error('Error updating quantity:', error);
      setError('Gagal mengupdate jumlah');
    }
  };

  const handleRemoveItem = async (cartItemId: string) => {
    try {
      await removeFromCart(cartItemId);
      // Refresh cart items
      const updatedItems = await getCartItems(user!.uid);
      setCartItems(updatedItems);
    } catch (error) {
      console.error('Error removing item:', error);
      setError('Gagal menghapus item');
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 pt-16">
        <div className="max-w-7xl mx-auto px-4 py-8">
          <div className="text-center">Loading...</div>
        </div>
      </div>
    );
  }

  const totalPrice = cartItems.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);

  return (
    <div className="min-h-screen bg-gray-50 pt-16">
      <div className="max-w-7xl mx-auto px-4 py-8">
        <h1 className="text-2xl font-bold text-gray-900 mb-8">Keranjang Belanja</h1>

        {error && (
          <div className="mb-6 bg-red-50 text-red-700 p-4 rounded-lg">
            {error}
          </div>
        )}

        {cartItems.length === 0 ? (
          <div className="text-center py-12">
            <p className="text-gray-500 mb-4">Keranjang belanja Anda kosong</p>
            <Link
              href="/products"
              className="text-indigo-600 hover:text-indigo-500"
            >
              Mulai Belanja
            </Link>
          </div>
        ) : (
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div className="lg:col-span-2">
              <div className="bg-white rounded-lg shadow-sm overflow-hidden">
                {cartItems.map((item) => (
                  <div key={item.id} className="p-4 border-b last:border-b-0">
                    <div className="flex gap-4">
                      <img
                        src={item.product.images[0] || '/images/GambarKosong.png'}
                        alt={item.product.name}
                        className="w-24 h-24 object-cover rounded-lg"
                      />
                      <div className="flex-1">
                        <h3 className="font-medium text-gray-900">
                          {item.product.name}
                        </h3>
                        <p className="text-sm text-gray-500 mt-1">
                          Rp {item.product.price.toLocaleString('id-ID')}
                        </p>
                        <div className="flex items-center mt-2">
                          <button 
                            onClick={() => handleUpdateQuantity(item.id, Math.max(1, item.quantity - 1))}
                            className="text-gray-500 hover:text-gray-700 px-2 py-1 border rounded-l-lg"
                          >
                            -
                          </button>
                          <input
                            type="number"
                            value={item.quantity}
                            onChange={(e) => {
                              const newQuantity = parseInt(e.target.value);
                              if (!isNaN(newQuantity) && newQuantity >= 1 && newQuantity <= item.product.stock) {
                                handleUpdateQuantity(item.id, newQuantity);
                              }
                            }}
                            className="w-16 text-center border-y py-1"
                            min="1"
                            max={item.product.stock}
                          />
                          <button 
                            onClick={() => handleUpdateQuantity(item.id, Math.min(item.product.stock, item.quantity + 1))}
                            className="text-gray-500 hover:text-gray-700 px-2 py-1 border rounded-r-lg"
                          >
                            +
                          </button>
                        </div>
                      </div>
                      <div className="flex flex-col justify-between">
                        <button 
                          onClick={() => handleRemoveItem(item.id)}
                          className="text-red-500 hover:text-red-700"
                        >
                          Hapus
                        </button>
                        <p className="text-right font-medium">
                          Rp {(item.product.price * item.quantity).toLocaleString('id-ID')}
                        </p>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
            
            <div className="bg-white rounded-lg shadow-sm p-4 h-fit">
              <h3 className="text-lg font-medium text-gray-900 mb-4">
                Ringkasan Belanja
              </h3>
              <div className="space-y-2 pb-4 border-b">
                <div className="flex justify-between text-sm">
                  <span className="text-gray-500">Total Harga ({cartItems.length} barang)</span>
                  <span className="font-medium">Rp {totalPrice.toLocaleString('id-ID')}</span>
                </div>
                <div className="flex justify-between text-sm">
                  <span className="text-gray-500">Total Ongkos Kirim</span>
                  <span className="font-medium">Rp 0</span>
                </div>
              </div>
              <div className="flex justify-between py-4">
                <span className="font-medium">Total Tagihan</span>
                <span className="font-bold">Rp {totalPrice.toLocaleString('id-ID')}</span>
              </div>
              <button 
                onClick={() => router.push('/checkout')}
                className="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition-colors"
              >
                Checkout
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}