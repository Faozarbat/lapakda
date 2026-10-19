<x-app-layout>
    <div x-data="{ showToast: false, toastMessage: '', showUploadModal: false, selectedBillId: null }"
         @show-toast.window="toastMessage = $event.detail.message; showToast = true; setTimeout(() => showToast = false, 3000)"
         class="pb-24">

        <!-- Header -->
        <header class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
            <div class="max-w-3xl mx-auto px-4 h-16 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700 hover:bg-gray-200 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
                </a>
                <h1 class="font-bold text-base text-gray-900 absolute left-1/2 -translate-x-1/2">Langganan & Tagihan</h1>
                <div class="w-10"></div>
            </div>
        </header>

        <div class="max-w-3xl mx-auto px-4 pt-6 space-y-6">
            
            <!-- Pesan Sukses -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @foreach($stores as $store)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    
                    <!-- Info Toko -->
                    <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-start">
                        <div>
                            <h3 class="font-extrabold text-gray-900 text-lg">{{ $store->nama }}</h3>
                            <p class="text-xs text-gray-500 mt-1">Status: 
                                @if($store->subscription_status === 'trial')
                                    <span class="font-bold text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded">Masa Trial</span>
                                @elseif($store->subscription_status === 'active')
                                    <span class="font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded">Aktif</span>
                                @elseif($store->subscription_status === 'discounted')
                                    <span class="font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Diskon 50%</span>
                                @else
                                    <span class="font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded">Diarsipkan</span>
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Berlaku Hingga</p>
                            <p class="font-bold text-sm {{ $store->active_until && $store->active_until->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $store->active_until ? $store->active_until->format('d M Y') : '-' }}
                            </p>
                        </div>
                    </div>

                    <!-- Daftar Tagihan Pending -->
                    <div class="p-5">
                        <h4 class="font-bold text-sm text-gray-900 mb-3">Tagihan Belum Lunas</h4>
                        
                        @php
                            $pendingBills = $store->subscriptions->where('status', 'pending');
                        @endphp

                        @if($pendingBills->isEmpty())
                            <div class="text-sm text-gray-500 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                Tidak ada tagihan tertunggak.
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($pendingBills as $bill)
                                    <div class="border border-gray-200 rounded-xl p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                        <div>
                                            <p class="font-bold text-sm text-gray-900">
                                                {{ $bill->type === 'open_fee' ? 'Biaya Pembukaan Toko' : 'Perpanjangan Langganan' }}
                                            </p>
                                            <p class="text-lg font-extrabold text-[#B9232D] mt-1">Rp {{ number_format($bill->amount, 0, ',', '.') }}</p>
                                            @if($bill->notes)
                                                <p class="text-xs text-gray-500 mt-1">{{ $bill->notes }}</p>
                                            @endif
                                        </div>

                                        <div class="w-full sm:w-auto text-right">
                                            @if($bill->payment_proof)
                                                <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1.5 rounded-lg w-full text-center">Menunggu Verifikasi Admin</span>
                                            @else
                                                <button @click="selectedBillId = {{ $bill->id }}; showUploadModal = true" class="bg-[#B9232D] text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-red-800 transition-colors w-full">Bayar / Upload Bukti</button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            @if($stores->isEmpty())
                <div class="text-center bg-white rounded-3xl p-10 border border-gray-100 shadow-sm">
                    <p class="text-gray-500 text-sm">Anda belum memiliki toko.</p>
                </div>
            @endif
        </div>

        <!-- Modal Upload Bukti -->
        <div x-show="showUploadModal" x-cloak class="fixed inset-0 z-[100] flex items-end justify-center sm:items-center">
            <div x-show="showUploadModal" x-transition.opacity class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showUploadModal = false"></div>
            
            <div x-show="showUploadModal" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-y-full sm:translate-y-8 sm:opacity-0"
                 x-transition:enter-end="translate-y-0 sm:opacity-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="translate-y-0 sm:opacity-100"
                 x-transition:leave-end="translate-y-full sm:translate-y-8 sm:opacity-0"
                 class="bg-white w-full max-w-lg sm:rounded-3xl rounded-t-3xl shadow-2xl relative z-10 p-6">
                
                <h3 class="text-lg font-extrabold text-gray-900 mb-4">Upload Bukti Transfer</h3>
                <p class="text-xs text-gray-500 mb-4">Silakan transfer ke <strong>Bank BCA 1234567890 a.n LapakDA</strong>, lalu unggah foto struk/bukti transfer Anda di bawah ini.</p>
                
                <form method="POST" :action="`/dashboard/subscription/${selectedBillId}/upload-proof`" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <input type="file" name="payment_proof" accept="image/*" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-red-50 file:text-[#B9232D] hover:file:bg-red-100 transition">
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click="showUploadModal = false" class="w-full bg-gray-100 text-gray-700 font-bold py-3 rounded-xl hover:bg-gray-200 transition">Batal</button>
                        <button type="submit" class="w-full bg-[#B9232D] text-white font-bold py-3 rounded-xl hover:bg-red-800 transition">Kirim Bukti</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Toast -->
        <div x-show="showToast" x-transition class="fixed top-20 left-4 right-4 z-[110] flex justify-center pointer-events-none" x-cloak>
            <div class="bg-gray-900 text-white px-4 py-3 rounded-xl shadow-2xl flex items-center gap-3 w-full max-w-sm pointer-events-auto">
                <div class="bg-red-500/20 p-1.5 rounded-lg flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                </div>
                <p class="text-sm font-medium flex-1" x-text="toastMessage"></p>
            </div>
        </div>
    </div>
</x-app-layout>