'use client';

import { useState, useEffect } from 'react';
import { useChat } from '@/context/ChatContext';
import { formatDistanceToNow } from 'date-fns';
import { id } from 'date-fns/locale';
import { useAuth } from '@/context/AuthContext';
import Image from 'next/image';
import ChatSearch from './ChatSearch';
import { MoreVertical, Trash2 } from 'lucide-react';
import { deleteChatRoom } from '@/lib/firebase/chatService';

export default function ChatSidebar() {
  const { chatRooms, activeChatRoom, setActiveChatRoom } = useChat();
  const { user } = useAuth();
  const [filteredRooms, setFilteredRooms] = useState(chatRooms);
  const [menuOpenId, setMenuOpenId] = useState<string | null>(null);

  useEffect(() => {
    const sortedRooms = [...chatRooms].sort((a, b) => {
      const timeA = a.updatedAt?.toDate?.() || new Date(a.updatedAt);
      const timeB = b.updatedAt?.toDate?.() || new Date(b.updatedAt);
      return timeB.getTime() - timeA.getTime();
    });
    setFilteredRooms(sortedRooms);
  }, [chatRooms]);

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (menuOpenId) {
        setMenuOpenId(null);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, [menuOpenId]);

  const getOtherParticipant = (participants: string[]) => {
    return participants.find(id => id !== user?.uid) || '';
  };

  const formatTimestamp = (timestamp: any) => {
    if (!timestamp) return '';
    try {
      const date = timestamp.toDate ? timestamp.toDate() : new Date(timestamp);
      return formatDistanceToNow(date, { addSuffix: true, locale: id });
    } catch (error) {
      console.error('Error formatting date:', error);
      return '';
    }
  };

  return (
    <div className="w-full max-w-sm border-r bg-white">
      <div className="p-4 border-b">
        <h2 className="text-lg font-semibold">Chat</h2>
      </div>
      <ChatSearch rooms={chatRooms} onSearch={setFilteredRooms} />
      <div className="overflow-y-auto h-[calc(100vh-8rem)]">
        {filteredRooms.map((room) => (
          
          <div
            key={room.id}
            onClick={() => setActiveChatRoom(room)}
            className={`relative p-4 border-b hover:bg-gray-50 ${
              activeChatRoom?.id === room.id ? 'bg-indigo-50' : ''
            }`}
          >
            <div className="flex items-center space-x-3">
              <div className="flex-shrink-0">
                <div className="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                  <span className="text-gray-500">U</span>
                </div>
              </div>
              
              <div className="flex-1 min-w-0">
                <div className="flex justify-between items-center">
                  <p className="text-sm font-medium text-gray-900 truncate">
                    {getOtherParticipant(room.participants)}
                  </p>
                  <div className="flex items-center gap-2">
                    {room.updatedAt && (
                      <p className="text-xs text-gray-500">
                        {formatTimestamp(room.updatedAt)}
                      </p>
                    )}
                    <div className="relative">
                      <button
                        onClick={(e) => {
                          e.stopPropagation();
                          setMenuOpenId(menuOpenId === room.id ? null : room.id);
                        }}
                        className="p-1 hover:bg-gray-100 rounded-full"
                      >
                        <MoreVertical className="h-5 w-5 text-gray-500" />
                      </button>
                      
                      {menuOpenId === room.id && (
                        <div 
                          className="fixed inset-0"
                          onClick={(e) => {
                            e.stopPropagation();
                            setMenuOpenId(null);
                          }}
                        >
                          <div 
                            className="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border"
                            onClick={e => e.stopPropagation()}
                          >
                            <button
                              onClick={(e) => {
                                e.stopPropagation();
                                if (window.confirm('Yakin ingin menghapus chat ini?')) {
                                  deleteChatRoom(room.id)
                                    .then(() => {
                                      if (activeChatRoom?.id === room.id) {
                                        setActiveChatRoom(null);
                                      }
                                      setMenuOpenId(null);
                                    })
                                    .catch((error) => {
                                      console.error('Error deleting chat:', error);
                                    });
                                }
                              }}
                              className="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                            >
                              <Trash2 className="h-4 w-4 mr-2" />
                              Hapus Chat
                            </button>
                          </div>
                          <div className={`flex-1 min-w-0 ${
                             room.lastMessage && 
                              room.lastMessage.receiverId === user?.uid && 
                              !room.lastMessage.read ? 'font-bold' : ''
                            }`}>
                             <p className="mt-1 text-sm text-gray-500 truncate">
                                    {room.lastMessage.content}
                                  </p>
                                </div>
                        </div>
                      )}
                    </div>
                  </div>
                </div>
                {room.lastMessage && (
                  <p className="mt-1 text-sm text-gray-500 truncate">
                    {room.lastMessage.content}
                  </p>
                )}
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}