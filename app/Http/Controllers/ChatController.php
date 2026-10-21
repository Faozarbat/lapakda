<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderMessage;
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

        // Ambil pesan
        $messages = $order->messages;

        // Tentukan lawan bicara
        $lawanBicara = (Auth::id() === $order->user_id) ? $order->store->owner : $order->user;

        return view('chat.show', compact('order', 'messages', 'lawanBicara'));
    }

    public function send(Request $request, Order $order)
    {
        $this->authorizeChat($order);

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
        $messages = $order->messages()->with('user')->get();
        
        return response()->json($messages);
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