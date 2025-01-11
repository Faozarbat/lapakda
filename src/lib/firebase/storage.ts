import { storage } from '@/config/firebase';
import { ref, uploadBytes, getDownloadURL } from 'firebase/storage';

export const uploadImage = async (file: File, path: string): Promise<string> => {
  const storageRef = ref(storage, path);
  await uploadBytes(storageRef, file);
  return getDownloadURL(storageRef);
};

export const uploadProductImages = async (files: File[], productId: string): Promise<string[]> => {
  const uploadPromises = files.map((file, index) => {
    const path = `products/${productId}/${index}-${file.name}`;
    return uploadImage(file, path);
  });

  return Promise.all(uploadPromises);
};