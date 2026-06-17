<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGateway;
use Illuminate\Support\Facades\Log;

class PaymentFactory
{
    public static function getAvailableMethods(): array
    {
        $methods = config('payment.payment_methods', []);

        return array_filter($methods, function ($method) {
            return $method['active'] ?? false;
        });
    }

    public static function getMethodDetails(string $methodId): ?array
    {
        $methods = config('payment.payment_methods', []);

        return $methods[$methodId] ?? null;
    }

    public static function isMethodActive(string $methodId): bool
    {
        $details = self::getMethodDetails($methodId);

        return $details && ($details['active'] ?? false);
    }

    public static function getGateway(string $methodId): PaymentGateway
    {
        $methodDetails = self::getMethodDetails($methodId);

        if (! $methodDetails) {
            throw new \InvalidArgumentException("Payment method '{$methodId}' not found.");
        }

        if (! ($methodDetails['active'] ?? false)) {
            throw new \InvalidArgumentException("Payment method '{$methodId}' is not active.");
        }

        $class = $methodDetails['class'] ?? null;

        if (! $class || ! class_exists($class)) {
            Log::error("Payment class not found for method: {$methodId}", [
                'class' => $class,
            ]);
            throw new \InvalidArgumentException("Payment class not configured for '{$methodId}'.");
        }

        $gateway = app($class);

        if (! $gateway instanceof PaymentGateway) {
            throw new \InvalidArgumentException(
                "Payment class for '{$methodId}' must implement ".PaymentGateway::class
            );
        }

        return $gateway;
    }

    public static function processPayment(
        string $methodId,
        \App\Models\Order $order,
        \Illuminate\Http\Request $request
    ): array {
        $gateway = self::getGateway($methodId);

        return $gateway->processPayment($order, $request);
    }

    public static function getActiveMethodIds(): array
    {
        return array_keys(self::getAvailableMethods());
    }
}
