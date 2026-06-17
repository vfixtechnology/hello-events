<?php

namespace App\Notifications;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentReceiptNotification extends Notification
{
    use Queueable;

    public Order $order;

    public $event = null;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->event = $order->tickets->first()->ticketType->event ?? null;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        $pdf = Pdf::loadView('pdf.receipt', [
            'order' => $this->order,
            'event' => $this->event,
        ])->setPaper('a4', 'portrait')->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => true,
        ]);

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Payment Receipt - Order #'.$this->order->order_number)
            ->view('emails.payment-receipt', [
                'order' => $this->order,
                'event' => $this->event,
            ])
            ->attachData($pdf->output(), 'receipt-'.$this->order->order_number.'.pdf', [
                'mime' => 'application/pdf',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_number' => $this->order->order_number,
            'amount' => $this->order->grand_total,
            'payment_method' => $this->order->payment_method,
            'message' => 'Payment receipt for your order',
        ];
    }
}
