<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderMessage;
use App\Notifications\NewChatNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function show(Order $order)
    {
        $this->authorizeChat($order);
        $this->markMessagesAsRead($order);

        // Ambil pesan
        $messages = $order->messages;

        // Tentukan lawan bicara
        $lawanBicara = (Auth::id() === $order->user_id) ? $order->store->owner : $order->user;

        return view('chat.show', compact('order', 'messages', 'lawanBicara'));
    }

    public function send(Request $request, Order $order)
    {
        $this->authorizeChat($order);

        // --- VALIDASI TIMER & ATURAN CHAT ---
        if ($order->status === 'selesai') {
            // 1. Tolak jika sesi sudah berakhir (lewat 5 jam)
            if ($order->chat_expires_at && now()->greaterThan($order->chat_expires_at)) {
                return response()->json(['error' => 'Sesi obrolan telah berakhir (melebihi 5 jam).'], 403);
            }
            
            // 2. Tolak jika Penjual mencoba chat duluan
            if (is_null($order->chat_expires_at) && Auth::id() === $order->store->owner_id) {
                return response()->json(['error' => 'Tunggu pembeli mengirim pesan terlebih dahulu.'], 403);
            }
        }
        // ------------------------------------

        $request->validate([
            'message' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:5120',
        ]);

        if (!$request->filled('message') && !$request->hasFile('image')) {
            return response()->json(['error' => 'Pesan tidak boleh kosong'], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->prosesDanSimpanFoto($request->file('image'));
        }

        // Simpan pesan
        $msg = OrderMessage::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'image_path' => $imagePath,
        ]);

        // --- LOGIKA NOTIFIKASI CHAT ---
        // Tentukan penerima: Jika yang kirim Penjual, tembak ke Pembeli. Jika Pembeli/Guest, tembak ke Penjual.
        $isSeller = Auth::id() === $order->store->owner_id;
        $recipient = $isSeller ? $order->user : $order->store->owner;
        
        // Tentukan nama pengirim untuk judul notif (Gunakan nama toko jika pengirim adalah penjual)
        $senderName = Auth::check() ? Auth::user()->name : 'Pembeli (Guest)';
        if (Auth::id() === $order->store->owner_id) {
            $senderName = $order->store->nama;
        }

        // Kirim push notification jika penerima valid (Pembeli login / Penjual punya akun)
        if ($recipient) {
            $recipient->notify(new NewChatNotification($msg, $order, $senderName));
        }
        // ------------------------------

        // Logika Timer 5 Jam (Hanya memicu jika order selesai & belum ada timer)
        if ($order->status === 'selesai' && is_null($order->chat_expires_at)) {
            $order->update([
                'chat_expires_at' => now()->addHours(5),
                'chat_initiated_by' => Auth::id() === $order->user_id ? 'buyer' : 'seller'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $msg->load('user')
        ]);
    }

    public function fetchMessages(Order $order)
    {
        $this->authorizeChat($order);
        $this->markMessagesAsRead($order);
        
        $messages = $order->messages()->with('user')->get()->map(function($msg) use ($order) {
            // Tentukan nama tampilan: Nama Toko (jika penjual) atau Nama User/Guest
            if ($msg->user_id === $order->store->owner_id) {
                $msg->display_name = $order->store->nama;
            } else {
                $msg->display_name = $msg->user ? $msg->user->name : 'Pembeli (Guest)';
            }
            return $msg;
        });
        
        return response()->json($messages);
    }

    public function badgeCount()
    {
        $authId = Auth::id();
        $storeIds = Auth::user()->stores()->pluck('id');
        
        $pendingCount = Order::whereIn('store_id', $storeIds)->where('status', 'menunggu_konfirmasi')->count();
        
        $unreadChatCount = OrderMessage::whereHas('order', function($q) use ($authId, $storeIds) {
            $q->where('user_id', $authId)->orWhereIn('store_id', $storeIds);
        })->where('is_read', false)
          ->where(function($q) use ($authId) {
              $q->where('user_id', '!=', $authId)->orWhereNull('user_id');
          })->count();

        // Ambil jumlah unread per order_id untuk update badge di halaman riwayat
        $unreadPerOrder = OrderMessage::whereHas('order', function($q) use ($authId, $storeIds) {
            $q->where('user_id', $authId)->orWhereIn('store_id', $storeIds);
        })->where('is_read', false)
          ->where(function($q) use ($authId) {
              $q->where('user_id', '!=', $authId)->orWhereNull('user_id');
          })
          ->selectRaw('order_id, count(*) as count')
          ->groupBy('order_id')
          ->pluck('count', 'order_id');

        // Ambil ID order terbaru untuk deteksi orderan masuk (khusus penjual)
        $latestOrderId = Order::whereIn('store_id', $storeIds)->max('id') ?? 0;
          
        return response()->json([
            'total' => $pendingCount + $unreadChatCount,
            'unread_per_order' => $unreadPerOrder,
            'latest_order_id' => $latestOrderId
        ]);
    }

    private function authorizeChat(Order $order)
    {
        $isBuyer = Auth::id() === $order->user_id;
        $isSeller = Auth::id() === $order->store->owner_id;
        
        // Cek Fallback: Jika pesanan ini milik Guest (user_id null), cocokkan session_id
        $isGuestBuyer = is_null($order->user_id) && session()->getId() === $order->session_id;

        if (!$isBuyer && !$isSeller && !$isGuestBuyer) {
            abort(403, 'Anda tidak berhak mengakses obrolan ini.');
        }
    }

    private function markMessagesAsRead(Order $order): void
    {
        if (!Auth::check()) {
            return;
        }

        $order->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    private function prosesDanSimpanFoto($file): string
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        $image->scaleDown(width: 800, height: 800);
        $image->pad(800, 800, 'ffffff');
        $namaFile = 'chat/' . Str::uuid() . '.webp';
        Storage::disk('public')->put($namaFile, (string) $image->toWebp(80));
        return $namaFile;
    }
}