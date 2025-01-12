'use client';

import { useState, useEffect } from 'react';
import { useAuth } from '@/context/AuthContext';
import { getUserOrders } from '@/lib/firebase/services';
import { useRouter } from 'next/navigation';
import Link from 'next/link';

export default function OrderListPage() {
  const { user } = useAuth();
  const router = useRouter();
  const [orders, setOrders] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!user) {
      router.push('/login');
      return;
    }

    const loadOrders = async () => {
      try {
        const ordersList = await getUserOrders(user.uid);
        setOrders(ordersList);
      } catch (error) {
        console.error('Error loading orders:', error);
      } finally {
        setLoading(false);
      }
    };

    loadOrders();
  }, [user, router]);

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 pt-16">
        <div className="max-w-7xl mx-auto px-4 py-8">
          <div className="text-center">Loading...</div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 pt-16">
      <div className="max-w-7xl mx-auto px-4 py-8">
        <h1 className="text-2xl font-bold text-gray-900 mb-8">Daftar Pesanan</h1>

        {orders.length === 0 ? (
          <div className="text-center py-12 bg-white rounded-lg">
            <p className="text-gray-500 mb-4">Belum ada pesanan</p>
            <Link
              href="/products"
              className="text-indigo-600 hover:text-indigo-500"
            >
              Mulai Belanja
            </Link>
          </div>
        ) : (
          <div className="space-y-6">
            {orders.map((order) => (
              <div key={order.id} className="bg-white rounded-lg shadow-sm overflow-hidden">
                <div className="p-6">
                  {/* Order Header */}
                  <div className="flex justify-between items-start mb-4">
                    <div>
                      <span className="text-sm text-gray-500">Nomor Pesanan:</span>
                      <p className="font-mono">{order.id}</p>
                      <p className="text-sm text-gray-500 mt-1">
                        {new Date(order.createdAt.toDate()).toLocaleDateString('id-ID', {
                          day: 'numeric',
                          month: 'long',
                          year: 'numeric',
                          hour: '2-digit',
                          minute: '2-digit'
                        })}
                      </p>
                    </div>
                    <div className="flex flex-col items-end">
                      <span className={`px-3 py-1 rounded-full text-sm font-medium ${
                        order.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                        order.status === 'processing' ? 'bg-blue-100 text-blue-800' :
                        order.status === 'shipped' ? 'bg-indigo-100 text-indigo-800' :
                        order.status === 'delivered' ? 'bg-green-100 text-green-800' :
                        'bg-red-100 text-red-800'
                      }`}>
                        {order.status === 'pending' ? 'Menunggu Pembayaran' :
                         order.status === 'processing' ? 'Diproses' :
                         order.status === 'shipped' ? 'Dikirim' :
                         order.status === 'delivered' ? 'Selesai' :
                         'Dibatalkan'}
                      </span>
                      <span className="text-sm text-gray-500 mt-1">
                        {order.paymentMethod === 'transfer' ? 'Transfer Bank' :
                         order.paymentMethod === 'cod' ? 'Bayar di Tempat' :
                         'E-Wallet'}
                      </span>
                    </div>
                  </div>

                  {/* Order Items */}
                  <div className="border-t border-b border-gray-200 py-4 mb-4">
                    {order.items.map((item: any) => (
                      <div key={item.productId} className="flex justify-between py-2">
                        <span className="text-gray-600">
                          {item.name} ({item.quantity}x)
                        </span>
                        <span className="font-medium">
                          Rp {(item.price * item.quantity).toLocaleString('id-ID')}
                        </span>
                      </div>
                    ))}
                  </div>

                  {/* Order Total */}
                  <div className="flex justify-between items-center">
                    <div>
                      <p className="text-sm text-gray-500">Total Pembayaran</p>
                      <p className="text-lg font-bold">
                        Rp {order.totalAmount.toLocaleString('id-ID')}
                      </p>
                    </div>
                    <Link
                      href={`/orders/${order.id}`}
                      className="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700"
                    >
                      Detail Pesanan
                    </Link>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}