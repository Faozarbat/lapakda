// User Profile
export interface UserProfile {
    uid: string;
    email: string;
    displayName?: string;
    phoneNumber?: string;
    address?: {
      street: string;
      city: string;
      district: string; // Kecamatan
      postalCode: string;
    };
    role: 'buyer' | 'seller';
    createdAt: Date;
  }
  
  // Product
  export interface Product {
    id: string;
    sellerId: string;
    name: string;
    description: string;
    price: number;
    stock: number;
    category: string;
    images: string[];
    condition: 'new' | 'used';
    weight: number; // dalam gram
    status: 'active' | 'inactive';
    createdAt: Date;
    updatedAt: Date;
  }
  
  // Cart Item
  export interface CartItem {
    id: string;
    userId: string;
    productId: string;
    quantity: number;
    addedAt: Date;
  }
  
  // Order
  export interface Order {
    id: string;
    userId: string;
    items: {
      productId: string;
      quantity: number;
      price: number;
    }[];
    totalAmount: number;
    shippingAddress: {
      street: string;
      city: string;
      district: string;
      postalCode: string;
    };
    status: 'pending' | 'processing' | 'shipped' | 'delivered' | 'cancelled';
    paymentStatus: 'pending' | 'paid' | 'failed';
    createdAt: Date;
    updatedAt: Date;
  }