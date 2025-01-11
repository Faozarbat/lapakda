import Image from "next/image";
import Link from "next/link";

export default function Home() {
  return (
    <div className="min-h-screen flex flex-col font-[family-name:var(--font-geist-sans)]">      
      {/* Hero Section */}
      <main className="flex-1 pt-16">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <div className="text-center">
            <h1 className="text-4xl sm:text-6xl font-bold mb-6">
              Marketplace Terpercaya di Batam
            </h1>
            <p className="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
              Temukan produk lokal berkualitas dari penjual terpercaya di sekitar Anda
            </p>
            
            {/* CTA Buttons */}
            <div className="flex gap-4 justify-center">
              <Link
                href="/products"
                className="rounded-full bg-foreground text-background px-6 py-3 hover:bg-[#383838] transition-colors"
              >
                Mulai Belanja
              </Link>
              <Link
                href="/register"
                className="rounded-full border border-solid border-black/[.08] px-6 py-3 hover:bg-[#f2f2f2] transition-colors"
              >
                Daftar Sekarang
              </Link>
            </div>
          </div>

          {/* Features Grid */}
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-8 mt-16">
            <div className="p-6 rounded-xl bg-white shadow-sm">
              <img
                src="/images/ProdukLokal.jpg"
                alt="Produk Lokal icon"
                className="w-16 h-16 object-cover mb-4"
              />
              <h3 className="font-semibold text-lg mb-2">Produk Lokal</h3>
              <p className="text-gray-600">Temukan berbagai produk dari penjual lokal terpercaya di Batam</p>
            </div>
            <div className="p-6 rounded-xl bg-white shadow-sm">
              <img
                src="/images/PengirimanCepat.png"
                alt="Delivery icon"
                className="w-16 h-16 object-cover mb-4"
              />
              <h3 className="font-semibold text-lg mb-2">Pengiriman Cepat</h3>
              <p className="text-gray-600">Nikmati pengiriman super cepat dalam area Batam</p>
            </div>
            <div className="p-6 rounded-xl bg-white shadow-sm">
              <img
                src="/images/TransaksiAman.jpg"
                alt="Secure icon"
                className="w-16 h-16 object-cover mb-4"
              />
              <h3 className="font-semibold text-lg mb-2">Transaksi Aman</h3>
              <p className="text-gray-600">Pembayaran dan transaksi dijamin aman</p>
            </div>
          </div>
        </div>
      </main>

      {/* Footer */}
      <footer className="border-t border-gray-200 py-8">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-center gap-8 text-sm text-gray-600">
            <Link href="/about" className="hover:underline hover:underline-offset-4">
              Tentang Kami
            </Link>
            <Link href="/contact" className="hover:underline hover:underline-offset-4">
              Hubungi Kami
            </Link>
            <Link href="/privacy" className="hover:underline hover:underline-offset-4">
              Kebijakan Privasi
            </Link>
          </div>
        </div>
      </footer>
    </div>
  );
}