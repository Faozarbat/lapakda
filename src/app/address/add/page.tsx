'use client';

import { useState } from 'react';
import { useAuth } from '@/context/AuthContext';
import { addAddress } from '@/lib/firebase/services';
import { useRouter } from 'next/navigation';

// Data Kecamatan dan Kelurahan di Batam
const DISTRICTS = {
    'Batam Kota': [
      'Baloi Permai',
      'Belian',
      'Sukajadi',
      'Teluk Tering',
      'Taman Baloi'
    ],
    'Batu Ampar': [
      'Batu Merah',
      'Kampung Seraya', 
      'Sungai Jodoh',
      'Tanjung Sengkuang'
    ],
    'Bengkong': [
      'Bengkong Indah',
      'Bengkong Laut',
      'Sadai',
      'Tanjung Buntung'
    ],
    'Lubuk Baja': [
      'Batu Selicin',
      'Kampung Pelita',
      'Lubuk Baja Kota',
      'Tanjung Uma'
    ],
    'Nongsa': [
      'Batu Besar',
      'Kabil',
      'Ngenang',
      'Sambau'
    ],
    'Sagulung': [
      'Sagulung Kota',
      'Sungai Binti',
      'Sungai Langkai',
      'Sungai Lekop',
      'Tembesi'
    ],
    'Sei Beduk': [
      'Duriangkang',
      'Mangsang',
      'Muka Kuning',
      'Tanjung Piayu'
    ],
    'Sekupang': [
      'Patam Lestari',
      'Sungai Harapan',
      'Tanjung Pinggir',
      'Tanjung Riau',
      'Tiban Baru',
      'Tiban Lama'
    ],
    'Bulang': [
      'Bulang Lintang',
      'Pantai Gelam',
      'Pulau Buluh',
      'Setokok',
      'Temoyong'
    ],
    'Galang': [
      'Air Raja',
      'Galang Baru',
      'Karas',
      'Pulau Abang',
      'Rempang Cate',
      'Sembulang'
    ],
    'Belakang Padang': [
      'Kasu',
      'Pemping',
      'Pecong',
      'Pulau Terong', 
      'Tanjung Sari',
      'Sekanak Raya'
    ]
   };

export default function AddAddressPage() {
  const { user } = useAuth();
  const router = useRouter();
  const [loading, setLoading] = useState(false);
  const [selectedDistrict, setSelectedDistrict] = useState('');
  const [formData, setFormData] = useState({
    receiverName: '',
    phone: '',
    district: '',
    subdistrict: '',
    address: ''
  });

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!user) return;

    setLoading(true);
    try {
      await addAddress(user.uid, formData);
      router.push('/checkout');
    } catch (error) {
      console.error('Error adding address:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 pt-16">
      <div className="max-w-2xl mx-auto px-4 py-8">
        <h1 className="text-2xl font-bold text-gray-900 mb-8">Tambah Alamat Baru</h1>

        <form onSubmit={handleSubmit} className="space-y-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Nama Penerima
            </label>
            <input
              type="text"
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              value={formData.receiverName}
              onChange={(e) => setFormData({ ...formData, receiverName: e.target.value })}
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Nomor HP/WhatsApp
            </label>
            <input
              type="tel"
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              value={formData.phone}
              onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Kecamatan
            </label>
            <select
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              value={selectedDistrict}
              onChange={(e) => {
                setSelectedDistrict(e.target.value);
                setFormData({ ...formData, district: e.target.value, subdistrict: '' });
              }}
            >
              <option value="">Pilih Kecamatan</option>
              {Object.keys(DISTRICTS).map((district) => (
                <option key={district} value={district}>
                  {district}
                </option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Kelurahan
            </label>
            <select
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              value={formData.subdistrict}
              onChange={(e) => setFormData({ ...formData, subdistrict: e.target.value })}
              disabled={!selectedDistrict}
            >
              <option value="">Pilih Kelurahan</option>
              {selectedDistrict &&
                DISTRICTS[selectedDistrict as keyof typeof DISTRICTS].map((subdistrict) => (
                  <option key={subdistrict} value={subdistrict}>
                    {subdistrict}
                  </option>
                ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Detail Alamat
            </label>
            <textarea
              required
              rows={3}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              value={formData.address}
              onChange={(e) => setFormData({ ...formData, address: e.target.value })}
              placeholder="Nama jalan, nomor rumah, patokan"
            />
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
              className="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50"
            >
              {loading ? 'Menyimpan...' : 'Simpan Alamat'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}