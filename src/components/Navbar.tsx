'use client';

import Link from 'next/link';
import { useAuth } from '@/context/AuthContext';
import { useState } from 'react';
import { Menu, X, ShoppingCart, User } from 'lucide-react';
import Image from 'next/image';

export default function Navbar() {
  const { user, logout } = useAuth();
  const [isMenuOpen, setIsMenuOpen] = useState(false);

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
            <Link href="/categories" className="text-gray-700 hover:text-indigo-600">
              Kategori
            </Link>
            
            {user ? (
              <>
                <Link href="/cart" className="text-gray-700 hover:text-indigo-600">
                  <ShoppingCart className="h-6 w-6" />
                </Link>
                <div className="relative group">
                  <button className="flex items-center text-gray-700 hover:text-indigo-600">
                    <User className="h-6 w-6" />
                  </button>
                  <div className="absolute right-0 w-48 py-2 mt-2 bg-white rounded-md shadow-xl hidden group-hover:block">
                    <Link href="/profile" className="block px-4 py-2 text-gray-700 hover:bg-indigo-50">
                      Profil
                    </Link>
                    <Link href="/orders" className="block px-4 py-2 text-gray-700 hover:bg-indigo-50">
                      Pesanan
                    </Link>
                    <button
                      onClick={() => logout()}
                      className="block w-full text-left px-4 py-2 text-gray-700 hover:bg-indigo-50"
                    >
                      Keluar
                    </button>
                  </div>
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
              href="/categories"
              className="block px-3 py-2 rounded-md text-gray-700 hover:bg-indigo-50"
            >
              Kategori
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