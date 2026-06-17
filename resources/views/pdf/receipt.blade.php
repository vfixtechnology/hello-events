<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $order->order_number }}</title>
    <style>
        @page { margin: 12px 15px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #333; line-height: 1.35; }

        .receipt { width: 100%; }

        .header { background: linear-gradient(135deg, #059669, #10B981); color: #fff; padding: 12px 16px; border-radius: 6px 6px 0 0; }
        .header h1 { font-size: 15px; font-weight: 700; letter-spacing: 0.5px; }
        .header .order-num { font-size: 10px; opacity: .85; margin-top: 2px; }

        .body { padding: 12px 16px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 6px 6px; }

        .greeting { font-size: 10.5px; margin-bottom: 10px; }
        .greeting strong { color: #059669; }

        .section-title { font-size: 10px; font-weight: 700; color: #059669; border-bottom: 1.5px solid #e5e7eb; padding-bottom: 3px; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.3px; }

        table.details { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.details td { padding: 2px 4px; vertical-align: top; }
        table.details .lbl { color: #6b7280; width: 28%; }
        table.details .val { font-weight: 600; width: 22%; }
        table.details .r-lbl { color: #6b7280; width: 28%; padding-left: 10px; }
        table.details .r-val { font-weight: 600; width: 22%; }

        table.attendees { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 9px; }
        table.attendees th { background: #f3f4f6; padding: 4px 5px; text-align: left; font-size: 8px; text-transform: uppercase; color: #6b7280; letter-spacing: 0.3px; border-bottom: 1px solid #e5e7eb; }
        table.attendees td { padding: 3px 5px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        table.attendees tr:last-child td { border-bottom: none; }
        table.attendees .addon-badge { display: inline-block; background: #ecfdf5; color: #059669; padding: 1px 5px; border-radius: 3px; font-size: 8px; margin: 1px 2px 1px 0; }

        table.pricing { width: 100%; border-collapse: collapse; font-size: 9.5px; margin-bottom: 8px; }
        table.pricing td { padding: 3px 5px; }
        table.pricing tr + tr td { border-top: 1px solid #f3f4f6; }
        table.pricing .total-row td { font-weight: 700; font-size: 11px; border-top: 2px solid #059669 !important; color: #059669; padding-top: 5px; }
        table.pricing .amount { text-align: right; font-weight: 600; }
        table.pricing .discount { color: #ef4444; }

        table.billing { width: 100%; border-collapse: collapse; font-size: 9px; }
        table.billing td { padding: 2px 4px; vertical-align: top; }
        table.billing .lbl { color: #6b7280; width: 20%; }
        table.billing .val { width: 30%; }
        table.billing .r-lbl { color: #6b7280; width: 20%; padding-left: 8px; }
        table.billing .r-val { width: 30%; }

        .footer { text-align: center; padding-top: 8px; margin-top: 4px; border-top: 1px solid #f3f4f6; font-size: 7.5px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="text-align:left;">
                        <h1>RECEIPT</h1>
                        <div class="order-num">#{{ $order->order_number }}</div>
                    </td>
                    <td style="text-align:right;vertical-align:bottom;font-size:10px;opacity:.85;">
                        {{ $order->created_at->format('M d, Y') }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="body">
            <div class="greeting">Hello <strong>{{ $order->buyer_name }}</strong>, thank you for your purchase!</div>

            <div class="section-title">Event &amp; Order</div>
            <table class="details">
                <tr>
                    <td class="lbl">Event</td>
                    <td class="val">{{ $event->title }}</td>
                    <td class="r-lbl">Order #</td>
                    <td class="r-val">{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <td class="lbl">Date</td>
                    <td class="val">{{ $event->start_datetime->format('D, M d, Y') }}</td>
                    <td class="r-lbl">Payment</td>
                    <td class="r-val">{{ $order->payment_method === 'cash_on_delivery' ? 'Pay at Event' : ucfirst(str_replace('_', ' ', $order->payment_method)) }}</td>
                </tr>
                <tr>
                    <td class="lbl">Venue</td>
                    <td class="val">{{ $event->venue }}</td>
                    <td class="r-lbl">Payment ID</td>
                    <td class="r-val">{{ $order->payment_id ?? 'N/A' }}</td>
                </tr>
            </table>

            <div class="section-title">Attendees</div>
            <table class="attendees">
                <thead>
                    <tr>
                        <th style="width:5%;">#</th>
                        <th style="width:28%;">Name</th>
                        <th style="width:32%;">Email</th>
                        <th style="width:35%;">Add-ons</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->tickets as $ticket)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $ticket->attendee_name }}</td>
                        <td>{{ $ticket->attendee_email }}</td>
                        <td>
                            @forelse($ticket->addOns as $addOn)
                            <span class="addon-badge">{{ $addOn->title }} {{ Number::currency($addOn->price, config('app.currency')) }}</span>
                            @empty
                            <span style="color:#9ca3af;">—</span>
                            @endforelse
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="section-title">Payment Summary</div>
            <table class="pricing">
                <tr>
                    <td>Tickets ({{ $order->tickets->count() }})</td>
                    <td class="amount">{{ Number::currency($order->subtotal - $order->tickets->sum(fn($t) => $t->addOns->sum('price')), config('app.currency')) }}</td>
                </tr>
                @if($order->tickets->sum(fn($t) => $t->addOns->sum('price')) > 0)
                <tr>
                    <td>Add-ons Total</td>
                    <td class="amount">{{ Number::currency($order->tickets->sum(fn($t) => $t->addOns->sum('price')), config('app.currency')) }}</td>
                </tr>
                @endif
                @if($order->discount_amount > 0)
                <tr>
                    <td>Discount</td>
                    <td class="amount discount">-{{ Number::currency($order->discount_amount, config('app.currency')) }}</td>
                </tr>
                @endif
                @if($order->tax_amount > 0)
                <tr>
                    <td>Tax</td>
                    <td class="amount">{{ Number::currency($order->tax_amount, config('app.currency')) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Total Paid</td>
                    <td class="amount">{{ Number::currency($order->grand_total, config('app.currency')) }}</td>
                </tr>
            </table>

            @if($order->address || $order->buyer_phone)
            <div class="section-title">Billing Details</div>
            <table class="billing">
                <tr>
                    <td class="lbl">Name</td>
                    <td class="val">{{ $order->buyer_name }}</td>
                    @if($order->buyer_phone)
                    <td class="r-lbl">Phone</td>
                    <td class="r-val">{{ $order->buyer_phone }}</td>
                    @else
                    <td></td><td></td>
                    @endif
                </tr>
                <tr>
                    <td class="lbl">Email</td>
                    <td class="val">{{ $order->buyer_email }}</td>
                    @if($order->address)
                    <td class="r-lbl">Address</td>
                    <td class="r-val">{{ $order->address }}, {{ $order->city }}, {{ ucwords($order->state) }}, {{ ucwords($order->country) }} - {{ $order->pincode }}</td>
                    @else
                    <td></td><td></td>
                    @endif
                </tr>
            </table>
            @endif
        </div>

        <div class="footer">
            {{ config('app.name', 'helloEvents') }} &mdash; Thank you for your purchase!
        </div>
    </div>
</body>
</html>
