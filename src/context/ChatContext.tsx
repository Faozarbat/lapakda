// /context/ChatContext.tsx
'use client';

import { createContext, useContext, useEffect, useState } from 'react';
import { collection, query, where, onSnapshot } from 'firebase/firestore';
import { db } from '@/config/firebase';
import { useAuth } from './AuthContext';
import { ChatRoom, ChatMessage } from '@/types/chat';

interface ChatContextType {
  unreadCount: number;
  chatRooms: ChatRoom[];
  activeChatRoom: ChatRoom | null;
  setActiveChatRoom: (room: ChatRoom | null) => void;
  messages: ChatMessage[];
  setMessages: (messages: ChatMessage[]) => void;
}

const ChatContext = createContext<ChatContextType>({
  unreadCount: 0,
  chatRooms: [],
  activeChatRoom: null,
  setActiveChatRoom: () => {},
  messages: [],
  setMessages: () => {}
});

export function ChatProvider({ children }: { children: React.ReactNode }) {
  const { user } = useAuth();
  const [unreadCount, setUnreadCount] = useState(0);
  const [chatRooms, setChatRooms] = useState<ChatRoom[]>([]);
  const [activeChatRoom, setActiveChatRoom] = useState<ChatRoom | null>(null);
  const [messages, setMessages] = useState<ChatMessage[]>([]);

  useEffect(() => {
    if (!user) return;

    const chatRoomsRef = collection(db, 'chatRooms');
    const q = query(
      chatRoomsRef,
      where('participants', 'array-contains', user.uid)
    );

    const unsubscribe = onSnapshot(q, (snapshot) => {
      const rooms = snapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      } as ChatRoom));

      setChatRooms(rooms);

      // Calculate unread messages
      let count = 0;
      rooms.forEach(room => {
        if (room.lastMessage && 
            room.lastMessage.receiverId === user.uid && 
            !room.lastMessage.read) {
          count++;
        }
      });
      setUnreadCount(count);
    });

    return () => unsubscribe();
  }, [user]);

  return (
    <ChatContext.Provider 
      value={{ 
        unreadCount, 
        chatRooms, 
        activeChatRoom, 
        setActiveChatRoom,
        messages,
        setMessages
      }}
    >
      {children}
    </ChatContext.Provider>
  );
}

export const useChat = () => useContext(ChatContext);