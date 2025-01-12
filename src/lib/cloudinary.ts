import { v2 as cloudinary } from 'cloudinary';

cloudinary.config({
  cloud_name: process.env.NEXT_PUBLIC_CLOUDINARY_CLOUD_NAME,
  api_key: process.env.CLOUDINARY_API_KEY,
  api_secret: process.env.CLOUDINARY_API_SECRET,
});

export const uploadToCloudinary = async (file: File): Promise<string> => {
  const buffer = await file.arrayBuffer();
  const bytes = Buffer.from(buffer);
  
  const base64String = bytes.toString('base64');
  const dataURI = `data:${file.type};base64,${base64String}`;
  
  const result = await cloudinary.uploader.upload(dataURI, {
    folder: 'lapakda',
  });
  
  return result.secure_url;
};