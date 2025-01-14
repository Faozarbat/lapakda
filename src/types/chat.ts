// /types/chat.ts

export interface ChatMessage {
  id: string;
  senderId: string;
  receiverId: string;
  content: string;
  imageUrl?: string;
  createdAt: any; // Timestamp dari Firebase
  read: boolean;
  metadata?: {
    type?: 'text' | 'image' | 'product_inquiry';
    productId?: string;
    productName?: string;
  };
}
  
  export interface ChatRoom {
  id: string;
  participants: string[];
  lastMessage?: {
    content: string;
    senderId: string;
    createdAt: any; // Timestamp dari Firebase
    read: boolean;
  };
  createdAt: any;
  updatedAt: any;
}
  
  export interface ChatParticipant {
    uid: string;
    displayName: string;
    photoURL?: string;
    online?: boolean;
    lastSeen?: Date;
  }