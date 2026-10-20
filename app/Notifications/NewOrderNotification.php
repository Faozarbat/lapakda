<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $store;

    public function __construct($order, $store)
    {
        $this->order = $order;
        $this->store = $store;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('Pesanan Baru Masuk!')
            ->icon('/icons/icon-192.png')
            ->body("Ada pesanan #{$this->order->nomor_order} di {$this->store->nama}. Segera cek dashboard!")
            ->action('Lihat Pesanan', route('dashboard.order.index', $this->store->id))
            ->data(['url' => route('dashboard.order.index', $this->store->id)]);
    }
}