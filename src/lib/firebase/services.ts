import {
  collection,
  doc,
  setDoc,
  getDoc,
  getDocs,
  query,
  where,
  addDoc,
  updateDoc,
  deleteDoc,
  Timestamp,
  orderBy,
  limit
} from 'firebase/firestore';
import { db } from '@/config/firebase';
import { UserProfile, Product, CartItem, Order } from '@/types';
import { uploadImage } from './storage';

// User Services
export const createUserProfile = async (userProfile: UserProfile) => {
  try {
    const userRef = doc(db, 'users', userProfile.uid);
    await setDoc(userRef, {
      ...userProfile,
      createdAt: Timestamp.now()
    });
  } catch (error) {
    console.error('Error creating user profile:', error);
    throw error;
  }
};

export const getUserProfile = async (uid: string) => {
  try {
    const userRef = doc(db, 'users', uid);
    const userSnap = await getDoc(userRef);
    
    if (userSnap.exists()) {
      const data = userSnap.data();
      return {
        ...data,
        uid: userSnap.id,
        createdAt: data.createdAt?.toDate() || new Date(),
        updatedAt: data.updatedAt?.toDate()
      };
    }
    
    // Return default profile if not found
    return {
      uid: uid,
      email: '',
      displayName: '',
      phoneNumber: '',
      photoURL: null,
      address: '',
      createdAt: new Date()
    };
  } catch (error) {
    console.error('Error getting user profile:', error);
    throw error;
  }
};

export const uploadProfileImage = async (uid: string, file: File): Promise<string> => {
  try {
    const path = `users/${uid}/profile.jpg`;
    return await uploadImage(file, path);
  } catch (error) {
    console.error('Error uploading profile image:', error);
    throw error;
  }
};

export const updateUserProfile = async (uid: string, data: Partial<UserProfile>) => {
  try {
    console.log('Starting updateUserProfile with data:', data); // tambahkan ini
    const userRef = doc(db, 'users', uid);
    const userSnap = await getDoc(userRef);
    
    const updateData = {
      ...data,
      updatedAt: Timestamp.now()
    };

    console.log('Final update data:', updateData); // tambahkan ini

    if (userSnap.exists()) {
      await updateDoc(userRef, updateData);
      console.log('Profile updated in Firestore'); // tambahkan ini
    } else {
      await setDoc(userRef, {
        ...updateData,
        createdAt: Timestamp.now()
      });
      console.log('New profile created in Firestore'); // tambahkan ini
    }
  } catch (error) {
    console.error('Error in updateUserProfile:', error); // tambahkan ini
    throw error;
  }
};


// Product Services
export const addProduct = async (product: Omit<Product, 'id'>) => {
  try {
    const productsRef = collection(db, 'products');
    const docRef = await addDoc(productsRef, {
      ...product,
      createdAt: Timestamp.now(),
      updatedAt: Timestamp.now()
    });
    return docRef;
  } catch (error) {
    console.error('Error adding product:', error);
    throw error;
  }
};

export const getProduct = async (productId: string) => {
  try {
    const productRef = doc(db, 'products', productId);
    const productSnap = await getDoc(productRef);
    return productSnap.exists() ? { id: productSnap.id, ...productSnap.data() } as Product : null;
  } catch (error) {
    console.error('Error getting product:', error);
    throw error;
  }
};

export const getProducts = async (category?: string) => {
  try {
    const productsRef = collection(db, 'products');
    const q = category 
      ? query(productsRef, where('category', '==', category), where('status', '==', 'active'))
      : query(productsRef, where('status', '==', 'active'));
    
    const querySnapshot = await getDocs(q);
    return querySnapshot.docs.map(doc => ({ id: doc.id, ...doc.data() })) as Product[];
  } catch (error) {
    console.error('Error getting products:', error);
    throw error;
  }
};

export const updateProduct = async (productId: string, data: Partial<Product>) => {
  try {
    const productRef = doc(db, 'products', productId);
    await updateDoc(productRef, {
      ...data,
      updatedAt: Timestamp.now()
    });
  } catch (error) {
    console.error('Error updating product:', error);
    throw error;
  }
};

export const deleteProduct = async (productId: string) => {
  try {
    const productRef = doc(db, 'products', productId);
    await updateDoc(productRef, {
      status: 'inactive',
      updatedAt: Timestamp.now()
    });
  } catch (error) {
    console.error('Error deleting product:', error);
    throw error;
  }
};

// Cart Services
export const addToCart = async (userId: string, productId: string, quantity: number) => {
  try {
    // Cek stock produk dulu
    const product = await getProduct(productId);
    if (!product) throw new Error('Produk tidak ditemukan');

    // Cek apakah produk sudah ada di cart
    const cartRef = collection(db, 'cart');
    const q = query(
      cartRef, 
      where('userId', '==', userId),
      where('productId', '==', productId)
    );
    
    const querySnapshot = await getDocs(q);
    
    if (!querySnapshot.empty) {
      // Update quantity jika produk sudah ada
      const cartItem = querySnapshot.docs[0];
      const currentQuantity = cartItem.data().quantity;
      const newQuantity = currentQuantity + quantity;

      // Validasi total quantity dengan stock
      if (newQuantity > product.stock) {
        throw new Error(`Jumlah melebihi stock yang tersedia (${product.stock})`);
      }

      await updateDoc(cartItem.ref, {
        quantity: newQuantity,
        updatedAt: Timestamp.now()
      });
      return cartItem.id;
    }

    // Tambah item baru jika belum ada
    if (quantity > product.stock) {
      throw new Error(`Jumlah melebihi stock yang tersedia (${product.stock})`);
    }

    const newCartItem = {
      userId,
      productId,
      quantity,
      createdAt: Timestamp.now(),
      updatedAt: Timestamp.now()
    };

    const docRef = await addDoc(cartRef, newCartItem);
    return docRef.id;
  } catch (error) {
    console.error('Error adding to cart:', error);
    throw error;
  }
};

export const getCartItems = async (userId: string) => {
  try {
    const cartRef = collection(db, 'cart');
    const q = query(cartRef, where('userId', '==', userId));
    const querySnapshot = await getDocs(q);

    const cartItems = [];
    for (const docItem of querySnapshot.docs) {
      const cartItem = { id: docItem.id, ...docItem.data() };
      // Ambil detail produk untuk setiap item
      const productRef = doc(db, 'products', cartItem.productId);
      const productDoc = await getDoc(productRef);
      if (productDoc.exists()) {
        cartItems.push({
          ...cartItem,
          product: { id: productDoc.id, ...productDoc.data() }
        });
      }
    }

    return cartItems;
  } catch (error) {
    console.error('Error getting cart items:', error);
    throw error;
  }
};

export const updateCartItemQuantity = async (cartItemId: string, quantity: number) => {
  try {
    const cartItemRef = doc(db, 'cart', cartItemId);
    await updateDoc(cartItemRef, {
      quantity,
      updatedAt: Timestamp.now()
    });
  } catch (error) {
    console.error('Error updating cart item:', error);
    throw error;
  }
};

export const removeFromCart = async (cartItemId: string) => {
  try {
    await deleteDoc(doc(db, 'cart', cartItemId));
  } catch (error) {
    console.error('Error removing from cart:', error);
    throw error;
  }
};

// Address Services
export const getUserAddresses = async (userId: string) => {
  try {
    const addressesRef = collection(db, 'addresses');
    const q = query(
      addressesRef, 
      where('userId', '==', userId),
      orderBy('createdAt', 'desc'),
      limit(10)
    );
    const querySnapshot = await getDocs(q);
    return querySnapshot.docs.map(doc => ({
      id: doc.id,
      ...doc.data()
    }));
  } catch (error) {
    console.error('Error getting addresses:', error);
    throw error;
  }
};

export const addAddress = async (userId: string, addressData: any) => {
  try {
    const addressesRef = collection(db, 'addresses');
    const docRef = await addDoc(addressesRef, {
      userId,
      ...addressData,
      createdAt: Timestamp.now()
    });
    return docRef.id;
  } catch (error) {
    console.error('Error adding address:', error);
    throw error;
  }
};

export const deleteAddress = async (addressId: string) => {
  try {
    const addressRef = doc(db, 'addresses', addressId);
    await deleteDoc(addressRef);
  } catch (error) {
    console.error('Error deleting address:', error);
    throw error;
  }
};

export const getAddress = async (addressId: string) => {
  try {
    const addressRef = doc(db, 'addresses', addressId);
    const addressSnap = await getDoc(addressRef);
    if (addressSnap.exists()) {
      return {
        id: addressSnap.id,
        ...addressSnap.data()
      };
    }
    return null;
  } catch (error) {
    console.error('Error getting address:', error);
    throw error;
  }
};

// Order Services
export const createOrder = async (orderData: any) => {
  try {
    const ordersRef = collection(db, 'orders');
    const docRef = await addDoc(ordersRef, {
      ...orderData,
      createdAt: Timestamp.now(),
      updatedAt: Timestamp.now()
    });
    return docRef.id;
  } catch (error) {
    console.error('Error creating order:', error);
    throw error;
  }
};

export const getOrder = async (orderId: string) => {
  try {
    const orderRef = doc(db, 'orders', orderId);
    const orderSnap = await getDoc(orderRef);
    return orderSnap.exists() ? { id: orderSnap.id, ...orderSnap.data() } : null;
  } catch (error) {
    console.error('Error getting order:', error);
    throw error;
  }
};

export const getUserOrders = async (userId: string) => {
  try {
    const ordersRef = collection(db, 'orders');
    const q = query(
      ordersRef, 
      where('userId', '==', userId),
      orderBy('createdAt', 'desc')
    );
    const querySnapshot = await getDocs(q);
    return querySnapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));
  } catch (error) {
    console.error('Error getting user orders:', error);
    throw error;
  }
};

export const updateOrderStatus = async (orderId: string, status: string) => {
  try {
    const orderRef = doc(db, 'orders', orderId);
    await updateDoc(orderRef, {
      status,
      updatedAt: Timestamp.now()
    });
  } catch (error) {
    console.error('Error updating order status:', error);
    throw error;
  }
};

export const updateAddress = async (addressId: string, data: any) => {
  try {
    const addressRef = doc(db, 'addresses', addressId);
    await updateDoc(addressRef, {
      ...data,
      updatedAt: Timestamp.now()
    });
  } catch (error) {
    console.error('Error updating address:', error);
    throw error;
  }
};

export const getAllOrders = async () => {
  try {
    const ordersRef = collection(db, 'orders');
    const q = query(ordersRef, orderBy('createdAt', 'desc'));
    const querySnapshot = await getDocs(q);
    return querySnapshot.docs.map(doc => ({
      id: doc.id,
      ...doc.data()
    }));
  } catch (error) {
    console.error('Error getting all orders:', error);
    throw error;
  }
};

// Tambahkan fungsi ini
export const getProductsByUser = async (userId: string) => {
  try {
    const productsRef = collection(db, 'products');
    // Hapus orderBy sementara
    const q = query(
      productsRef, 
      where('sellerId', '==', userId)
      // orderBy('createdAt', 'desc') // comment ini dulu
    );
    
    const querySnapshot = await getDocs(q);
    return querySnapshot.docs.map(doc => ({
      id: doc.id,
      ...doc.data()
    }));
  } catch (error) {
    console.error('Error getting user products:', error);
    throw error;
  }
};