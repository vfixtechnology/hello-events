@forelse($upcomingEvents as $event)
    <tr>
        <td>
            <div class="d-flex align-items-center">
                @if($event->getFirstMediaUrl('default', 'thumb'))
                    <img src="{{ $event->getFirstMediaUrl('default', 'thumb') }}"
                         alt="{{ $event->title }}"
                         class="rounded mr-2"
                         style="width:40px;height:30px;object-fit:cover">
                @else
                    <span class="d-inline-flex align-items-center justify-content-center rounded mr-2 font-weight-bold text-white"
                          style="width:40px;height:30px;font-size:11px;background:linear-gradient(135deg,#36b9cc,#258391)">
                        {{ substr($event->title, 0, 2) }}
                    </span>
                @endif
                <div class="text-truncate" style="max-width:200px">
                    <a href="{{ route('event.edit', $event->id) }}" class="text-reset text-decoration-none font-weight-medium">{{ $event->title }}</a>
                    <br>
                    <small class="text-muted">{{ $event->venue ?? 'No venue' }}</small>
                </div>
            </div>
        </td>
        <td class="text-nowrap">
            {{ $event->start_datetime->format('M d, Y') }}<br>
            <small class="text-muted">{{ $event->start_datetime->format('h:i A') }}</small>
        </td>
        <td><span class="badge badge-primary">{{ $event->total_tickets ?: 0 }}</span></td>
    </tr>
@empty
    <tr>
        <td colspan="3" class="text-center py-4 text-muted">
            <i class="fas fa-calendar fa-2x mb-2 d-block"></i>
            No upcoming events
        </td>
    </tr>
@endforelse
