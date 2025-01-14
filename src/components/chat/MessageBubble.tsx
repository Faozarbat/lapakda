// components/chat/MessageBubble.tsx
'use client';

import { formatDistanceToNow } from 'date-fns';
import { id } from 'date-fns/locale';
import { ChatMessage } from '@/types/chat';

interface MessageBubbleProps {
  message: ChatMessage;
  isOwnMessage: boolean;
  showTimestamp: boolean;
}

export default function MessageBubble({ message, isOwnMessage, showTimestamp }: MessageBubbleProps) {
  const formatTimestamp = (timestamp: any) => {
    if (!timestamp) return '';
    
    try {
      // Konversi timestamp Firestore ke Date
      const date = timestamp.toDate ? timestamp.toDate() : new Date(timestamp);
      return formatDistanceToNow(date, { addSuffix: true, locale: id });
    } catch (error) {
      console.error('Error formatting date:', error);
      return '';
    }
  };

  return (
    <div className={`flex ${isOwnMessage ? 'justify-end' : 'justify-start'}`}>
      <div
        className={`max-w-[70%] rounded-lg p-3 ${
          isOwnMessage
            ? 'bg-indigo-600 text-white'
            : 'bg-gray-100 text-gray-900'
        }`}
      >
        {message.imageUrl && (
          <img
            src={message.imageUrl}
            alt="Shared"
            className="max-w-full rounded-lg mb-2"
          />
        )}
        <p className="whitespace-pre-wrap">{message.content}</p>
        {showTimestamp && (
          <div className={`text-xs mt-1 ${isOwnMessage ? 'text-indigo-200' : 'text-gray-500'}`}>
            {formatTimestamp(message.createdAt)}
          </div>
        )}
      </div>
    </div>
  );
}