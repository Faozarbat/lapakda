'use client';

import { useState } from 'react';
import { useAuth } from '@/context/AuthContext';
import { useRouter } from 'next/navigation';
import { addProduct } from '@/lib/firebase/services';

export default function AddProductPage() {
  const { user } = useAuth();
  const router = useRouter();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [images, setImages] = useState<File[]>([]);
  const [imageUrls, setImageUrls] = useState<string[]>([]);

  const [formData, setFormData] = useState({
    name: '',
    description: '',
    price: '',
    stock: '',
    category: 'other',
    condition: 'new',
    weight: ''
  });

  const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files) {
      const newImages = Array.from(e.target.files);
      setImages(prevImages => [...prevImages, ...newImages]);
      
      // Create preview URLs
      const urls = newImages.map(file => URL.createObjectURL(file));
      setImageUrls(prevUrls => [...prevUrls, ...urls]);
    }
  };

  const handleRemoveImage = (index: number) => {
    setImages(prevImages => prevImages.filter((_, i) => i !== index));
    setImageUrls(prevUrls => {
      URL.revokeObjectURL(prevUrls[index]); // Cleanup URL
      return prevUrls.filter((_, i) => i !== index);
    });
  };

  const uploadImage = async (file: File): Promise<string> => {
    const formData = new FormData();
    formData.append('file', file);

    const response = await fetch('/api/upload', {
      method: 'POST',
      body: formData
    });

    if (!response.ok) {
      throw new Error('Failed to upload image');
    }

    const data = await response.json();
    return data.secure_url;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError('');

    try {
      if (!user) throw new Error('Anda harus login terlebih dahulu');
      if (images.length === 0) throw new Error('Tambahkan minimal 1 foto produk');

      // Upload images
      const uploadPromises = images.map(file => uploadImage(file));
      const imageUrls = await Promise.all(uploadPromises);

      // Add product with image URLs
      const productData = {
        sellerId: user.uid,
        name: formData.name,
        description: formData.description,
        price: Number(formData.price),
        stock: Number(formData.stock),
        category: formData.category,
        condition: formData.condition as 'new' | 'used',
        weight: Number(formData.weight),
        images: imageUrls,
        status: 'active' as const,
        createdAt: new Date(),
        updatedAt: new Date()
      };

      await addProduct(productData);
      router.push('/products');
    } catch (err: any) {
      setError(err.message || 'Gagal menambahkan produk');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 pt-16">
      <div className="max-w-2xl mx-auto px-4 py-8">
        <h1 className="text-2xl font-bold text-gray-900 mb-6">Tambah Produk Baru</h1>

        {error && (
          <div className="bg-red-50 text-red-700 p-4 rounded-lg mb-6">
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
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              value={formData.category}
              onChange={(e) => setFormData({ ...formData, category: e.target.value })}
            >
              <option value="other">Pilih Kategori</option>
              <option value="electronics">Elektronik</option>
              <option value="fashion">Fashion</option>
              <option value="food">Makanan</option>
              <option value="hobby">Hobi</option>
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

          <div className="space-y-4">
            <label className="block text-sm font-medium text-gray-700">
              Foto Produk (maksimal 5 foto)
            </label>
            <div className="flex items-center space-x-4">
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
                  disabled={images.length >= 5}
                />
              </label>
              
              {imageUrls.map((url, index) => (
                <div key={index} className="relative w-24 h-24">
                  <img
                    src={url}
                    alt={`Preview ${index + 1}`}
                    className="w-24 h-24 object-cover rounded-lg"
                  />
                  <button
                    type="button"
                    onClick={() => handleRemoveImage(index)}
                    className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center"
                  >
                    ×
                  </button>
                </div>
              ))}
            </div>
            {images.length === 0 && (
              <p className="text-sm text-red-500">
                *Tambahkan minimal 1 foto produk
              </p>
            )}
          </div>

          <button
            type="submit"
            disabled={loading}
            className={`w-full py-2 px-4 border border-transparent rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
              ${loading ? 'opacity-75 cursor-not-allowed' : ''}`}
          >
            {loading ? 'Menyimpan...' : 'Simpan Produk'}
          </button>
        </form>
      </div>
    </div>
  );
}