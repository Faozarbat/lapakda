<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class NewChatNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $order;
    protected $senderName;

    public function __construct($message, $order, $senderName)
    {
        $this->message = $message;
        $this->order = $order;
        $this->senderName = $senderName;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        // Jika isi chat kosong (hanya kirim gambar), tampilkan teks default
        $bodyText = $this->message->message ?: '[Mengirim Gambar]';

        return (new WebPushMessage)
            ->title("Chat dari {$this->senderName}")
            ->icon('/icons/icon-192.png')
            ->body($bodyText)
            ->action('Balas Chat', route('chat.show', $this->order->id))
            ->data(['url' => route('chat.show', $this->order->id)]);
    }
}