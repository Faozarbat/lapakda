'use client';

import Link from 'next/link';
import { useAuth } from '@/context/AuthContext';
import { useState, useRef, useEffect } from 'react';
import { Menu, X, ShoppingCart, User, MessageSquare } from 'lucide-react';
import Image from 'next/image';


export default function Navbar() {
  const { user, logout } = useAuth();
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [isProfileMenuOpen, setIsProfileMenuOpen] = useState(false);
  const profileMenuRef = useRef<HTMLDivElement>(null);
  const profileButtonRef = useRef<HTMLButtonElement>(null);

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (
        profileMenuRef.current && 
        profileButtonRef.current &&
        !profileMenuRef.current.contains(event.target as Node) &&
        !profileButtonRef.current.contains(event.target as Node)
      ) {
        setIsProfileMenuOpen(false);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, []);

  return (
    <nav className="bg-white shadow-sm fixed w-full top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-16">
          {/* Logo dan Brand */}
          <div className="flex-shrink-0 flex items-center gap-2">
            <Image 
              src="/images/LogoUtama.jpg"
              alt="Lapakda Logo"
              width={40}
              height={40}
              className="rounded-lg"
            />
            <Link href="/" className="text-2xl font-bold text-indigo-600">
              Lapakda
            </Link>
          </div>

          {/* Desktop Menu */}
          <div className="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-8">
            <Link href="/products" className="text-gray-700 hover:text-indigo-600">
              Produk
            </Link>
            
            
            {user ? (
              <>
                <Link href="/shop" className="text-gray-700 hover:text-indigo-600">
                        Toko_Ku
                       </Link>
                <Link href="/cart" className="text-gray-700 hover:text-indigo-600">
                  <ShoppingCart className="h-6 w-6" />
                </Link>
                <Link href="/chat" className="text-gray-700 hover:text-indigo-600">
                  <MessageSquare className="h-6 w-6" />
                </Link>
                <div className="relative">
                  <button
                    ref={profileButtonRef}
                    onClick={() => setIsProfileMenuOpen(!isProfileMenuOpen)}
                    className="flex items-center text-gray-700 hover:text-indigo-600"
                  >
                    <User className="h-6 w-6" />
                  </button>
                  
                  {isProfileMenuOpen && (
                    <div 
                      ref={profileMenuRef}
                      className="absolute right-0 w-48 py-2 mt-2 bg-white rounded-md shadow-xl"
                    >
                      <Link 
                        href="/profile" 
                        className="block px-4 py-2 text-gray-700 hover:bg-indigo-50"
                      >
                        Profil
                      </Link>
                      
                      <Link 
                        href="/orders" 
                        className="block px-4 py-2 text-gray-700 hover:bg-indigo-50"
                      >
                        Pesanan
                      </Link>
                      <button
                        onClick={() => {
                          logout();
                          setIsProfileMenuOpen(false);
                        }}
                        className="block w-full text-left px-4 py-2 text-gray-700 hover:bg-indigo-50"
                      >
                        Keluar
                      </button>
                    </div>
                  )}
                </div>
              </>
            ) : (
              <Link
                href="/login"
                className="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
              >
                Masuk
              </Link>
            )}
          </div>

          {/* Mobile Menu Button */}
          <div className="sm:hidden flex items-center">
            <button
              onClick={() => setIsMenuOpen(!isMenuOpen)}
              className="text-gray-700 hover:text-indigo-600"
            >
              {isMenuOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
            </button>
          </div>
        </div>
      </div>

      {/* Mobile Menu */}
      {isMenuOpen && (
        <div className="sm:hidden">
          <div className="px-2 pt-2 pb-3 space-y-1">
            <Link
              href="/products"
              className="block px-3 py-2 rounded-md text-gray-700 hover:bg-indigo-50"
            >
              Produk
            </Link>
            <Link
              href="/shop"
              className="block px-3 py-2 rounded-md text-gray-700 hover:bg-indigo-50"
            >
              Toko_Ku
            </Link>
            
            {user ? (
              <>
                <Link
                  href="/cart"
                  className="block px-3 py-2 rounded-md text-gray-700 hover:bg-indigo-50"
                >
                  Keranjang
                </Link>
                
                <Link
                  href="/profile"
                  className="block px-3 py-2 rounded-md text-gray-700 hover:bg-indigo-50"
                >
                  Profil
                </Link>
                <Link
                  href="/orders"
                  className="block px-3 py-2 rounded-md text-gray-700 hover:bg-indigo-50"
                >
                  Pesanan
                </Link>
                
                <button
                  onClick={() => logout()}
                  className="block w-full text-left px-3 py-2 rounded-md text-gray-700 hover:bg-indigo-50"
                >
                  Keluar
                </button>
              </>
            ) : (
              <Link
                href="/login"
                className="block px-3 py-2 rounded-md text-gray-700 hover:bg-indigo-50"
              >
                Masuk
              </Link>
            )}
          </div>
        </div>
      )}
    </nav>
  );
}