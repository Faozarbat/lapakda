'use client';

import { useState, useEffect } from 'react';
import { useAuth } from '@/context/AuthContext';
import { getOrder } from '@/lib/firebase/services';
import { useRouter, useSearchParams } from 'next/navigation';
import Link from 'next/link';
import { CheckCircle } from 'lucide-react';

export default function OrderSuccessPage() {
  const { user } = useAuth();
  const router = useRouter();
  const searchParams = useSearchParams();
  const orderId = searchParams.get('id'); // Ubah bagian ini
  const [order, setOrder] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!user) {
      router.push('/login');
      return;
    }

    if (!orderId) {
      router.push('/');
      return;
    }

    const loadOrder = async () => {
      try {
        const orderData = await getOrder(orderId);
        if (!orderData || orderData.userId !== user.uid) {
          router.push('/');
          return;
        }
        setOrder(orderData);
      } catch (error) {
        console.error('Error loading order:', error);
      } finally {
        setLoading(false);
      }
    };

    loadOrder();
  }, [user, orderId, router]);

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 pt-16">
        <div className="max-w-2xl mx-auto px-4 py-8">
          <div className="text-center">Loading...</div>
        </div>
      </div>
    );
  }

  if (!order) return null;

  return (
    <div className="min-h-screen bg-gray-50 pt-16">
      <div className="max-w-2xl mx-auto px-4 py-8">
        <div className="bg-white rounded-lg shadow-sm p-6">
          {/* Success Icon */}
          <div className="text-center mb-6">
            <CheckCircle className="w-16 h-16 text-green-500 mx-auto mb-4" />
            <h1 className="text-2xl font-bold text-gray-900">
              Pesanan Berhasil Dibuat!
            </h1>
            <p className="text-gray-600 mt-2">
              Terima kasih telah berbelanja di Lapakda
            </p>
          </div>

          {/* Order Details */}
          <div className="space-y-6">
            {/* Order ID */}
            <div className="bg-gray-50 p-4 rounded-lg">
              <p className="text-sm text-gray-600 mb-1">Nomor Pesanan:</p>
              <p className="font-mono text-lg">{order.id}</p>
            </div>

            {/* Payment Instructions */}
            {order.paymentMethod === 'transfer' && (
              <div className="bg-gray-50 p-4 rounded-lg">
                <h3 className="font-medium mb-2">Instruksi Pembayaran:</h3>
                <ol className="list-decimal list-inside text-sm text-gray-600 space-y-2">
                  <li>Transfer sejumlah Rp {order.totalAmount.toLocaleString('id-ID')}</li>
                  <li>Ke rekening BCA: 1234567890 a.n. Lapakda</li>
                  <li>Konfirmasi pembayaran melalui WhatsApp ke 081234567890</li>
                  <li>Sertakan nomor pesanan saat konfirmasi</li>
                </ol>
              </div>
            )}

            {order.paymentMethod === 'cod' && (
              <div className="bg-gray-50 p-4 rounded-lg">
                <h3 className="font-medium mb-2">Instruksi:</h3>
                <p className="text-sm text-gray-600">
                  Mohon siapkan uang tunai saat pesanan tiba sejumlah Rp {order.totalAmount.toLocaleString('id-ID')} 
                  
                </p>
              </div>
            )}

            {/* Shipping Address */}
            <div>
              <h3 className="font-medium mb-2">Alamat Pengiriman:</h3>
              <div className="text-sm text-gray-600">
                <p className="font-medium">{order.shippingAddress.receiverName}</p>
                <p>{order.shippingAddress.phone}</p>
                <p className="mt-1">
                  {order.shippingAddress.address}, {order.shippingAddress.subdistrict}, {order.shippingAddress.district}
                </p>
              </div>
            </div>

            {/* Order Summary */}
            <div>
              <h3 className="font-medium mb-2">Ringkasan Pesanan:</h3>
              <div className="space-y-4">
                {order.items.map((item: any) => (
                  <div key={item.productId} className="flex justify-between text-sm">
                    <span className="text-gray-600">
                      {item.name} ({item.quantity}x)
                    </span>
                    <span className="font-medium">
                      Rp {(item.price * item.quantity).toLocaleString('id-ID')}
                    </span>
                  </div>
                ))}
                <div className="border-t pt-4 space-y-2">
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Subtotal</span>
                    <span>Rp {order.subtotal.toLocaleString('id-ID')}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Ongkos Kirim</span>
                    <span>Rp {order.shippingCost.toLocaleString('id-ID')}</span>
                  </div>
                  <div className="flex justify-between font-medium text-lg border-t pt-2">
                    <span>Total</span>
                    <span>Rp {order.totalAmount.toLocaleString('id-ID')}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Action Buttons */}
          <div className="mt-8 space-y-4">
            <Link
              href={`/orders/${order.id}`}
              className="block w-full bg-indigo-600 text-white text-center py-2 rounded-lg hover:bg-indigo-700"
            >
              Lihat Detail Pesanan
            </Link>
            
            <Link
              href="/orders"
              className="block w-full bg-white text-indigo-600 text-center py-2 rounded-lg border border-indigo-600 hover:bg-indigo-50"
            >
              Lihat Semua Pesanan
            </Link>

            <Link
              href="/"
              className="block text-gray-500 hover:text-gray-700 text-center"
            >
              Kembali ke Beranda
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}