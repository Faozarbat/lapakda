'use client';

import { useState, useEffect } from 'react';
import { useAuth } from '@/context/AuthContext';
import { getProduct, updateProduct } from '@/lib/firebase/services';
import { useRouter } from 'next/navigation';
import React from 'react';

const CATEGORIES = [
  'Elektronik',
  'Fashion',
  'Makanan',
  'Minuman',
  'Kesehatan',
  'Kecantikan',
  'Rumah Tangga',
  'Olahraga',
  'Otomotif',
  'Lainnya'
];

export default function EditProductPage({ params }: { params: { id: string } }) {
  const productId = React.use(params).id;
  const { user } = useAuth();
  const router = useRouter();
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [images, setImages] = useState<File[]>([]);
  const [imageUrls, setImageUrls] = useState<string[]>([]);
  const [existingImages, setExistingImages] = useState<string[]>([]);

  const [formData, setFormData] = useState({
    name: '',
    description: '',
    price: '',
    stock: '',
    category: '',
    condition: 'new',
    weight: ''
  });

  useEffect(() => {
    if (!user) {
      router.push('/login');
      return;
    }

    const loadProduct = async () => {
      try {
        const product = await getProduct(productId);
        if (!product) {
          router.push('/products');
          return;
        }

        // Verify if current user is the seller
        if (product.sellerId !== user.uid) {
          router.push('/products');
          return;
        }

        setFormData({
          name: product.name,
          description: product.description,
          price: product.price.toString(),
          stock: product.stock.toString(),
          category: product.category,
          condition: product.condition,
          weight: product.weight.toString()
        });

        setExistingImages(product.images);
      } catch (error) {
        console.error('Error loading product:', error);
        setError('Gagal memuat produk');
      } finally {
        setLoading(false);
      }
    };

    loadProduct();
  }, [productId, user, router]);

  const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files) {
      const newImages = Array.from(e.target.files);
      
      // Validasi total gambar (existing + new)
      if (existingImages.length + images.length + newImages.length > 5) {
        setError('Maksimal 5 gambar');
        return;
      }

      setImages(prev => [...prev, ...newImages]);
      
      // Create preview URLs
      const urls = newImages.map(file => URL.createObjectURL(file));
      setImageUrls(prev => [...prev, ...urls]);
    }
  };

  const handleRemoveImage = (index: number, isExisting: boolean) => {
    if (isExisting) {
      setExistingImages(prev => prev.filter((_, i) => i !== index));
    } else {
      setImages(prev => prev.filter((_, i) => i !== index));
      setImageUrls(prev => {
        URL.revokeObjectURL(prev[index]);
        return prev.filter((_, i) => i !== index);
      });
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      if (existingImages.length + images.length === 0) {
        throw new Error('Tambahkan minimal 1 foto produk');
      }

      // Upload new images if any
      // Untuk sementara kita skip upload gambar
      const newImageUrls = ['https://via.placeholder.com/400x400'];

      const productData = {
        name: formData.name,
        description: formData.description,
        price: Number(formData.price),
        stock: Number(formData.stock),
        category: formData.category,
        condition: formData.condition as 'new' | 'used',
        weight: Number(formData.weight),
        images: [...existingImages, ...newImageUrls],
        updatedAt: new Date()
      };

      await updateProduct(productId, productData);
      router.push('/products');
    } catch (err: any) {
      setError(err.message || 'Gagal mengupdate produk');
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 pt-16">
        <div className="max-w-2xl mx-auto px-4 py-8">
          <div className="text-center">Loading...</div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 pt-16">
      <div className="max-w-2xl mx-auto px-4 py-8">
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-2xl font-bold text-gray-900">Edit Produk</h1>
        </div>

        {error && (
          <div className="mb-6 bg-red-50 text-red-700 p-4 rounded-lg">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-6 bg-white p-6 rounded-lg shadow-sm">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Nama Produk
            </label>
            <input
              type="text"
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              value={formData.name}
              onChange={(e) => setFormData({ ...formData, name: e.target.value })}
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Deskripsi
            </label>
            <textarea
              required
              rows={4}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              value={formData.description}
              onChange={(e) => setFormData({ ...formData, description: e.target.value })}
            />
          </div>

          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Harga (Rp)
              </label>
              <input
                type="number"
                required
                min="0"
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                value={formData.price}
                onChange={(e) => setFormData({ ...formData, price: e.target.value })}
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Stok
              </label>
              <input
                type="number"
                required
                min="0"
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                value={formData.stock}
                onChange={(e) => setFormData({ ...formData, stock: e.target.value })}
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Kategori
            </label>
            <select
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              value={formData.category}
              onChange={(e) => setFormData({ ...formData, category: e.target.value })}
            >
              <option value="">Pilih Kategori</option>
              {CATEGORIES.map((category) => (
                <option key={category} value={category}>
                  {category}
                </option>
              ))}
            </select>
          </div>

          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Kondisi
              </label>
              <select
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                value={formData.condition}
                onChange={(e) => setFormData({ ...formData, condition: e.target.value })}
              >
                <option value="new">Baru</option>
                <option value="used">Bekas</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Berat (gram)
              </label>
              <input
                type="number"
                required
                min="0"
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                value={formData.weight}
                onChange={(e) => setFormData({ ...formData, weight: e.target.value })}
              />
            </div>
          </div>

          {/* Image section */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Foto Produk (maksimal 5 foto)
            </label>
            <div className="flex flex-wrap gap-4">
              {/* Existing Images */}
              {existingImages.map((url, index) => (
                <div key={`existing-${index}`} className="relative w-24 h-24">
                  <img
                    src={url}
                    alt={`Existing ${index + 1}`}
                    className="w-full h-full object-cover rounded-lg"
                  />
                  <button
                    type="button"
                    onClick={() => handleRemoveImage(index, true)}
                    className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center"
                  >
                    ×
                  </button>
                </div>
              ))}

              {/* New Images */}
              {imageUrls.map((url, index) => (
                <div key={`new-${index}`} className="relative w-24 h-24">
                  <img
                    src={url}
                    alt={`New ${index + 1}`}
                    className="w-full h-full object-cover rounded-lg"
                  />
                  <button
                    type="button"
                    onClick={() => handleRemoveImage(index, false)}
                    className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center"
                  >
                    ×
                  </button>
                </div>
              ))}

              {/* Add Image Button */}
              {existingImages.length + images.length < 5 && (
                <label className="cursor-pointer">
                  <div className="w-24 h-24 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                    <span className="text-4xl text-gray-400">+</span>
                  </div>
                  <input
                    type="file"
                    className="hidden"
                    accept="image/*"
                    multiple
                    onChange={handleImageChange}
                  />
                </label>
              )}
            </div>
            {existingImages.length + images.length === 0 && (
              <p className="text-sm text-red-500 mt-1">
                *Tambahkan minimal 1 foto produk
              </p>
            )}
          </div>

          <div className="flex gap-4">
            <button
              type="button"
              onClick={() => router.back()}
              className="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              Batal
            </button>
            <button
              type="submit"
              disabled={loading}
              className={`flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50`}
            >
              {loading ? 'Menyimpan...' : 'Simpan Perubahan'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}