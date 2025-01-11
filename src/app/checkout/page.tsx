'use client';

import { useState, useEffect } from 'react';
import { useAuth } from '@/context/AuthContext';
import { getCartItems, getUserAddresses, createOrder } from '@/lib/firebase/services';
import { useRouter } from 'next/navigation';
import Link from 'next/link';

const SHIPPING_METHODS = [
  { 
    id: 'kurirda', 
    name: 'Kurirda', 
    price: 10000, 
    description: 'Pengiriman dalam area Batam' 
  },
  { 
    id: 'meetup', 
    name: 'Cek di lokasi', 
    price: 0, 
    description: 'Saling Jumpa di lokasi kesepakatan' 
  }
];

const PAYMENT_METHODS = [
  { 
    id: 'cod', 
    name: 'Bayar di Tempat (COD)', 
    description: 'Bayar tunai saat pesanan tiba' 
  },
  { 
    id: 'transfer', 
    name: 'Transfer Bank', 
    description: 'BCA, BNI, Mandiri' 
  },
  { 
    id: 'ewallet', 
    name: 'E-Wallet', 
    description: 'GoPay, OVO, DANA' 
  },
];

export default function CheckoutPage() {
  const { user } = useAuth();
  const router = useRouter();
  const [cartItems, setCartItems] = useState<any[]>([]);
  const [addresses, setAddresses] = useState<any[]>([]);
  const [selectedAddress, setSelectedAddress] = useState('');
  const [selectedShipping, setSelectedShipping] = useState(SHIPPING_METHODS[0].id);
  const [selectedPayment, setSelectedPayment] = useState(PAYMENT_METHODS[0].id);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    if (!user) {
      router.push('/login');
      return;
    }

    const loadCheckoutData = async () => {
      try {
        // Load cart items
        const items = await getCartItems(user.uid);
        if (items.length === 0) {
          router.push('/cart');
          return;
        }
        setCartItems(items);

        // Load user addresses
        const addresses = await getUserAddresses(user.uid);
        if (addresses.length === 0) {
          router.push('/address/add');
          return;
        }
        setAddresses(addresses);
        setSelectedAddress(addresses[0].id);
      } catch (error) {
        console.error('Error loading checkout data:', error);
        setError('Gagal memuat data checkout');
      } finally {
        setLoading(false);
      }
    };

    loadCheckoutData();
  }, [user, router]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      if (!user) throw new Error('Silakan login terlebih dahulu');
      if (!selectedAddress) throw new Error('Pilih alamat pengiriman');

      const selectedAddr = addresses.find(addr => addr.id === selectedAddress);
      const shippingMethod = SHIPPING_METHODS.find(m => m.id === selectedShipping)!;
      const subtotal = cartItems.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);
      const shippingCost = shippingMethod.price;
      const total = subtotal + shippingCost;

      const orderData = {
        userId: user.uid,
        items: cartItems.map(item => ({
          productId: item.product.id,
          quantity: item.quantity,
          price: item.product.price
        })),
        shippingAddress: {
          receiverName: selectedAddr.receiverName,
          phone: selectedAddr.phone,
          address: selectedAddr.address,
          district: selectedAddr.district,
          subdistrict: selectedAddr.subdistrict
        },
        shippingMethod: {
          id: shippingMethod.id,
          name: shippingMethod.name,
          cost: shippingMethod.price
        },
        paymentMethod: selectedPayment,
        subtotal,
        shippingCost,
        totalAmount: total,
        status: 'pending',
        paymentStatus: 'pending'
      };

      const orderId = await createOrder(orderData);
      router.push(`/orders/${orderId}`);
    } catch (error: any) {
      console.error('Error creating order:', error);
      setError(error.message || 'Gagal membuat pesanan');
    } finally {
      setLoading(false);
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

  const subtotal = cartItems.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);
  const shippingCost = SHIPPING_METHODS.find(m => m.id === selectedShipping)?.price || 0;
  const total = subtotal + shippingCost;

  return (
    <div className="min-h-screen bg-gray-50 pt-16">
      <div className="max-w-7xl mx-auto px-4 py-8">
        <h1 className="text-2xl font-bold text-gray-900 mb-8">Checkout</h1>

        {error && (
          <div className="mb-6 bg-red-50 text-red-700 p-4 rounded-lg">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div className="lg:col-span-2 space-y-6">
            {/* Alamat Pengiriman */}
            <div className="bg-white p-6 rounded-lg shadow-sm">
              <div className="flex justify-between items-center mb-4">
                <h2 className="text-lg font-medium text-gray-900">Alamat Pengiriman</h2>
                <Link
                  href="/address/add"
                  className="text-indigo-600 hover:text-indigo-500 text-sm"
                >
                  Buat Alamat Baru
                </Link>
              </div>
              
              <div className="space-y-4">
                {addresses.map((address) => (
                  <label
                    key={address.id}
                    className={`block p-4 border rounded-lg cursor-pointer ${
                      selectedAddress === address.id
                        ? 'border-indigo-500 bg-indigo-50'
                        : 'border-gray-200 hover:border-indigo-200'
                    }`}
                  >
                    <div className="flex items-center space-x-3">
                      <input
                        type="radio"
                        name="address"
                        value={address.id}
                        checked={selectedAddress === address.id}
                        onChange={() => setSelectedAddress(address.id)}
                        className="h-4 w-4 text-indigo-600"
                      />
                      <div className="flex-1">
                        <p className="font-medium">{address.receiverName}</p>
                        <p className="text-sm text-gray-500">{address.phone}</p>
                        <p className="text-sm text-gray-500 mt-1">
                          {address.address}, {address.subdistrict}, {address.district}
                        </p>
                      </div>
                    </div>
                  </label>
                ))}
              </div>
            </div>

            {/* Metode Pengiriman */}
            <div className="bg-white p-6 rounded-lg shadow-sm">
              <h2 className="text-lg font-medium text-gray-900 mb-4">Metode Pengiriman</h2>
              <div className="space-y-4">
                {SHIPPING_METHODS.map((method) => (
                  <label
                    key={method.id}
                    className={`flex items-center justify-between p-4 border rounded-lg cursor-pointer ${
                      selectedShipping === method.id
                        ? 'border-indigo-500 bg-indigo-50'
                        : 'border-gray-200 hover:border-indigo-200'
                    }`}
                  >
                    <div className="flex items-center">
                      <input
                        type="radio"
                        name="shipping"
                        value={method.id}
                        checked={selectedShipping === method.id}
                        onChange={(e) => setSelectedShipping(e.target.value)}
                        className="h-4 w-4 text-indigo-600"
                      />
                      <div className="ml-3">
                        <span className="block font-medium text-gray-900">
                          {method.name}
                        </span>
                        <span className="block text-sm text-gray-500">
                          {method.description}
                        </span>
                      </div>
                    </div>
                    <span className="font-medium">
                      {method.price > 0 
                        ? `Rp ${method.price.toLocaleString('id-ID')}`
                        : 'Gratis'
                      }
                    </span>
                  </label>
                ))}
              </div>
            </div>

            {/* Metode Pembayaran */}
            <div className="bg-white p-6 rounded-lg shadow-sm">
              <h2 className="text-lg font-medium text-gray-900 mb-4">Metode Pembayaran</h2>
              <div className="space-y-4">
                {PAYMENT_METHODS.map((method) => (
                  <label
                    key={method.id}
                    className={`flex items-center p-4 border rounded-lg cursor-pointer ${
                      selectedPayment === method.id
                        ? 'border-indigo-500 bg-indigo-50'
                        : 'border-gray-200 hover:border-indigo-200'
                    }`}
                  >
                    <input
                      type="radio"
                      name="payment"
                      value={method.id}
                      checked={selectedPayment === method.id}
                      onChange={(e) => setSelectedPayment(e.target.value)}
                      className="h-4 w-4 text-indigo-600"
                    />
                    <div className="ml-3">
                      <span className="block font-medium text-gray-900">
                        {method.name}
                      </span>
                      <span className="block text-sm text-gray-500">
                        {method.description}
                      </span>
                    </div>
                  </label>
                ))}
              </div>
            </div>
          </div>

          {/* Ringkasan Pesanan */}
          <div className="lg:col-span-1">
            <div className="bg-white p-6 rounded-lg shadow-sm sticky top-20">
              <h2 className="text-lg font-medium text-gray-900 mb-4">Ringkasan Pesanan</h2>
              
              {/* Daftar Produk */}
              <div className="space-y-4 mb-4">
                {cartItems.map((item) => (
                  <div key={item.id} className="flex justify-between text-sm">
                    <span className="text-gray-500">
                      {item.product.name} ({item.quantity}x)
                    </span>
                    <span className="font-medium">
                      Rp {(item.product.price * item.quantity).toLocaleString('id-ID')}
                    </span>
                  </div>
                ))}
              </div>

              {/* Total */}
              <div className="border-t pt-4 space-y-2">
                <div className="flex justify-between text-sm">
                  <span className="text-gray-500">Subtotal</span>
                  <span className="font-medium">
                    Rp {subtotal.toLocaleString('id-ID')}
                  </span>
                </div>
                <div className="flex justify-between text-sm">
                  <span className="text-gray-500">Ongkos Kirim</span>
                  <span className="font-medium">
                    Rp {shippingCost.toLocaleString('id-ID')}
                  </span>
                </div>
                <div className="flex justify-between text-base font-bold border-t pt-2">
                  <span>Total</span>
                  <span>Rp {total.toLocaleString('id-ID')}</span>
                </div>
              </div>

              <button
                type="submit"
                disabled={loading}
                className={`w-full mt-6 py-3 px-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                  ${loading ? 'opacity-75 cursor-not-allowed' : ''}`}
              >
                {loading ? 'Memproses...' : 'Buat Pesanan'}
              </button>

              <Link
                href="/cart"
                className="block text-center mt-4 text-sm text-gray-500 hover:text-gray-700"
              >
                Kembali ke Keranjang
              </Link>
            </div>
          </div>
        </form>
      </div>
    </div>
  );
}