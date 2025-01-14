// /src/app/(auth)/chat/page.tsx
'use client';

import ChatLayout from '@/components/chat/ChatLayout';
import { useAuth } from '@/context/AuthContext';
import { useRouter } from 'next/navigation';
import { useEffect } from 'react';

export default function ChatPage() {
  const { user } = useAuth();
  const router = useRouter();

  useEffect(() => {
    if (!user) {
      router.push('/login');
    }
  }, [user, router]);

  if (!user) {
    return null;
  }

  return (
    <main className="min-h-screen pt-16">
      <ChatLayout />
    </main>
  );
}