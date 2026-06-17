<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket - {{ $ticket['uuid'] }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; line-height: 1.5; color: #333; }
        .ticket-container { max-width: 400px; margin: 0 auto; border: 2px dashed #007bff; border-radius: 12px; overflow: hidden; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .header h1 { font-size: 18px; margin-bottom: 5px; }
        .header p { font-size: 12px; opacity: 0.9; }

        .content { padding: 20px; }
        .event-title { font-size: 16px; font-weight: bold; color: #007bff; margin-bottom: 15px; }

        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: bold; color: #666; font-size: 12px; }
        .info-value { font-size: 12px; text-align: right; }

        .attendee-box { background: #f9fafb; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .attendee-box h3 { font-size: 14px; color: #007bff; margin-bottom: 10px; }

        .qr-section { text-align: center; padding: 20px; background: #f3f4f6; }
        .qr-section img { border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .qr-section p { font-size: 10px; color: #666; margin-top: 8px; }

        .footer { background: #1f2937; color: #9ca3af; padding: 15px; text-align: center; font-size: 10px; }

        .map-link { display: inline-block; margin-top: 10px; padding: 8px 16px; background: #10B981; color: white; text-decoration: none; border-radius: 6px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="header">
            <h1>{{ config('app.name', 'helloEvents') }}</h1>
            <p>Event Ticket</p>
        </div>

        <div class="content">
            <div class="event-title">{{ $event->title }}</div>

            <div class="info-row">
                <span class="info-label">Date</span>
                <span class="info-value">{{ $event->start_datetime->format('D, M d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Time</span>
                <span class="info-value">{{ $event->start_datetime->format('g:i A') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Venue</span>
                <span class="info-value">{{ $event->venue }}</span>
            </div>
            @if($event->city || $event->state)
            <div class="info-row">
                <span class="info-label">Location</span>
                <span class="info-value">{{ $event->city }}{{ $event->city && $event->state ? ', ' : '' }}{{ $event->state }}</span>
            </div>
            @endif

            @if($event->map_link)
            <div class="info-row">
                <span class="info-label">Map</span>
                <span class="info-value"><a href="{{ $event->map_link }}" style="color: #10B981;">View on Map</a></span>
            </div>
            @endif

            <div class="attendee-box">
                <h3>Attendee Details</h3>
                <div class="info-row">
                    <span class="info-label">Name</span>
                    <span class="info-value">{{ $ticket['attendee_name'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ $ticket['attendee_email'] }}</span>
                </div>
                @if(isset($ticket['attendee_phone']) && $ticket['attendee_phone'])
                <div class="info-row">
                    <span class="info-label">Phone</span>
                    <span class="info-value">{{ $ticket['attendee_phone'] }}</span>
                </div>
                @endif
            </div>

            <div class="info-row">
                <span class="info-label">Ticket ID</span>
                <span class="info-value">{{ $ticket['uuid'] }}</span>
            </div>

            @if(isset($ticket['add_ons']) && count($ticket['add_ons']) > 0)
            <div class="attendee-box">
                <h3>Add-ons</h3>
                @foreach($ticket['add_ons'] as $addOn)
                <div class="info-row">
                    <span class="info-label">{{ $addOn['title'] }}</span>
                    <span class="info-value">{{ Number::currency($addOn['price'], config('app.currency')) }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="qr-section">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" width="120">
            <p>Show this QR code at the venue</p>
        </div>

        <div class="footer">
            <p>{{ config('app.name', 'helloEvents') }} - Thank you for your booking!</p>
            <p style="margin-top: 5px;">Designed & Developed By <a href="https://www.vfixtechnology.com" target="_blank" style="color: #9ca3af;">VFIX TECHNOLOGY</a></p>
        </div>
    </div>
</body>
</html>
