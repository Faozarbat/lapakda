'use client';

import { useState, useEffect } from 'react';
import { getProducts } from '@/lib/firebase/services';
import { Product } from '@/types';
import Link from 'next/link';
import { useAuth } from '@/context/AuthContext';

export default function ProductsPage() {
  const [products, setProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState(true);
  const { user } = useAuth();

  useEffect(() => {
    const loadProducts = async () => {
      try {
        const data = await getProducts();
        setProducts(data);
      } catch (error) {
        console.error('Error loading products:', error);
      } finally {
        setLoading(false);
      }
    };

    loadProducts();
  }, []);

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 pt-16">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <div className="text-center">Loading...</div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 pt-16">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-2xl font-bold text-gray-900">Semua Produk</h1>
          {user && (
            <Link
              href="/products/add"
              className="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
            >
              Tambah Produk
            </Link>
          )}
        </div>

        {products.length === 0 ? (
          <div className="text-center py-12">
            <p className="text-gray-500">Belum ada produk yang tersedia</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {products.map((product) => (
              <Link 
                href={`/products/${product.id}`} 
                key={product.id}
                className="block bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-all duration-200"
              >
                <div className="aspect-w-1 aspect-h-1 w-full">
                  <img
                    src={product.images[0] || '/images/GambarKosong.png'}
                    alt={product.name}
                    className="w-full h-48 object-cover"
                  />
                </div>
                
                <div className="p-4">
                  <h3 className="text-lg font-medium text-gray-900 line-clamp-1">
                    {product.name}
                  </h3>
                  <p className="mt-1 text-gray-500 text-sm line-clamp-2">
                    {product.description}
                  </p>
                  <div className="mt-2 flex items-center justify-between">
                    <span className="text-lg font-bold text-gray-900">
                      Rp {product.price.toLocaleString('id-ID')}
                    </span>
                    <span className="text-sm text-gray-500">
                      Stok: {product.stock}
                    </span>
                  </div>
                  <div className="mt-4 flex justify-between items-center">
                    <span className="text-sm text-gray-500">
                      {product.condition === 'new' ? 'Baru' : 'Bekas'}
                    </span>
                    <button
                      onClick={(e) => {
                        e.preventDefault();
                        // Handle beli action
                      }}
                      className="bg-indigo-600 text-white px-4 py-2 text-sm rounded-lg hover:bg-indigo-700 transition-colors"
                    >
                      Beli
                    </button>
                  </div>
                </div>
              </Link>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}