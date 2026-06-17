<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGateway;
use App\Models\Order;
use Illuminate\Http\Request;

class CashOnDelivery implements PaymentGateway
{
    public function processPayment(Order $order, Request $request): array
    {
        // For Pay at Event, we just mark the order as pending
        // Payment will be collected at the venue
        $order->update([
            'status' => 'pending',
            'payment_method' => $this->getMethodId(),
            'payment_id' => 'COD-'.strtoupper(uniqid()),
        ]);

        return [
            'success' => true,
            'message' => 'Order placed successfully. Pay at the event venue.',
            'payment_id' => $order->payment_id,
        ];
    }

    public function verifyPayment(Request $request): bool
    {
        // COD doesn't need verification - always returns true
        return true;
    }

    public function getMethodId(): string
    {
        return 'cash_on_delivery';
    }

    public function getTitle(): string
    {
        return 'Pay at Event';
    }
}
