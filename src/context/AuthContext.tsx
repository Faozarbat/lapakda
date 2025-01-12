import { createContext, useContext, useEffect, useState } from 'react';
import { 
  onAuthStateChanged, 
  signInWithEmailAndPassword,
  createUserWithEmailAndPassword,
  signOut,
  User
} from 'firebase/auth';
import { auth } from '@/config/firebase';
import { createUserProfile } from '@/lib/firebase/services';

// Rate limiting
const RATE_LIMIT_DURATION = 60000; // 1 minute
const MAX_ATTEMPTS = 5;
const attemptMap = new Map<string, { count: number; timestamp: number }>();

// Password validation
const isStrongPassword = (password: string) => {
  const minLength = 8;
  const hasUpperCase = /[A-Z]/.test(password);
  const hasLowerCase = /[a-z]/.test(password);
  const hasNumbers = /\d/.test(password);
  const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);

  return (
    password.length >= minLength &&
    hasUpperCase &&
    hasLowerCase &&
    hasNumbers &&
    hasSpecialChar
  );
};

// Input sanitization
const sanitizeInput = (input: string) => {
  return input.replace(/<[^>]*>/g, '').trim();
};

type AuthContextType = {
  user: User | null;
  loading: boolean;
  signIn: (email: string, password: string) => Promise<void>;
  signUp: (email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
};

const AuthContext = createContext<AuthContextType>({} as AuthContextType);

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const unsubscribe = onAuthStateChanged(auth, (user) => {
      setUser(user);
      setLoading(false);
    });

    return () => unsubscribe();
  }, []);

  const checkRateLimit = (email: string) => {
    const now = Date.now();
    const attempt = attemptMap.get(email);

    if (attempt) {
      if (now - attempt.timestamp < RATE_LIMIT_DURATION) {
        if (attempt.count >= MAX_ATTEMPTS) {
          throw new Error('Terlalu banyak percobaan. Silakan tunggu beberapa saat.');
        }
        attemptMap.set(email, { count: attempt.count + 1, timestamp: attempt.timestamp });
      } else {
        attemptMap.set(email, { count: 1, timestamp: now });
      }
    } else {
      attemptMap.set(email, { count: 1, timestamp: now });
    }
  };

  const signIn = async (email: string, password: string) => {
    const sanitizedEmail = sanitizeInput(email);
    checkRateLimit(sanitizedEmail);
    await signInWithEmailAndPassword(auth, sanitizedEmail, password);
  };

  const signUp = async (email: string, password: string) => {
    try {
      const userCredential = await createUserWithEmailAndPassword(auth, email, password);
      
      // Create user profile
      await createUserProfile({
        uid: userCredential.user.uid,
        email: userCredential.user.email!,
        role: 'user', // default role
        createdAt: new Date(),
      });
  
      return userCredential;
    } catch (error) {
      console.error('Error signing up:', error);
      throw error;
    }
  };

  const logout = async () => {
    await signOut(auth);
  };

  return (
    <AuthContext.Provider value={{ user, loading, signIn, signUp, logout }}>
      {!loading && children}
    </AuthContext.Provider>
  );
}

export const useAuth = () => useContext(AuthContext);