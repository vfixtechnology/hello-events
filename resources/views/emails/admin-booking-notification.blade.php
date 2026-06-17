<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Booking Notification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 700px; margin: 0 auto; }
        .header { background: #0d6efd; color: white; padding: 25px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 25px 20px; background: #f9f9f9; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th, table td { padding: 10px 8px; text-align: left; border-bottom: 1px solid #e5e5e5; font-size: 13px; }
        table th { background: #f3f4f6; font-weight: 600; color: #374151; }
        .total { font-size: 18px; font-weight: bold; color: #EF4444; }
        .status { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .status-completed { background: #d1fae5; color: #059669; }
        .status-pending { background: #fef3c7; color: #d97706; }
        
        .ticket-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .ticket-table th, .ticket-table td { padding: 10px 8px; text-align: left; border-bottom: 1px solid #e5e5e5; font-size: 12px; }
        .ticket-table th { background: #f3f4f6; font-weight: 600; }
        .ticket-table .attendee-name { font-weight: bold; color: #4F46E5; }
        .addon-badge { display: inline-block; background: #e0f2fe; padding: 2px 8px; border-radius: 4px; font-size: 10px; margin-right: 4px; color: #0369a1; }
        
        .billing-info { background: white; padding: 15px; border-radius: 8px; margin-top: 15px; }
        .billing-info h4 { margin: 0 0 10px 0; color: #374151; }
        
        .footer { text-align: center; padding: 20px; background: #1f2937; color: #9ca3af; font-size: 13px; }
        .footer a { color: #9ca3af; text-decoration: underline; }
        .footer .brand { color: #fff; font-weight: bold; font-size: 14px; margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Booking Received</h1>
        </div>
        
        <div class="content">
            <p>A new booking has been placed on your website:</p>
            
            <table>
                <tr>
                    <th>Order Number</th>
                    <td><strong>{{ $order->order_number }}</strong></td>
                </tr>
                <tr>
                    <th>Buyer Name</th>
                    <td>{{ $order->buyer_name }}</td>
                </tr>
                <tr>
                    <th>Buyer Email</th>
                    <td>{{ $order->buyer_email }}</td>
                </tr>
                <tr>
                    <th>Buyer Phone</th>
                    <td>{{ $order->buyer_phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Payment Method</th>
                    <td>{{ $order->payment_method === 'cash_on_delivery' ? 'Pay at Event' : ucfirst(str_replace('_', ' ', $order->payment_method)) }}</td>
                </tr>
                <tr>
                    <th>Payment Status</th>
                    <td>
                        <span class="status {{ $order->status === 'completed' ? 'status-completed' : 'status-pending' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Total Amount</th>
                    <td class="total">{{ Number::currency($order->grand_total, config('app.currency')) }}</td>
                </tr>
            </table>
            
            <h4 style="margin-top: 20px; color: #374151;">Tickets & Attendees</h4>
            <table class="ticket-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Attendee</th>
                        <th>Ticket Type</th>
                        <th>Add-ons</th>
                        <th style="text-align: right;">Ticket Price</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $ticketPrice = $order->subtotal / $order->tickets->count();
                    @endphp
                    @foreach($order->tickets as $ticket)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="attendee-name">{{ $ticket->attendee_name }}</div>
                            <div style="font-size: 11px; color: #666;">{{ $ticket->attendee_email }}</div>
                        </td>
                        <td>{{ $ticket->ticketType->title ?? 'N/A' }}</td>
                        <td>
                            @if($ticket->addOns->count() > 0)
                                @foreach($ticket->addOns as $addOn)
                                    <span class="addon-badge">{{ $addOn->title }} ({{ Number::currency($addOn->price, config('app.currency')) }})</span>
                                @endforeach
                            @else
                                <span style="color: #999; font-size: 11px;">-</span>
                            @endif
                        </td>
                        <td style="text-align: right;">{{ Number::currency($ticketPrice, config('app.currency')) }}</td>
                    </tr>
                    @endforeach
                    <tr style="background: #f9fafb;">
                        <td colspan="4" style="text-align: right; font-weight: bold;">Subtotal (Tickets)</td>
                        <td style="text-align: right;">{{ Number::currency($order->subtotal - $order->tickets->sum(fn($t) => $t->addOns->sum('price')), config('app.currency')) }}</td>
                    </tr>
                    @if($order->tickets->sum(fn($t) => $t->addOns->sum('price')) > 0)
                    <tr style="background: #f9fafb;">
                        <td colspan="4" style="text-align: right; font-weight: bold;">Add-ons Total</td>
                        <td style="text-align: right;">{{ Number::currency($order->tickets->sum(fn($t) => $t->addOns->sum('price')), config('app.currency')) }}</td>
                    </tr>
                    @endif
                    @if($order->discount_amount > 0)
                    <tr style="background: #f9fafb;">
                        <td colspan="4" style="text-align: right; font-weight: bold; color: #10B981;">Discount</td>
                        <td style="text-align: right; color: #10B981;">-{{ Number::currency($order->discount_amount, config('app.currency')) }}</td>
                    </tr>
                    @endif
                    @if($order->tax_amount > 0)
                    <tr style="background: #f9fafb;">
                        <td colspan="4" style="text-align: right; font-weight: bold;">Tax</td>
                        <td style="text-align: right;">{{ Number::currency($order->tax_amount, config('app.currency')) }}</td>
                    </tr>
                    @endif
                    <tr style="background: #fef3c7;">
                        <td colspan="4" style="text-align: right; font-weight: bold;">Total</td>
                        <td style="text-align: right; font-weight: bold;">{{ Number::currency($order->grand_total, config('app.currency')) }}</td>
                    </tr>
                </tbody>
            </table>
            
            @if($order->address)
            <div class="billing-info">
                <h4>Billing Address</h4>
                <p style="margin: 5px 0; font-size: 13px;">
                    {{ $order->buyer_name }}<br>
                    {{ $order->address }}<br>
                    {{ $order->city }}, {{ $order->state }} - {{ $order->pincode }}
                </p>
            </div>
            @endif
            
            <hr style="border: none; border-top: 1px solid #e5e5e5; margin: 20px 0;">
            
            <p>Login to admin panel to view more details and manage the order.</p>
        </div>
        
        <div class="footer">
            <p class="brand">{{ config('app.name', 'helloEvents') }} - Admin Panel</p>
            <p style="margin-top: 10px;">Designed & Developed By <a href="https://www.vfixtechnology.com" target="_blank" rel="noopener">VFIX TECHNOLOGY</a></p>
        </div>
    </div>
</body>
</html>
