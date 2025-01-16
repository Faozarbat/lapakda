// /lib/firebase/chatService.ts
import {
    collection,
    doc,
    getDocs,
    query,
    where,
    orderBy,
    onSnapshot,
    updateDoc,
    Timestamp,
    writeBatch,
    addDoc
  } from 'firebase/firestore';
  import { db } from '@/config/firebase';
  import { uploadImage } from './storage';
  import { ChatMessage, ChatRoom } from '@/types/chat';
  
  export const createChatRoom = async (buyerId: string, sellerId: string) => {
    try {
      // Cek apakah chat room sudah ada
      const chatRoomsRef = collection(db, 'chatRooms');
      const q = query(
        chatRoomsRef,
        where('participants', 'array-contains', buyerId)
      );
      
      const snapshot = await getDocs(q);
      const existingRoom = snapshot.docs.find(doc => {
        const data = doc.data();
        return data.participants.includes(sellerId);
      });
  
      if (existingRoom) {
        return existingRoom.ref;
      }
  
      // Jika belum ada, buat chat room baru
      const newRoomRef = await addDoc(chatRoomsRef, {
        participants: [buyerId, sellerId],
        createdAt: Timestamp.now(),
        updatedAt: Timestamp.now()
      });
  
      return newRoomRef;
    } catch (error) {
      console.error('Error creating chat room:', error);
      throw error;
    }
  };
  
  export const sendMessage = async (roomId: string, message: Partial<ChatMessage>) => {
    try {
      const messagesRef = collection(db, `chatRooms/${roomId}/messages`);
      const messageData = {
        ...message,
        imageUrl: message.imageUrl || null, // ubah undefined jadi null
        createdAt: Timestamp.now(),
        read: false
      };
      
  
      await addDoc(messagesRef, messageData);
      
      // Update lastMessage in chatRoom
      await updateDoc(doc(db, 'chatRooms', roomId), {
        lastMessage: messageData,
        updatedAt: Timestamp.now()
      });
    } catch (error) {
      console.error('Error sending message:', error);
      throw error;
    }
  };
  
  export const markMessageAsRead = async (roomId: string, messageId: string) => {
    try {
      // Update message
      const messageRef = doc(db, `chatRooms/${roomId}/messages/${messageId}`);
      await updateDoc(messageRef, { read: true });
      
      // Update last message in chat room juga
      const roomRef = doc(db, 'chatRooms', roomId);
      await updateDoc(roomRef, {
        'lastMessage.read': true
      });
    } catch (error) {
      console.error('Error marking message as read:', error);
      throw error;
    }
  }
  export const getChatRooms = async (userId: string) => {
    try {
      const chatRoomsRef = collection(db, 'chatRooms');
      const q = query(
        chatRoomsRef,
        where('participants', 'array-contains', userId),
        orderBy('updatedAt', 'desc')
      );
      
      const snapshot = await getDocs(q);
      return snapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      })) as ChatRoom[];
    } catch (error) {
      console.error('Error getting chat rooms:', error);
      throw error;
    }
  };
  
  export const getChatMessages = async (roomId: string) => {
    try {
      const messagesRef = collection(db, `chatRooms/${roomId}/messages`);
      const q = query(messagesRef, orderBy('createdAt', 'asc'));
      
      const snapshot = await getDocs(q);
      return snapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      })) as ChatMessage[];
    } catch (error) {
      console.error('Error getting messages:', error);
      throw error;
    }
  };
  
  export const uploadChatImage = async (roomId: string, file: File) => {
    try {
      const path = `chats/${roomId}/${Date.now()}-${file.name}`;
      return await uploadImage(file, path);
    } catch (error) {
      console.error('Error uploading chat image:', error);
      throw error;
    }
  };
  
  // Subscription for real-time updates
  export const subscribeToChatRoom = (roomId: string, callback: (messages: ChatMessage[]) => void) => {
    const messagesRef = collection(db, `chatRooms/${roomId}/messages`);
    const q = query(messagesRef, orderBy('createdAt', 'asc'));
  
    return onSnapshot(q, (snapshot) => {
      const messages = snapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      })) as ChatMessage[];
      callback(messages);
    });
  };


  export const deleteChatRoom = async (roomId: string) => {
    try {
      // Gunakan batch write untuk atomic operation
      const batch = writeBatch(db);
  
      // 1. Hapus semua pesan dalam chat room
      const messagesRef = collection(db, `chatRooms/${roomId}/messages`);
      const messagesSnapshot = await getDocs(messagesRef);
      messagesSnapshot.docs.forEach((doc) => {
        batch.delete(doc.ref);
      });
  
      // 2. Hapus typing indicators jika ada
      const typingRef = collection(db, `chatRooms/${roomId}/typing`);
      const typingSnapshot = await getDocs(typingRef);
      typingSnapshot.docs.forEach((doc) => {
        batch.delete(doc.ref);
      });
  
      // 3. Hapus chat room itu sendiri
      const roomRef = doc(db, 'chatRooms', roomId);
      batch.delete(roomRef);
  
      // Eksekusi semua operasi delete
      await batch.commit();
  
      console.log('Chat room deleted successfully');
    } catch (error) {
      console.error('Error deleting chat room:', error);
      throw error;
    }
  };
  export const markRoomAsRead = async (roomId: string, userId: string) => {
    try {
      const messagesRef = collection(db, `chatRooms/${roomId}/messages`);
      const q = query(
        messagesRef, 
        where('receiverId', '==', userId),
        where('read', '==', false)
      );
  
      const snapshot = await getDocs(q);
      const batch = writeBatch(db);
  
      snapshot.docs.forEach((doc) => {
        batch.update(doc.ref, { read: true });
      });
  
      // Update last message
      const roomRef = doc(db, 'chatRooms', roomId);
      batch.update(roomRef, {
        'lastMessage.read': true
      });
  
      await batch.commit();
      console.log('Room marked as read successfully');
    } catch (error) {
      console.error('Error marking room as read:', error);
      throw error;
    }
  };