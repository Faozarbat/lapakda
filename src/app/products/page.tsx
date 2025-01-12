'use client';

import { useState, useEffect } from 'react';
import { getProducts } from '@/lib/firebase/services';
import { Product } from '@/types';
import Link from 'next/link';
import { useAuth } from '@/context/AuthContext';
import { DISTRICTS } from '@/constants/districts';
import { CATEGORIES, getCategoryName } from '@/constants/categories';

export default function ProductsPage() {
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('');
  const [products, setProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState(true);
  const { user } = useAuth();

  // Filter states
  const [selectedDistrict, setSelectedDistrict] = useState('');
  const [selectedSubdistrict, setSelectedSubdistrict] = useState('');

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

  // Filter products based on location
  const filteredProducts = products.filter(product => {
    // Filter by search
    if (searchQuery && !product.name.toLowerCase().includes(searchQuery.toLowerCase())) {
      return false;
    }
    // Filter by category
    if (selectedCategory && product.category !== selectedCategory) {
      return false;
    }
    // Filter by location
    if (selectedDistrict && product.district !== selectedDistrict) return false;
    if (selectedSubdistrict && product.subdistrict !== selectedSubdistrict) return false;
    return true;
  });

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
        {/* Header */}
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

        {/* Filter Section */}

        <div className="bg-white p-4 rounded-lg shadow-sm mb-6">
  {/* Search */}
  <div className="mb-4">
    <input
      type="text"
      placeholder="Cari produk..."
      className="w-full px-3 py-2 border border-gray-300 rounded-lg"
      value={searchQuery}
      onChange={(e) => setSearchQuery(e.target.value)}
    />
  </div>

  <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
    {/* Kategori */}
    <div>
      <label className="block text-sm font-medium text-gray-700 mb-1">
        Filter Kategori
      </label>
      <select
        className="w-full px-3 py-2 border border-gray-300 rounded-lg"
        value={selectedCategory}
        onChange={(e) => setSelectedCategory(e.target.value)}
      >
        <option value="">Semua Kategori</option>
        {CATEGORIES.map((category) => (
          <option key={category.id} value={category.id}>
            {category.name}
          </option>
        ))}
      </select>
    </div>

    {/* Kecamatan dan Kelurahan yang sudah ada tetap sama */}
  </div>
</div>
        <div className="bg-white p-4 rounded-lg shadow-sm mb-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Filter Kecamatan
              </label>
              <select
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                value={selectedDistrict}
                onChange={(e) => {
                  setSelectedDistrict(e.target.value);
                  setSelectedSubdistrict('');
                }}
              >
                <option value="">Semua Kecamatan</option>
                {Object.keys(DISTRICTS).map((district) => (
                  <option key={district} value={district}>
                    {district}
                  </option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Filter Kelurahan
              </label>
              <select
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                value={selectedSubdistrict}
                onChange={(e) => setSelectedSubdistrict(e.target.value)}
                disabled={!selectedDistrict}
              >
                <option value="">Semua Kelurahan</option>
                {selectedDistrict &&
                  DISTRICTS[selectedDistrict as keyof typeof DISTRICTS].map((subdistrict) => (
                    <option key={subdistrict} value={subdistrict}>
                      {subdistrict}
                    </option>
                  ))}
              </select>
            </div>
          </div>
        </div>

        {/* Products Grid */}
        {filteredProducts.length === 0 ? (
          <div className="text-center py-12">
            <p className="text-gray-500">
              {products.length === 0 
                ? "Belum ada produk yang tersedia"
                : "Tidak ada produk di lokasi ini"}
            </p>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {filteredProducts.map((product) => (
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
                  <div className="mt-2 text-sm text-gray-500">
                    Lokasi: {product.district}, {product.subdistrict}
                  </div>
                  <div className="mt-4 flex justify-between items-center">
                    <span className="text-sm text-gray-500">
                      {product.condition === 'new' ? 'Baru' : 'Bekas'}
                    </span>
                    <span className="text-sm text-gray-500">
                      {getCategoryName(product.category)}
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