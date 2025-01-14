// /components/chat/ImagePreview.tsx
interface ImagePreviewProps {
    file: File;
    onRemove: () => void;
  }
  
  export default function ImagePreview({ file, onRemove }: ImagePreviewProps) {
    const imageUrl = URL.createObjectURL(file);
  
    return (
      <div className="mt-2 p-2 bg-gray-50 rounded-lg">
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-2">
            <img
              src={imageUrl}
              alt="Preview"
              className="h-16 w-16 object-cover rounded"
              onLoad={() => URL.revokeObjectURL(imageUrl)}
            />
            <span className="text-sm text-gray-600 truncate max-w-[200px]">
              {file.name}
            </span>
          </div>
          <button
            onClick={onRemove}
            className="text-red-500 hover:text-red-700 p-1"
          >
            <X className="h-5 w-5" />
          </button>
        </div>
      </div>
    );
  }