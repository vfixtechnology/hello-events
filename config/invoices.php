<?php

return [
    'date' => [
        'format' => 'Y-m-d',
        'pay_until_days' => 7,
    ],

    'serial_number' => [
        'series' => 'INV',
        'sequence' => 1,
        'sequence_padding' => 5,
        'delimiter' => '.',
        'format' => '{SERIES}{DELIMITER}{SEQUENCE}',
    ],

    'currency' => [
        'code' => env('PAYMENT_CURRENCY', 'INR'),
        'fraction' => 'paise',
        'symbol' => env('PAYMENT_CURRENCY_SYMBOL', '&#8377;'),
        'decimals' => 2,
        'decimal_point' => '.',
        'thousands_separator' => ',',
        'format' => '{SYMBOL}{VALUE}',
    ],

    'paper' => [
        'size' => 'a4',
        'orientation' => 'portrait',
    ],

    'disk' => 'local',

    'seller' => [
        'class' => \LaravelDaily\Invoices\Classes\Seller::class,
        'attributes' => [
            'name' => env('APP_NAME', 'helloEvents'),
            'address' => env('APP_ADDRESS', 'Event Venue'),
            'code' => env('SELLER_CODE', 'EVENT001'),
            'vat' => env('SELLER_VAT', ''),
            'phone' => env('SELLER_PHONE', ''),
            'custom_fields' => [
                'Email' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
            ],
        ],
    ],

    'dompdf_options' => [
        'enable_php' => true,
        'logOutputFile' => '/dev/null',
        'defaultFont' => 'DejaVu Sans',
    ],
];
