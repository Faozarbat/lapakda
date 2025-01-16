// /components/chat/ChatWindow.tsx
'use client';

import { useState, useEffect, useRef } from 'react';
import { useChat } from '@/context/ChatContext';
import { useAuth } from '@/context/AuthContext';
import { subscribeToChatRoom, sendMessage,markRoomAsRead  } from '@/lib/firebase/chatService';
import ChatInput from './ChatInput';
import MessageBubble from './MessageBubble';
import { uploadImage } from '@/lib/firebase/storage';

export default function ChatWindow() {
  const { activeChatRoom, messages, setMessages } = useChat();
  const { user } = useAuth();
  const messagesEndRef = useRef<HTMLDivElement>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!activeChatRoom || !user) return;

    const unsubscribe = subscribeToChatRoom(activeChatRoom.id, (newMessages) => {
      setMessages(newMessages);
      setLoading(false);
    });

    return () => unsubscribe();
  }, [activeChatRoom, user]);

  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages]);
  useEffect(() => {
    if (activeChatRoom && user) {
      console.log('Marking room as read:', activeChatRoom.id);
      markRoomAsRead(activeChatRoom.id, user.uid)
        .then(() => console.log('Room marked as read'))
        .catch(console.error);
    }
  }, [activeChatRoom, user]);
  const handleSendMessage = async (content: string, image?: File) => {
    if (!activeChatRoom || !user || (!content.trim() && !image)) return;

    try {
      let imageUrl;
      if (image) {
        // Handle image upload
      }

      await sendMessage(activeChatRoom.id, {
        content: content.trim(),
        senderId: user.uid,
        receiverId: activeChatRoom.participants.find(id => id !== user.uid) || '',
        imageUrl
      });
    } catch (error) {
      console.error('Error sending message:', error);
    }
  };

  if (loading) {
    return (
      <div className="flex-1 flex items-center justify-center">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
      </div>
    );
  }

  return (
    <div className="flex-1 flex flex-col bg-white">
      <div className="p-4 border-b">
        <h2 className="font-semibold">
          Chat with Participant
        </h2>
      </div>

      <div className="flex-1 overflow-y-auto p-4 space-y-4">
        {messages.map((message, index) => (
          <MessageBubble 
            key={message.id} 
            message={message} 
            isOwnMessage={message.senderId === user?.uid}
            showTimestamp={
              index === 0 || 
              messages[index - 1].senderId !== message.senderId
            }
          />
        ))}
        <div ref={messagesEndRef} />
      </div>

      <ChatInput
        onSend={async (content, image) => {
          try {
            let imageUrl;
            if (image) {
              imageUrl = await uploadImage(image, `chats/${activeChatRoom.id}/${Date.now()}-${image.name}`);
            }
            
            await sendMessage(activeChatRoom.id, {
              senderId: user!.uid,
              receiverId: activeChatRoom.participants.find(id => id !== user!.uid) || '',
              content,
              imageUrl
            });
          } catch (error) {
            console.error('Error sending message:', error);
          }
        }}
      />
    </div>
  );
}
