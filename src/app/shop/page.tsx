'use client';

import { useState, useEffect } from 'react';
import { useAuth } from '@/context/AuthContext';
import { getUserProfile, getProductsByUser } from '@/lib/firebase/services';
import { useRouter } from 'next/navigation';
import Link from 'next/link';

export default function ShopProfilePage() {
  const { user } = useAuth();
  const router = useRouter();
  const [profile, setProfile] = useState<any>(null);
  const [products, setProducts] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [stats, setStats] = useState({
    totalProducts: 0,
    activeProducts: 0,
    totalSales: 0
  });

  useEffect(() => {
    if (!user) {
      router.push('/login');
      return;
    }

    const loadShopData = async () => {
      try {
        // Load user profile
        const userProfile = await getUserProfile(user.uid);
        setProfile(userProfile);

        // Load user's products
        const userProducts = await getProductsByUser(user.uid);
        setProducts(userProducts);

        // Calculate stats
        const activeProducts = userProducts.filter(p => p.status === 'active');
        setStats({
          totalProducts: userProducts.length,
          activeProducts: activeProducts.length,
          totalSales: 0 // Will be implemented later
        });
      } catch (error) {
        console.error('Error loading shop data:', error);
      } finally {
        setLoading(false);
      }
    };

    loadShopData();
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
        {/* Shop Profile Section */}
        <div className="bg-white rounded-lg shadow-sm p-6 mb-8">
          <div className="flex justify-between items-start">
            <div>
              <h1 className="text-2xl font-bold text-gray-900">
                {profile?.shopName || 'Toko Saya'}
              </h1>
              <p className="text-gray-600 mt-1">
                {profile?.shopDescription || 'Belum ada deskripsi toko'}
              </p>
              <div className="mt-4 flex gap-4 text-sm text-gray-500">
                <div>
                  <span className="font-medium">{stats.totalProducts}</span> Produk
                </div>
                <div>
                  <span className="font-medium">{stats.totalSales}</span> Penjualan
                </div>
              </div>
            </div>
            <Link
              href="/shop/edit"
              className="text-indigo-600 hover:text-indigo-500 text-sm"
            >
              Edit Profil Toko
            </Link>
          </div>
        </div>

        {/* Action Buttons */}
        <div className="flex justify-between items-center mb-6">
          <h2 className="text-xl font-bold text-gray-900">Produk Saya</h2>
          <Link
            href="/products/add"
            className="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700"
          >
            Tambah Produk
          </Link>
        </div>

        {/* Products Grid */}
        {products.length === 0 ? (
          <div className="text-center py-12 bg-white rounded-lg">
            <p className="text-gray-500 mb-4">Belum ada produk</p>
            <Link
              href="/products/add"
              className="text-indigo-600 hover:text-indigo-500"
            >
              Mulai tambah produk
            </Link>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {products.map((product) => (
              <div
                key={product.id}
                className="bg-white rounded-lg shadow-sm overflow-hidden"
              >
                <img
                  src={product.images[0] || '/images/GambarKosong.png'}
                  alt={product.name}
                  className="w-full h-48 object-cover"
                />
                <div className="p-4">
                  <h3 className="font-medium text-gray-900">{product.name}</h3>
                  <p className="mt-1 text-gray-500 line-clamp-2">
                    {product.description}
                  </p>
                  <div className="mt-4 flex justify-between items-center">
                    <span className="font-bold text-gray-900">
                      Rp {product.price.toLocaleString('id-ID')}
                    </span>
                    <span className={`text-sm ${
                      product.status === 'active'
                        ? 'text-green-600'
                        : 'text-red-600'
                    }`}>
                      {product.status === 'active' ? 'Aktif' : 'Nonaktif'}
                    </span>
                  </div>
                  <div className="mt-4 flex justify-between">
                    <Link
                      href={`/products/edit/${product.id}`}
                      className="text-indigo-600 hover:text-indigo-500 text-sm"
                    >
                      Edit
                    </Link>
                    <button
                      onClick={() => {/* Toggle status */}}
                      className="text-sm text-gray-600 hover:text-gray-800"
                    >
                      {product.status === 'active' ? 'Nonaktifkan' : 'Aktifkan'}
                    </button>
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