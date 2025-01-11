'use client';

import { useState, useEffect } from 'react';
import { useAuth } from '@/context/AuthContext';
import { getUserAddresses, deleteAddress } from '@/lib/firebase/services';
import { useRouter } from 'next/navigation';
import Link from 'next/link';

export default function AddressListPage() {
  const { user } = useAuth();
  const router = useRouter();
  const [addresses, setAddresses] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!user) {
      router.push('/login');
      return;
    }

    const loadAddresses = async () => {
      try {
        const userAddresses = await getUserAddresses(user.uid);
        setAddresses(userAddresses);
      } catch (error) {
        console.error('Error loading addresses:', error);
      } finally {
        setLoading(false);
      }
    };

    loadAddresses();
  }, [user, router]);

  const handleDelete = async (addressId: string) => {
    if (window.confirm('Yakin ingin menghapus alamat ini?')) {
      try {
        await deleteAddress(user!.uid, addressId);
        setAddresses(addresses.filter(addr => addr.id !== addressId));
      } catch (error) {
        console.error('Error deleting address:', error);
      }
    }
  };

  if (loading) return <div>Loading...</div>;

  return (
    <div className="min-h-screen bg-gray-50 pt-16">
      <div className="max-w-2xl mx-auto px-4 py-8">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-2xl font-bold text-gray-900">Alamat Pengiriman</h1>
          <Link
            href="/address/add"
            className="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700"
          >
            Tambah Alamat
          </Link>
        </div>

        <div className="space-y-4">
          {addresses.length === 0 ? (
            <div className="text-center py-12 bg-white rounded-lg">
              <p className="text-gray-500">Belum ada alamat tersimpan</p>
            </div>
          ) : (
            addresses.map((address) => (
              <div key={address.id} className="bg-white p-4 rounded-lg shadow-sm">
                <div className="flex justify-between items-start">
                  <div>
                    <h3 className="font-medium">{address.receiverName}</h3>
                    <p className="text-sm text-gray-600">{address.phone}</p>
                    <p className="text-sm text-gray-600 mt-2">
                      {address.address}, {address.subdistrict}, {address.district}
                    </p>
                  </div>
                  <button
                    onClick={() => handleDelete(address.id)}
                    className="text-red-500 hover:text-red-700"
                  >
                    Hapus
                  </button>
                </div>
              </div>
            ))
          )}
        </div>
      </div>
    </div>
  );
}