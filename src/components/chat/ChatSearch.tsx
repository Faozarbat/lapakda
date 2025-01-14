// components/chat/ChatSearch.tsx
'use client';

import { useState, useEffect } from 'react';
import { Search, X } from 'lucide-react';
import { ChatRoom } from '@/types/chat';
import { getUserProfile } from '@/lib/firebase/services';

interface ChatSearchProps {
  rooms: ChatRoom[];
  onSearch: (results: ChatRoom[]) => void;
}

interface ParticipantCache {
  [key: string]: {
    displayName: string;
    email: string;
  }
}

export default function ChatSearch({ rooms, onSearch }: ChatSearchProps) {
  const [searchTerm, setSearchTerm] = useState('');
  const [participantCache, setParticipantCache] = useState<ParticipantCache>({});
  const [isLoading, setIsLoading] = useState(false);

  // Load participant info
  useEffect(() => {
    const loadParticipants = async () => {
      const uniqueParticipants = new Set(
        rooms.flatMap(room => room.participants)
      );

      for (const participantId of uniqueParticipants) {
        if (!participantCache[participantId]) {
          try {
            const userProfile = await getUserProfile(participantId);
            setParticipantCache(prev => ({
              ...prev,
              [participantId]: {
                displayName: userProfile.displayName || '',
                email: userProfile.email || ''
              }
            }));
          } catch (error) {
            console.error('Error loading participant:', error);
          }
        }
      }
    };

    loadParticipants();
  }, [rooms]);

  const handleSearch = async (term: string) => {
    setSearchTerm(term);
    setIsLoading(true);

    try {
      if (!term.trim()) {
        onSearch(rooms);
        return;
      }

      const searchTermLower = term.toLowerCase();
      
      const results = rooms.filter(room => {
        // Search in last message
        const lastMessageMatch = room.lastMessage?.content
          ?.toLowerCase()
          .includes(searchTermLower);

        // Search in participant names
        const participantMatches = room.participants.some(participantId => {
          const participant = participantCache[participantId];
          if (!participant) return false;

          return (
            participant.displayName.toLowerCase().includes(searchTermLower) ||
            participant.email.toLowerCase().includes(searchTermLower)
          );
        });

        return lastMessageMatch || participantMatches;
      });

      onSearch(results);
    } catch (error) {
      console.error('Error searching chats:', error);
    } finally {
      setIsLoading(false);
    }
  };

  const clearSearch = () => {
    setSearchTerm('');
    onSearch(rooms);
  };

  return (
    <div className="p-4 border-b">
      <div className="relative">
        <input
          type="text"
          value={searchTerm}
          onChange={(e) => handleSearch(e.target.value)}
          placeholder="Cari chat berdasarkan nama atau pesan..."
          className="w-full pl-10 pr-12 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
        />
        <Search 
          className={`absolute left-3 top-2.5 h-5 w-5 ${
            isLoading ? 'text-indigo-500' : 'text-gray-400'
          }`} 
        />
        
        {searchTerm && (
          <button
            onClick={clearSearch}
            className="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
          >
            <X className="h-5 w-5" />
          </button>
        )}
      </div>

      {isLoading && (
        <div className="text-sm text-gray-500 mt-2">
          Mencari...
        </div>
      )}

      {!isLoading && searchTerm && rooms.length === 0 && (
        <div className="text-sm text-gray-500 mt-2">
          Tidak ada hasil pencarian
        </div>
      )}
    </div>
  );
}