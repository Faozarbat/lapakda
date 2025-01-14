// /components/chat/ChatNotification.tsx
'use client';

import { Bell } from 'lucide-react';
import { useChat } from '@/context/ChatContext';


export default function ChatNotification() {
  const { unreadCount } = useChat();

  if (unreadCount === 0) return null;

  return (
    <div className="relative">
      <Bell className="h-6 w-6" />
      <div className="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
        {unreadCount > 99 ? '99+' : unreadCount}
      </div>
    </div>
  );
}
