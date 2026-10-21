<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#B9232D">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat — {{ $order->nomor_order }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#E5DDD5] text-gray-800 antialiased">

    <!-- HEADER DIPAKU DI ATAS -->
    <header class="bg-[#B9232D] text-white shadow-md z-20 fixed top-0 left-0 right-0">
        <div class="max-w-3xl mx-auto px-4 h-full flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('order.riwayat') }}" class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
                </a>
                <div class="flex flex-col">
                    <h1 class="font-bold text-sm leading-tight">{{ $lawanBicara->name ?? 'Pengguna' }}</h1>
                    <span class="text-[10px] text-red-100">Order #{{ $order->nomor_order }}</span>
                </div>
            </div>
            
            <div class="text-right">
                <span class="text-[10px] font-bold bg-white/20 px-2 py-1 rounded-md">{{ $order->store->nama }}</span>
            </div>
        </div>
    </header>

    <!-- AREA CHAT (DIBERI PADDING AGAR TIDAK TERTUTUP HEADER/FOOTER) -->
    <main class="h-screen overflow-y-auto p-4 pt-20 pb-24 hide-scrollbar" id="chat-container">
        <div class="max-w-3xl mx-auto px-4 space-y-4" id="chat-messages">
            <!-- Render Pesan Awal via Blade -->
            @foreach($messages as $msg)
                @php $isMe = auth()->id() === $msg->user_id; @endphp
                <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] rounded-2xl px-4 py-2.5 shadow-sm {{ $isMe ? 'bg-[#DCF8C6] rounded-tr-none' : 'bg-white rounded-tl-none' }}">
                        @if(!$isMe)
                            <p class="text-[10px] font-bold text-[#B9232D] mb-1">{{ $msg->user->name }}</p>
                        @endif
                        
                        @if($msg->image_path)
                            <img src="{{ Storage::url($msg->image_path) }}" class="w-full max-w-[200px] rounded-lg mb-2 cursor-pointer" onclick="window.open(this.src, '_blank')">
                        @endif
                        
                        @if($msg->message)
                            <p class="text-sm text-gray-800 break-words">{{ $msg->message }}</p>
                        @endif
                        
                        <p class="text-[9px] text-gray-500 text-right mt-1">{{ $msg->created_at->format('H:i') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    <!-- FORM INPUT DIPAKU DI BAWAH -->
    @php
        $canChat = true;
        $alasanTutup = '';

        if ($order->status === 'selesai') {
            if (now()->greaterThan($order->chat_expires_at) && $order->chat_expires_at !== null) {
                $canChat = false;
                $alasanTutup = 'Sesi obrolan telah berakhir (melebihi 5 jam).';
            } elseif (is_null($order->chat_expires_at) && auth()->id() === $order->store->owner_id) {
                $canChat = false;
                $alasanTutup = 'Tunggu pembeli mengirim pesan terlebih dahulu.';
            }
        }
    @endphp

<div class="bg-white border-t border-gray-200 p-3 pb-[calc(0.75rem+env(safe-area-inset-bottom))] fixed bottom-0 left-0 right-0 z-30">        <div class="max-w-3xl mx-auto">
            @if($canChat)
                <form id="chat-form" class="flex items-center gap-2">
                    <label class="cursor-pointer p-2 text-gray-500 hover:text-[#B9232D] transition-colors rounded-full hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                        <input type="file" id="chat-image" name="image" accept="image/*" class="hidden">
                    </label>
                    
                    <input type="text" id="chat-input" placeholder="Ketik pesan..." class="flex-grow bg-gray-100 border-none rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#B9232D]">
                    
                    <!-- IKON PAPER PLANE YANG BENAR (MIRING KE KANAN) -->
                    <button type="submit" class="w-11 h-11 bg-[#B9232D] text-white rounded-full flex items-center justify-center shadow-md hover:bg-red-800 transition-colors flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                        </svg>
                    </button>
                </form>
                
                <div id="image-preview" class="hidden mt-2 p-2 bg-gray-50 border border-gray-200 rounded-lg flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-600">Gambar siap dikirim</span>
                    <button type="button" id="cancel-image" class="text-red-500 hover:text-red-700 text-xs font-bold">Batal</button>
                </div>
            @else
                <div class="text-center py-2 text-sm font-bold text-gray-500 bg-gray-100 rounded-xl">
                    {{ $alasanTutup }}
                </div>
            @endif
        </div>
    </div>

    <script>
        const orderId = {{ $order->id }};
        const authId = {{ auth()->id() }};
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const chatContainer = document.getElementById('chat-container');
        const messagesDiv = document.getElementById('chat-messages');
        const imageInput = document.getElementById('chat-image');
        const imagePreview = document.getElementById('image-preview');
        let selectedFile = null;

        // Scroll ke bawah saat pertama kali buka
        chatContainer.scrollTop = chatContainer.scrollHeight;

        // Handle pilih gambar
        if(imageInput) {
            imageInput.addEventListener('change', function(e) {
                if(e.target.files[0]) {
                    selectedFile = e.target.files[0];
                    imagePreview.classList.remove('hidden');
                }
            });
        }

        if(document.getElementById('cancel-image')) {
            document.getElementById('cancel-image').addEventListener('click', function() {
                selectedFile = null;
                imageInput.value = '';
                imagePreview.classList.add('hidden');
            });
        }

        // Kirim Pesan
        if(chatForm) {
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const text = chatInput.value.trim();
                if(!text && !selectedFile) return;

                const formData = new FormData();
                formData.append('message', text);
                if(selectedFile) {
                    formData.append('image', selectedFile);
                }

                // Optimistic UI (Sementara)
                chatInput.value = '';
                selectedFile = null;
                imageInput.value = '';
                imagePreview.classList.add('hidden');

                fetch(`/chat/${orderId}/send`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    fetchMessages(); // Tarik pesan terbaru
                });
            });
        }

        // Polling: Ambil Pesan Baru tiap 3 detik
        function fetchMessages() {
            fetch(`/chat/${orderId}/fetch`)
            .then(res => res.json())
            .then(messages => {
                renderMessages(messages);
            });
        }

        function renderMessages(messages) {
            let html = '';
            messages.forEach(msg => {
                const isMe = msg.user_id === authId;
                const imgHtml = msg.image_path ? `<img src="/storage/${msg.image_path}" class="w-full max-w-[200px] rounded-lg mb-2" onclick="window.open(this.src, '_blank')">` : '';
                const textHtml = msg.message ? `<p class="text-sm text-gray-800 break-words">${msg.message}</p>` : '';
                const nameHtml = !isMe ? `<p class="text-[10px] font-bold text-[#B9232D] mb-1">${msg.user.name}</p>` : '';
                
                // Format jam
                const date = new Date(msg.created_at);
                const time = date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');

                html += `
                    <div class="flex ${isMe ? 'justify-end' : 'justify-start'}">
                        <div class="max-w-[80%] rounded-2xl px-4 py-2.5 shadow-sm ${isMe ? 'bg-[#DCF8C6] rounded-tr-none' : 'bg-white rounded-tl-none'}">
                            ${nameHtml}
                            ${imgHtml}
                            ${textHtml}
                            <p class="text-[9px] text-gray-500 text-right mt-1">${time}</p>
                        </div>
                    </div>
                `;
            });
            
            // Hanya render ulang jika ada perubahan jumlah pesan (mencegah layar lompat-lompat)
            if(messagesDiv.children.length !== messages.length) {
                messagesDiv.innerHTML = html;
                chatContainer.scrollTop = chatContainer.scrollHeight; // Auto scroll ke bawah
            }
        }

        // Jalankan Polling
        setInterval(fetchMessages, 3000);
    </script>
</body>
</html>