// /components/chat/ChatInput.tsx
'use client';

import { useState, useRef } from 'react';
import { Image as ImageIcon, Send, X } from 'lucide-react';
import { useDebounce } from '@/hooks/useDebounce';
import ImagePreview from './ImagePreview';

interface ChatInputProps {
  onSend: (content: string, image?: File) => Promise<void>;
}

export default function ChatInput({ onSend }: ChatInputProps) {
  const [message, setMessage] = useState('');
  const [image, setImage] = useState<File | null>(null);
  const [sending, setSending] = useState(false);
  const fileInputRef = useRef<HTMLInputElement>(null);
  const textareaRef = useRef<HTMLTextAreaElement>(null);

  const handleSend = async () => {
    if (!message.trim() && !image) return;
    
    try {
      setSending(true);
      await onSend(message, image || undefined);
      setMessage('');
      setImage(null);
    } finally {
      setSending(false);
    }
  };

  const handleKeyPress = (e: React.KeyboardEvent) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      handleSend();
    }
  };

  return (
    <div className="p-4 border-t">
      {image && (
        <ImagePreview
          file={image}
          onRemove={() => setImage(null)}
        />
      )}
      <div className="flex items-end space-x-2">
        <div className="flex-1">
          <textarea
            ref={textareaRef}
            value={message}
            onChange={(e) => setMessage(e.target.value)}
            onKeyPress={handleKeyPress}
            placeholder="Ketik pesan..."
            className="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
            rows={1}
          />
        </div>
        <button
          onClick={() => fileInputRef.current?.click()}
          className="p-2 text-gray-500 hover:text-indigo-600"
          disabled={sending}
        >
          <ImageIcon className="h-6 w-6" />
        </button>
        <button
          onClick={handleSend}
          disabled={sending || (!message.trim() && !image)}
          className="p-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50"
        >
          <Send className="h-6 w-6" />
        </button>
      </div>
      <input
        type="file"
        ref={fileInputRef}
        accept="image/*"
        className="hidden"
        onChange={(e) => {
          const file = e.target.files?.[0];
          if (file && file.size <= 5 * 1024 * 1024) { // 5MB limit
            setImage(file);
          } else {
            alert('Ukuran file maksimal 5MB');
          }
        }}
      />
    </div>
  );
}



