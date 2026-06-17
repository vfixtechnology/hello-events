<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 700px; margin: 0 auto; }
        .header { background: #10B981; color: white; padding: 25px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 25px 20px; background: #f9f9f9; }
        
        .receipt-box { background: white; border: 1px solid #e5e5e5; padding: 20px; border-radius: 8px; }
        .receipt-box h3 { color: #10B981; margin-top: 0; }
        
        .billing-details { background: #f0fdf4; border: 1px solid #bbf7d0; padding: 15px; border-radius: 8px; margin-top: 20px; }
        .billing-details h4 { margin: 0 0 10px 0; color: #166534; }
        
        .invoice-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .invoice-table th, .invoice-table td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e5e5; }
        .invoice-table th { background: #f3f4f6; font-weight: 600; }
        .invoice-table .total-row { background: #f0fdf4; font-weight: bold; font-size: 18px; }
        
        .footer { text-align: center; padding: 20px; background: #1f2937; color: #9ca3af; font-size: 13px; }
        .footer a { color: #9ca3af; text-decoration: underline; }
        .footer .brand { color: #fff; font-weight: bold; font-size: 14px; margin-bottom: 5px; }
    </style>
</head>
<body>
    @php 
        $ticketTotal = $order->subtotal - $order->tickets->sum(fn($t) => $t->addOns->sum('price'));
    @endphp
    <div class="container">
        <div class="header">
            <h1>Payment Receipt</h1>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $order->buyer_name }}</strong>,</p>
            <p>Your payment has been successfully processed. Thank you for your booking!</p>
            
            <div class="receipt-box">
                <h3>Order Summary</h3>
                @if(isset($event) && $event)
                <div style="background: #f9fafb; padding: 12px; border-radius: 6px; margin-bottom: 15px;">
                    <p style="margin: 0; font-weight: bold; color: #4F46E5;">{{ $event->title }}</p>
                    <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">
                        {{ $event->start_datetime->format('D, M d, Y \a\t g:i A') }} | {{ $event->venue }}
                    </p>
                    @if($event->map_link)
                    <p style="margin: 5px 0 0 0;">
                        <a href="{{ $event->map_link }}" target="_blank" style="display: inline-block; padding: 5px 12px; background: #10B981; color: white; text-decoration: none; border-radius: 4px; font-size: 11px;">View on Google Map</a>
                    </p>
                    @endif
                </div>
                @endif
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th style="text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <strong>Tickets</strong><br>
                                <span style="color: #666; font-size: 12px;">
                                    @php $ticketType = $order->tickets->first()->ticketType ?? null; @endphp
                                    {{ $ticketType->title ?? 'Event Ticket' }} ({{ $order->tickets->count() }} x {{ Number::currency($ticketTotal / max($order->tickets->count(), 1), config('app.currency')) }})
                                </span>
                            </td>
                            <td style="text-align: right;">{{ Number::currency($ticketTotal, config('app.currency')) }}</td>
                        </tr>
                        
                        @php $addOnsTotal = 0; @endphp
                        @foreach($order->tickets as $ticket)
                            @foreach($ticket->addOns as $addOn)
                                @php $addOnsTotal += $addOn->price; @endphp
                                <tr>
                                    <td>
                                        <strong>Add-on</strong><br>
                                        <span style="color: #666; font-size: 12px;">{{ $addOn->title }}</span>
                                    </td>
                                    <td style="text-align: right;">{{ Number::currency($addOn->price, config('app.currency')) }}</td>
                                </tr>
                            @endforeach
                        @endforeach

                        @if($order->discount_amount > 0)
                        <tr>
                            <td><strong>Discount</strong></td>
                            <td style="text-align: right; color: #10B981;">-{{ Number::currency($order->discount_amount, config('app.currency')) }}</td>
                        </tr>
                        @endif
                        
                        @if($order->tax_amount > 0)
                        <tr>
                            <td><strong>Tax</strong></td>
                            <td style="text-align: right;">{{ Number::currency($order->tax_amount, config('app.currency')) }}</td>
                        </tr>
                        @endif
                        
                        <tr class="total-row">
                            <td>Total Paid</td>
                            <td style="text-align: right; color: #10B981;">{{ Number::currency($order->grand_total, config('app.currency')) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 20px; padding: 15px; background: white; border: 1px solid #e5e5e5; border-radius: 8px;">
                <h4 style="color: #4F46E5; margin: 0 0 15px 0;">Attendees & Add-ons</h4>
                @foreach($order->tickets as $ticket)
                <div style="padding: 12px; background: #f9fafb; border-radius: 6px; margin-bottom: 10px;">
                    <p style="margin: 0 0 5px 0; font-weight: bold; color: #333;">
                        {{ $loop->iteration }}. {{ $ticket->attendee_name }}
                        <span style="font-weight: normal; color: #666;">({{ $ticket->attendee_email }})</span>
                    </p>
                    @if($ticket->addOns->count() > 0)
                        <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">
                            <strong>Add-ons:</strong>
                                @foreach($ticket->addOns as $addOn)
                                        <span style="display: inline-block; background: #e0f2fe; padding: 2px 8px; border-radius: 4px; margin-right: 5px;">
                                            {{ $addOn->title }}: {{ Number::currency($addOn->price, config('app.currency')) }}
                                        </span>
                                    @endforeach
                        </p>
                    @endif
                </div>
                @endforeach
            </div>
            
            <div class="billing-details">
                <h4>Billing Details</h4>
                <p style="margin: 5px 0;"><strong>Name:</strong> {{ $order->buyer_name }}</p>
                <p style="margin: 5px 0;"><strong>Email:</strong> {{ $order->buyer_email }}</p>
                @if($order->buyer_phone)
                <p style="margin: 5px 0;"><strong>Phone:</strong> {{ $order->buyer_phone }}</p>
                @endif
                @if($order->address)
                <p style="margin: 5px 0;"><strong>Address:</strong> {{ $order->address }}, {{ $order->city }}, {{ ucwords($order->state) }}, {{ ucwords($order->country) }} - {{ $order->pincode }}</p>
                @endif
                <p style="margin: 10px 0 0 0;"><strong>Order Number:</strong> {{ $order->order_number }}</p>
                <p style="margin: 5px 0;"><strong>Payment Method:</strong> {{ $order->payment_method === 'cash_on_delivery' ? 'Pay at Event' : ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                <p style="margin: 5px 0;"><strong>Payment ID:</strong> {{ $order->payment_id ?? 'N/A' }}</p>
            </div>
        </div>
        
        <div class="footer">
            <p class="brand">{{ config('app.name', 'helloEvents') }}</p>
            <p>Thank you for your purchase!</p>
            <p style="margin-top: 10px;">Designed & Developed By <a href="https://www.vfixtechnology.com" target="_blank" rel="noopener">VFIX TECHNOLOGY</a></p>
        </div>
    </div>
</body>
</html>
