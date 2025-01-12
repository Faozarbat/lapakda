export const CATEGORIES = [
    { id: 'electronics', name: 'Elektronik & Gadget' },
    { id: 'computer', name: 'Komputer & Laptop' },
    { id: 'phone', name: 'Handphone & Aksesoris' },
    { id: 'fashion', name: 'Fashion & Pakaian' },
    { id: 'beauty', name: 'Kesehatan & Kecantikan' },
    { id: 'food', name: 'Makanan & Minuman' },
    { id: 'home', name: 'Rumah Tangga' },
    { id: 'sport', name: 'Olahraga' },
    { id: 'automotive', name: 'Otomotif' },
    { id: 'toys', name: 'Mainan & Hobi' },
    { id: 'books', name: 'Buku & Alat Tulis' },
    { id: 'baby', name: 'Perlengkapan Bayi' },
    { id: 'jewelry', name: 'Jam & Perhiasan' },
    { id: 'property', name: 'Properti' },
    { id: 'office', name: 'Peralatan Kantor' },
    { id: 'other', name: 'Lainnya' }
  ] as const;
  
  // Helper function untuk mendapatkan nama kategori berdasarkan id
  export const getCategoryName = (categoryId: string): string => {
    const category = CATEGORIES.find(cat => cat.id === categoryId);
    return category ? category.name : 'Kategori Tidak Ditemukan';
  };