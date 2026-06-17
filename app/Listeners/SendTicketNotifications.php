<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Notifications\TicketBookingNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;

class SendTicketNotifications implements ShouldQueue
{
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        $tickets = $order->tickets()->with(['ticketType', 'ticketType.event', 'addOns'])->get();

        $sentEmails = [];

        foreach ($tickets as $ticket) {
            $email = $ticket->attendee_email;

            if (in_array($email, $sentEmails)) {
                continue;
            }
            $sentEmails[] = $email;

            $attendeeName = $ticket->attendee_name;
            $attendeeTickets = $tickets->where('attendee_email', $email)->values()->toArray();

            $notifiable = new AnonymousNotifiable;
            $notifiable->route('mail', $email);

            $notifiable->notify(new TicketBookingNotification($attendeeTickets, $order, $attendeeName));
        }
    }
}
