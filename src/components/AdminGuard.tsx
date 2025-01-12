'use client';

import { useEffect } from 'react';
import { useAuth } from '@/context/AuthContext';
import { getUserProfile } from '@/lib/firebase/services';
import { useRouter } from 'next/navigation';

export default function AdminGuard({ children }: { children: React.ReactNode }) {
  const { user } = useAuth();
  const router = useRouter();

  useEffect(() => {
    const checkAdmin = async () => {
      if (!user) {
        router.push('/login');
        return;
      }

      const profile = await getUserProfile(user.uid);
      if (!profile || profile.role !== 'admin') {
        router.push('/');
        return;
      }
    };

    checkAdmin();
  }, [user, router]);

  return <>{children}</>;
}