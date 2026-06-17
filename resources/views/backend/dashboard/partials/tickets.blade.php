@forelse($recentTickets as $ticket)
    <tr>
        <td>
            {{ $ticket->attendee_name }}<br>
            <small class="text-muted">{{ $ticket->attendee_email }}</small>
        </td>
        <td>
            <span class="font-weight-medium">{{ $ticket->ticketType->title ?? 'N/A' }}</span><br>
            <small class="text-muted">{{ Str::limit($ticket->ticketType->event->title ?? '', 30) }}</small>
        </td>
        <td>
            <span class="badge badge-pill badge-{{ $ticket->status === 'valid' ? 'success' : ($ticket->status === 'used' ? 'danger' : ($ticket->status === 'cancelled' ? 'secondary' : 'warning')) }}">
                {{ $ticket->status }}
            </span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="3" class="text-center py-4 text-muted">
            <i class="fas fa-ticket-alt fa-2x mb-2 d-block"></i>
            No tickets yet
        </td>
    </tr>
@endforelse
