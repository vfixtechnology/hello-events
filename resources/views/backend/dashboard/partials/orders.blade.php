@forelse($recentOrders as $ticket)
    <tr onclick="window.location='{{ route('tickets.show', $ticket->id) }}'" style="cursor:pointer">
        <td>
            {{ $ticket->attendee_name }}<br>
            <small class="text-muted">{{ $ticket->attendee_email }}</small>
        </td>
        <td>
            <span class="font-weight-medium">{{ $ticket->ticketType->title ?? 'N/A' }}</span><br>
            <small class="text-muted">{{ Str::limit($ticket->ticketType->event->title ?? '', 30) }}</small>
        </td>
        <td class="font-weight-bold text-primary">{{ $ticket->order->order_number ?? 'N/A' }}</td>
        <td>
            <span class="badge badge-pill badge-{{ $ticket->status === 'valid' ? 'success' : ($ticket->status === 'used' ? 'danger' : ($ticket->status === 'cancelled' ? 'secondary' : 'warning')) }}">
                {{ $ticket->status }}
            </span>
        </td>
        <td>
            <span title="{{ $ticket->created_at->format('M d, Y H:i') }}">
                {{ $ticket->created_at->diffForHumans() }}
            </span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center py-4 text-muted">
            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
            No tickets yet
        </td>
    </tr>
@endforelse
