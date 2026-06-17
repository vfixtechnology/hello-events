<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Notifications\PaymentReceiptNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;

class SendPaymentReceipt implements ShouldQueue
{
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;

        $notifiable = new AnonymousNotifiable;
        $notifiable->route('mail', $order->buyer_email);

        $notifiable->notify(new PaymentReceiptNotification($order));
    }
}
