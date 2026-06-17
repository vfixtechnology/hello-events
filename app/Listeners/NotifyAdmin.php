<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\User;
use App\Notifications\AdminBookingNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;

class NotifyAdmin implements ShouldQueue
{
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;

        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            $notifiable = new AnonymousNotifiable;
            $notifiable->route('mail', $admin->email);

            $notifiable->notify(new AdminBookingNotification($order));
        }
    }
}
