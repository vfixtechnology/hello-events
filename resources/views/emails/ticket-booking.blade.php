<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Event Tickets</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 700px; margin: 0 auto; }
        .header { background: #007bff; color: white; padding: 25px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 25px 20px; background: #f9f9f9; }
        
        .event-details { background: white; border: 1px solid #e5e5e5; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .event-details h3 { color: #007bff; margin-top: 0; }
        .event-details table { width: 100%; }
        .event-details table td { padding: 5px 0; }
        .event-details .label { font-weight: bold; color: #666; width: 120px; }
        
        .ticket { background: white; border: 1px solid #e5e5e5; padding: 20px; margin: 15px 0; border-radius: 8px; }
        .ticket-header { font-weight: bold; color: #007bff; margin-bottom: 15px; font-size: 16px; }
        .ticket-details table { width: 100%; }
        .ticket-details table td { padding: 4px 0; }
        .ticket-details .label { color: #666; width: 120px; }
        
        .qr-code { text-align: center; margin: 20px 0; padding: 15px; background: #f3f4f6; border-radius: 8px; }
        .qr-code img { border: 5px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        
        .pricing { background: white; border: 1px solid #e5e5e5; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .pricing table { width: 100%; }
        .pricing table td { padding: 8px 0; border-bottom: 1px solid #eee; }
        .pricing .total { font-size: 18px; font-weight: bold; color: #007bff; }
        
        .host-info { background: #f0f9ff; border: 1px solid #bae6fd; padding: 15px; border-radius: 8px; margin-top: 20px; }
        .host-info h4 { margin: 0 0 10px 0; color: #0369a1; }
        
        .footer { text-align: center; padding: 20px; background: #1f2937; color: #9ca3af; font-size: 13px; }
        .footer a { color: #9ca3af; text-decoration: underline; }
        .footer .brand { color: #fff; font-weight: bold; font-size: 14px; margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your Event Tickets</h1>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{ $recipientName ?? $order->buyer_name }}</strong>,</p>
            <p>Thank you for booking! Here are your tickets:</p>
            
            @if($event)
            <div class="event-details">
                <h3>{{ $event->title }}</h3>
                <table>
                    <tr>
                        <td class="label">Date & Time:</td>
                        <td>{{ $event->start_datetime->format('D, M d, Y \a\t g:i A') }}</td>
                    </tr>
                    @if($event->end_datetime)
                    <tr>
                        <td class="label">End Date:</td>
                        <td>{{ $event->end_datetime->format('D, M d, Y \a\t g:i A') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Venue:</td>
                        <td>{{ $event->venue }}</td>
                    </tr>
                    @if($event->location)
                    <tr>
                        <td class="label">Location:</td>
                        <td>{{ $event->location }}</td>
                    </tr>
                    @endif
                    @if($event->city || $event->state)
                    <tr>
                        <td class="label">City/State:</td>
                        <td>{{ $event->city }}{{ $event->city && $event->state ? ', ' : '' }}{{ $event->state }}</td>
                    </tr>
                    @endif
                    @if($event->full_address)
                    <tr>
                        <td class="label">Address:</td>
                        <td>{{ $event->full_address }}</td>
                    </tr>
                    @endif
                    @if($event->map_link)
                    <tr>
                        <td class="label">Map Link:</td>
                        <td><a href="{{ $event->map_link }}" target="_blank" style="display: inline-block; padding: 8px 16px; background: #10B981; color: white; text-decoration: none; border-radius: 6px; font-size: 12px;">View on Google Map</a></td>
                    </tr>
                    @endif
                </table>
                
                @if($event->host_name)
                <div class="host-info">
                    <h4>Event Host: {{ $event->host_name }}</h4>
                    @if($event->host_email)<p style="margin: 5px 0;">Email: {{ $event->host_email }}</p>@endif
                    @if($event->host_phone)<p style="margin: 5px 0;">Phone: {{ $event->host_phone }}</p>@endif
                </div>
                @endif
            </div>
            @endif
            
            @foreach($tickets as $ticket)
            <div class="ticket">
                <div class="ticket-header">Ticket #{{ $loop->iteration }}</div>
                <div class="ticket-details">
                    <table>
                        <tr>
                            <td class="label">Attendee:</td>
                            <td>{{ $ticket['attendee_name'] }}</td>
                        </tr>
                        <tr>
                            <td class="label">Email:</td>
                            <td>{{ $ticket['attendee_email'] }}</td>
                        </tr>
                        @if(isset($ticket['attendee_phone']) && $ticket['attendee_phone'])
                        <tr>
                            <td class="label">Phone:</td>
                            <td>{{ $ticket['attendee_phone'] }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="label">Ticket ID:</td>
                            <td>{{ $ticket['uuid'] }}</td>
                        </tr>
                        @if(isset($ticket['add_ons']) && count($ticket['add_ons']) > 0)
                        <tr>
                            <td class="label" style="vertical-align: top;">Add-ons:</td>
                            <td>
                                @foreach($ticket['add_ons'] as $addOn)
                                <span style="display: inline-block; background: #e0f2fe; padding: 3px 10px; border-radius: 4px; font-size: 12px; margin: 2px 4px 2px 0; color: #0369a1;">
                                    {{ $addOn['title'] }}: {{ Number::currency($addOn['price'], config('app.currency')) }}
                                </span>
                                @endforeach
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
                
                @if(isset($qrCodes[$ticket['uuid']]))
                <div class="qr-code">
                    <img src="data:image/svg+xml;base64,{{ $qrCodes[$ticket['uuid']] }}" alt="QR Code" width="150">
                    <p style="margin: 10px 0 0 0; font-size: 12px; color: #666;">Show this QR code at the venue for check-in</p>
                </div>
                @endif
            </div>
            @endforeach
            
            <div class="pricing">
                @php 
                    $ticketPrice = $order->subtotal / $order->tickets->count();
                @endphp
                <table>
                    <tr>
                        <td>Ticket ({{ count($tickets) }} x {{ Number::currency($ticketPrice, config('app.currency')) }})</td>
                        <td style="text-align: right;">{{ Number::currency($order->subtotal - $order->tickets->sum(fn($t) => $t->addOns->sum('price')), config('app.currency')) }}</td>
                    </tr>
                    @if($order->tickets->sum(fn($t) => $t->addOns->sum('price')) > 0)
                    <tr>
                        <td>Add-ons Total</td>
                        <td style="text-align: right;">{{ Number::currency($order->tickets->sum(fn($t) => $t->addOns->sum('price')), config('app.currency')) }}</td>
                    </tr>
                    @endif
                    @if($order->discount_amount > 0)
                    <tr>
                        <td>Discount</td>
                        <td style="text-align: right; color: #10B981;">-{{ Number::currency($order->discount_amount, config('app.currency')) }}</td>
                    </tr>
                    @endif
                    @if($order->tax_amount > 0)
                    <tr>
                        <td>Tax</td>
                        <td style="text-align: right;">{{ Number::currency($order->tax_amount, config('app.currency')) }}</td>
                    </tr>
                    @endif
                    <tr class="total">
                        <td>Total Paid</td>
                        <td style="text-align: right;">{{ Number::currency($order->grand_total, config('app.currency')) }}</td>
                    </tr>
                </table>
            </div>
            
            <p style="margin-top: 20px;">
                <strong>Order Number:</strong> {{ $order->order_number }}<br>
                <strong>Payment Method:</strong> {{ $order->payment_method === 'cash_on_delivery' ? 'Pay at Event' : ucfirst(str_replace('_', ' ', $order->payment_method)) }}
            </p>
        </div>
        
        <div class="footer">
            <p class="brand">{{ config('app.name', 'helloEvents') }}</p>
            <p>Thank you for using our service!</p>
            <p style="margin-top: 10px;">Designed & Developed By <a href="https://www.vfixtechnology.com" target="_blank" rel="noopener">VFIX TECHNOLOGY</a></p>
        </div>
    </div>
</body>
</html>
