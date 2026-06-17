<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Methods Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the available payment methods for your application.
    | To add a new payment method:
    | 1. Add the class implementation in app/Services/Payment/
    | 2. Add the payment details below with active => true
    |
    | Fields:
    | - title: Display name
    | - description: Brief description shown to user
    | - image: Image URL for the payment option
    | - class: Full namespace of the payment class
    | - active: Enable/disable this payment method
    |
    */

    'payment_methods' => [
        'cash_on_delivery' => [
            'title' => 'Pay at Event',
            'description' => 'Pay with cash or card on delivery or at the venue.',
            'image' => '/cod.png',
            'class' => \App\Services\Payment\CashOnDelivery::class,
            'active' => true,
        ],

        // 'razorpay' => [
        //     'title' => 'Razorpay',
        //     'description' => 'Pay securely online via UPI/card or Net banking',
        //     'image' => '/razorpay.png',
        //     'class' => \Vfixtechnology\RazorpayPayment\RazorpayPaymentGateway::class,
        //     'active' => true,
        // ],

        // 'stripe' => [
        //     'title' => 'Stripe',
        //     'description' => 'Pay securely with credit/debit card via Stripe',
        //     'image' => '/stripe.png',
        //     'class' => \Vfixtechnology\StripePayment\StripePaymentGateway::class,
        //     'active' => true,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Payment Settings
    |--------------------------------------------------------------------------
    */
    'default' => [
        'currency' => env('CURRENCY_CODE', 'INR'),
        'currency_symbol' => env('CURRENCY_SYMBOL', '₹'),
    ],
];
