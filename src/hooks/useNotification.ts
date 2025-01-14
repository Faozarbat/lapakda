// hooks/useNotification.ts
'use client';

import { useState, useEffect } from 'react';
import { collection, query, where, onSnapshot } from 'firebase/firestore';
import { db } from '@/config/firebase';
import { useAuth } from '@/context/AuthContext';

export function useNotification() {
  const { user } = useAuth();
  const [unreadCount, setUnreadCount] = useState(0);
  const [lastMessage, setLastMessage] = useState<any>(null);

  useEffect(() => {
    if (!user) return;

    const q = query(
      collection(db, 'chatRooms'),
      where('participants', 'array-contains', user.uid)
    );

    return onSnapshot(q, (snapshot) => {
      let count = 0;
      let lastMsg = null;

      snapshot.docs.forEach(doc => {
        const room = doc.data();
        if (room.lastMessage && 
            room.lastMessage.receiverId === user.uid && 
            !room.lastMessage.read) {
          count++;
          if (!lastMsg || room.lastMessage.createdAt > lastMsg.createdAt) {
            lastMsg = room.lastMessage;
          }
        }
      });

      setUnreadCount(count);
      setLastMessage(lastMsg);

      // Play notification sound if new message
      if (lastMsg && lastMsg !== lastMessage) {
        const audio = new Audio('/sounds/notification.mp3');
        audio.play().catch(console.error);
      }
    });
  }, [user]);

  return { unreadCount, lastMessage };
}

