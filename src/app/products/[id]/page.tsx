'use client';

import { useState, useEffect } from 'react';
import { useAuth } from '@/context/AuthContext';
import { getProduct, addToCart } from '@/lib/firebase/services';
import { Product } from '@/types';
import { useRouter } from 'next/navigation';
import React from 'react';

export default function ProductDetailPage({ params }: { params: { id: string } }) {
  const productId = React.use(params).id;
  const [product, setProduct] = useState<Product | null>(null);
  const [loading, setLoading] = useState(true);
  const [currentImage, setCurrentImage] = useState(0);
  const [quantity, setQuantity] = useState(1);
  const [error, setError] = useState('');
  const { user } = useAuth();
  const router = useRouter();

  useEffect(() => {
    const loadProduct = async () => {
      try {
        const data = await getProduct(productId);
        setProduct(data);
      } catch (error) {
        console.error('Error loading product:', error);
        setError('Gagal memuat produk');
      } finally {
        setLoading(false);
      }
    };

    loadProduct();
  }, [productId]);

  const handleAddToCart = async () => {
    if (!user) {
      router.push('/login');
      return;
    }

    try {
      await addToCart(user.uid, productId, quantity);
      router.push('/cart');
    } catch (error) {
      console.error('Error adding to cart:', error);
      setError('Gagal menambahkan ke keranjang');
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

  if (!product) {
    return (
      <div className="min-h-screen bg-gray-50 pt-16">
        <div className="max-w-7xl mx-auto px-4 py-8">
          <div className="text-center">Produk tidak ditemukan</div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 pt-16">
      {error && (
        <div className="max-w-7xl mx-auto px-4 py-2">
          <div className="bg-red-50 text-red-700 p-4 rounded-lg">{error}</div>
        </div>
      )}
      <div className="max-w-7xl mx-auto px-4 py-8">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
          {/* Image Gallery */}
          <div className="space-y-4">
            {/* Main Image */}
            <div className="w-full h-[400px] overflow-hidden rounded-lg">
              <img
                src={product.images[currentImage] || '/images/GambarKosong.png'}
                alt={product.name}
                className="w-full h-full object-cover object-center"
              />
            </div>

            {/* Thumbnail Images */}
            <div className="flex gap-2 overflow-x-auto pb-2">
              {product.images.map((image, index) => (
                <button
                  key={index}
                  onClick={() => setCurrentImage(index)}
                  className={`flex-shrink-0 w-24 h-24 overflow-hidden rounded-lg ${
                    currentImage === index 
                      ? 'ring-2 ring-indigo-500' 
                      : 'ring-1 ring-gray-200'
                  }`}
                >
                  <img
                    src={image}
                    alt={`${product.name} ${index + 1}`}
                    className="w-full h-full object-cover object-center"
                  />
                </button>
              ))}
            </div>
          </div>

          {/* Product Info */}
          <div className="space-y-6">
            <h1 className="text-3xl font-bold text-gray-900">{product.name}</h1>
            
            <div className="flex items-center justify-between">
              <p className="text-2xl font-bold text-gray-900">
                Rp {product.price.toLocaleString('id-ID')}
              </p>
              <span className={`px-3 py-1 rounded-full text-sm ${
                product.condition === 'new' 
                  ? 'bg-green-100 text-green-800' 
                  : 'bg-yellow-100 text-yellow-800'
              }`}>
                {product.condition === 'new' ? 'Baru' : 'Bekas'}
              </span>
            </div>

            <div className="border-t border-b border-gray-200 py-4">
              <div className="prose prose-sm text-gray-500">
                {product.description}
              </div>
            </div>

            <div className="grid grid-cols-2 gap-4 text-sm">
              <div className="flex items-center gap-2">
                <span className="text-gray-500">Stok:</span>
                <span className="font-medium text-gray-900">{product.stock}</span>
              </div>
              <div className="flex items-center gap-2">
                <span className="text-gray-500">Berat:</span>
                <span className="font-medium text-gray-900">{product.weight}g</span>
              </div>
            </div>

            <div className="space-y-4">
              <div className="flex items-center gap-4">
                <label className="text-sm font-medium text-gray-700">
                  Jumlah:
                </label>
                <div className="flex items-center border border-gray-300 rounded-lg">
                  <button
                    onClick={() => setQuantity(Math.max(1, quantity - 1))}
                    className="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-l-lg"
                  >
                    -
                  </button>
                  <input
                    type="number"
                    min="1"
                    max={product.stock}
                    value={quantity}
                    onChange={(e) => {
                      const val = parseInt(e.target.value);
                      if (!isNaN(val)) {
                        setQuantity(Math.min(Math.max(1, val), product.stock));
                      }
                    }}
                    className="w-16 text-center border-x border-gray-300 py-1 focus:outline-none"
                  />
                  <button
                    onClick={() => setQuantity(Math.min(product.stock, quantity + 1))}
                    className="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-r-lg"
                  >
                    +
                  </button>
                </div>
              </div>

              <div className="flex gap-4">
                <button
                  onClick={handleAddToCart}
                  className="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors"
                >
                  Tambah ke Keranjang
                </button>
                <button
                  onClick={() => {
                    if (!user) {
                      router.push('/login');
                      return;
                    }
                    // Handle buy now
                  }}
                  className="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors"
                >
                  Beli Sekarang
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}