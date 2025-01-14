// components/chat/ChatSellerButton.tsx
'use client';

import { useState } from 'react';
import { MessageSquare } from 'lucide-react';
import { useRouter } from 'next/navigation';
import { useAuth } from '@/context/AuthContext';
import { createChatRoom, sendMessage } from '@/lib/firebase/chatService';

interface ChatSellerButtonProps {
  sellerId: string;
  productId: string;
  productName: string;
}

export default function ChatSellerButton({ sellerId, productId, productName }: ChatSellerButtonProps) {
  const router = useRouter();
  const { user } = useAuth();
  const [loading, setLoading] = useState(false);

  const handleStartChat = async () => {
    if (!user) {
      router.push('/login');
      return;
    }

    // Jangan izinkan chat dengan diri sendiri
    if (user.uid === sellerId) {
      return;
    }

    try {
      setLoading(true);
      const chatRoomRef = await createChatRoom(user.uid, sellerId);
      
      // Kirim pesan pertama dengan info produk
      await sendMessage(chatRoomRef.id, {
        senderId: user.uid,
        receiverId: sellerId,
        content: `Hai, saya tertarik dengan produk "${productName}"`,
        metadata: {
          type: 'product_inquiry',
          productId,
          productName
        }
      });

      router.push(`/chat?room=${chatRoomRef.id}`);
    } catch (error) {
      console.error('Error starting chat:', error);
    } finally {
      setLoading(false);
    }
  };

  // Jika user adalah penjual, jangan tampilkan tombol chat
  if (user?.uid === sellerId) {
    return null;
  }

  return (
    <button
      onClick={handleStartChat}
      disabled={loading || !user}
      className="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50"
    >
      <MessageSquare className="h-5 w-5" />
      {loading ? 'Memulai chat...' : 'Chat Penjual'}
    </button>
  );
}