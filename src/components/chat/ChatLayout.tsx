// /components/chat/ChatLayout.tsx
'use client';

import { useState, useEffect } from 'react';
import { useAuth } from '@/context/AuthContext';
import { useChat } from '@/context/ChatContext';
import { getChatRooms } from '@/lib/firebase/chatService';
import ChatSidebar from './ChatSidebar';
import ChatWindow from './ChatWindow';

export default function ChatLayout() {
  const { user } = useAuth();
  const { activeChatRoom,  } = useChat();
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const loadInitialData = async () => {
      if (!user) return;
      try {
        await getChatRooms(user.uid);
        setLoading(false);
      } catch (error) {
        console.error('Error loading chat rooms:', error);
        setLoading(false);
      }
    };

    loadInitialData();
  }, [user]);

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="animate-spin rounded-full h-32 w-32 border-t-2 border-b-2 border-indigo-500"></div>
      </div>
    );
  }

  return (
    <div className="flex h-[calc(100vh-4rem)] bg-gray-50">
      <ChatSidebar />
      {activeChatRoom ? (
        <ChatWindow />
      ) : (
        <div className="flex-1 flex items-center justify-center text-gray-500">
          Pilih chat untuk memulai percakapan
        </div>
      )}
    </div>
  );
}