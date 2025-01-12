'use client';

import { useState, useEffect } from 'react';
import { useAuth } from '@/context/AuthContext';
import { getOrder } from '@/lib/firebase/services';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import React from 'react';

export default function OrderDetailPage({ params }: { params: { id: string } }) {
  const orderId = React.use(params).id;
  const { user } = useAuth();
  const router = useRouter();
  const [order, setOrder] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!user) {
      router.push('/login');
      return;
    }

    const loadOrder = async () => {
      try {
        const orderData = await getOrder(orderId);
        if (!orderData || orderData.userId !== user.uid) {
          router.push('/orders');
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
        {/* Header */}
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-2xl font-bold text-gray-900">Detail Pesanan</h1>
          <Link
            href="/orders"
            className="text-indigo-600 hover:text-indigo-500"
          >
            Kembali ke Daftar Pesanan
          </Link>
        </div>

        <div className="bg-white rounded-lg shadow-sm overflow-hidden">
          {/* Order Status */}
          <div className="p-6 border-b">
            <div className="flex justify-between items-start">
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
            </div>
          </div>

          {/* Shipping Info */}
          <div className="p-6 border-b">
            <h2 className="font-medium mb-4">Informasi Pengiriman</h2>
            <div className="text-sm text-gray-600">
              <p className="font-medium">{order.shippingAddress.receiverName}</p>
              <p>{order.shippingAddress.phone}</p>
              <p className="mt-1">
                {order.shippingAddress.address}, {order.shippingAddress.subdistrict}, {order.shippingAddress.district}
              </p>
            </div>
          </div>

          {/* Items */}
          <div className="p-6 border-b">
            <h2 className="font-medium mb-4">Produk</h2>
            <div className="space-y-4">
              {order.items.map((item: any) => (
                <div key={item.productId} className="flex justify-between">
                  <div>
                    <p className="text-gray-900">{item.name}</p>
                    <p className="text-sm text-gray-500">{item.quantity}x @ Rp {item.price.toLocaleString('id-ID')}</p>
                  </div>
                  <p className="font-medium">
                    Rp {(item.price * item.quantity).toLocaleString('id-ID')}
                  </p>
                </div>
              ))}
            </div>
          </div>

          {/* Payment Info */}
          <div className="p-6">
            <h2 className="font-medium mb-4">Informasi Pembayaran</h2>
            <div className="space-y-2">
              <div className="flex justify-between text-sm">
                <span className="text-gray-500">Metode Pembayaran</span>
                <span>{order.paymentMethod === 'transfer' ? 'Transfer Bank' :
                       order.paymentMethod === 'cod' ? 'Bayar di Tempat' :
                       'E-Wallet'}</span>
              </div>
              <div className="flex justify-between text-sm">
                <span className="text-gray-500">Subtotal</span>
                <span>Rp {order.subtotal.toLocaleString('id-ID')}</span>
              </div>
              <div className="flex justify-between text-sm">
                <span className="text-gray-500">Ongkos Kirim ({order.shippingMethod.name})</span>
                <span>Rp {order.shippingCost.toLocaleString('id-ID')}</span>
              </div>
              <div className="flex justify-between font-medium text-lg border-t pt-2 mt-2">
                <span>Total</span>
                <span>Rp {order.totalAmount.toLocaleString('id-ID')}</span>
              </div>
            </div>
          </div>

          {/* Payment Instructions for pending orders */}
          {order.status === 'pending' && order.paymentMethod === 'transfer' && (
            <div className="p-6 bg-gray-50 border-t">
              <h2 className="font-medium mb-4">Instruksi Pembayaran</h2>
              <ol className="list-decimal list-inside text-sm text-gray-600 space-y-2">
                <li>Transfer sejumlah Rp {order.totalAmount.toLocaleString('id-ID')}</li>
                <li>Ke rekening BCA: 1234567890 a.n. Lapakda</li>
                <li>Konfirmasi pembayaran melalui WhatsApp ke 081234567890</li>
                <li>Sertakan nomor pesanan saat konfirmasi</li>
              </ol>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}