<?php

namespace App\Contracts;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGateway
{
    public function processPayment(Order $order, Request $request): array;

    public function verifyPayment(Request $request): bool;

    public function getMethodId(): string;

    public function getTitle(): string;
}
