// components/chat/ImageUploader.tsx
'use client';

import { useState, useRef } from 'react';
import { Image as ImageIcon, X } from 'lucide-react';
import Image from 'next/image';

interface ImageUploaderProps {
  onImageSelect: (file: File) => void;
  onImageRemove: () => void;
  selectedImage: File | null;
}

export default function ImageUploader({ 
  onImageSelect, 
  onImageRemove, 
  selectedImage 
}: ImageUploaderProps) {
  const fileInputRef = useRef<HTMLInputElement>(null);
  const [previewUrl, setPreviewUrl] = useState<string>('');

  const handleFileSelect = (file: File) => {
    if (file.size > 5 * 1024 * 1024) { // 5MB limit
      alert('Ukuran file maksimal 5MB');
      return;
    }

    setPreviewUrl(URL.createObjectURL(file));
    onImageSelect(file);
  };

  return (
    <div>
      <input
        type="file"
        ref={fileInputRef}
        className="hidden"
        accept="image/*"
        onChange={(e) => {
          const file = e.target.files?.[0];
          if (file) handleFileSelect(file);
        }}
      />

      <button
        type="button"
        onClick={() => fileInputRef.current?.click()}
        className="p-2 text-gray-500 hover:text-indigo-600"
      >
        <ImageIcon className="h-6 w-6" />
      </button>

      {selectedImage && previewUrl && (
        <div className="mt-2 relative">
          <Image
            src={previewUrl}
            alt="Preview"
            width={200}
            height={200}
            className="rounded-lg"
          />
          <button
            onClick={() => {
              onImageRemove();
              setPreviewUrl('');
            }}
            className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1"
          >
            <X className="h-4 w-4" />
          </button>
        </div>
      )}
    </div>
  );
}