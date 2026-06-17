<?php

namespace App\Notifications;

use App\Helpers\QrCodeHelper;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketBookingNotification extends Notification
{
    use Queueable;

    public array $tickets;

    public Order $order;

    public string $attendeeName;

    public array $qrCodes = [];

    public $event = null;

    public function __construct(array $tickets, Order $order, string $attendeeName)
    {
        $this->tickets = $tickets;
        $this->order = $order;
        $this->attendeeName = $attendeeName;
        $this->event = $order->tickets->first()->ticketType->event ?? null;

        foreach ($tickets as $ticket) {
            $this->qrCodes[$ticket['uuid']] = QrCodeHelper::generateBase64($ticket['uuid'], 200);
        }
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $mail = (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Your Event Tickets - '.($this->event?->title ?? 'Event'))
            ->view('emails.ticket-booking', [
                'tickets' => $this->tickets,
                'order' => $this->order,
                'event' => $this->event,
                'qrCodes' => $this->qrCodes,
                'recipientName' => $this->attendeeName,
            ]);

        foreach ($this->tickets as $ticket) {
            $pdf = Pdf::loadView('pdf.ticket', [
                'ticket' => $ticket,
                'event' => $this->event,
                'qrCode' => $this->qrCodes[$ticket['uuid']] ?? '',
            ])->setPaper('a4', 'portrait')->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => true,
            ]);

            $mail->attachData($pdf->output(), 'ticket-'.$ticket['uuid'].'.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_number' => $this->order->order_number,
            'total' => $this->order->grand_total,
            'message' => 'Your tickets have been booked successfully!',
        ];
    }
}
