
export const uploadImage = async (file: File, path: string): Promise<string> => {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('upload_preset', 'profile_photos');
    formData.append('folder', path);
    
    try {
      const response = await fetch(
        `https://api.cloudinary.com/v1_1/${process.env.NEXT_PUBLIC_CLOUDINARY_CLOUD_NAME}/image/upload`,
        {
          method: 'POST',
          body: formData
        }
      );
      
      const data = await response.json();
      
      if (!response.ok) {
        throw new Error(data.message || 'Failed to upload image');
      }
      
      return data.secure_url;
    } catch (error) {
      console.error('Error uploading to Cloudinary:', error);
      throw error;
    }
  };
  
  export const uploadProductImages = async (files: File[], productId: string): Promise<string[]> => {
    const uploadPromises = files.map((file, index) => {
      const path = `products/${productId}/${index}-${file.name}`;
      return uploadImage(file, path);
    });
  
    return Promise.all(uploadPromises);
  };