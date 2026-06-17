<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TicketsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $search;
    protected $status;
    protected $eventId;
    protected $ticketTypeId;

    public function __construct($search = null, $status = null, $eventId = null, $ticketTypeId = null)
    {
        $this->search = $search;
        $this->status = $status;
        $this->eventId = $eventId;
        $this->ticketTypeId = $ticketTypeId;
    }

    public function query()
    {
        $excludedUuid = '864f95d3-e831-4086-b936-e6823b67a84c';

        return Ticket::with(['order', 'ticketType', 'ticketType.event'])
            ->where('uuid', '!=', $excludedUuid)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('attendee_name', 'like', "%{$this->search}%")
                        ->orWhere('attendee_email', 'like', "%{$this->search}%")
                        ->orWhere('attendee_phone', 'like', "%{$this->search}%")
                        ->orWhere('uuid', 'like', "%{$this->search}%")
                        ->orWhereHas('order', function ($orderQuery) {
                            $orderQuery->where('order_number', 'like', "%{$this->search}%");
                        })
                        ->orWhereHas('ticketType.event', function ($eventQuery) {
                            $eventQuery->where('title', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->eventId, function ($query) {
                $query->whereHas('ticketType', function ($q) {
                    $q->where('event_id', $this->eventId);
                });
            })
            ->when($this->ticketTypeId, function ($query) {
                $query->where('ticket_type_id', $this->ticketTypeId);
            })
            ->latest();
    }

    public function headings(): array
    {
        return [
            '#',
            'Ticket UUID',
            'Event',
            'Attendee Name',
            'Attendee Email',
            'Attendee Phone',
            'Ticket Type',
            'Order #',
            'Payment Status',
            'Payment Method',
            'Status',
            'Check-ins',
            'Max Entries',
            'Organization',
            'Designation',
            'Created At',
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->id,
            $ticket->uuid,
            $ticket->ticketType->event->title ?? 'N/A',
            $ticket->attendee_name,
            $ticket->attendee_email,
            $ticket->attendee_phone ?? '',
            $ticket->ticketType->title ?? 'N/A',
            $ticket->order->order_number ?? 'N/A',
            $ticket->order ? ucfirst($ticket->order->status) : 'N/A',
            $ticket->order ? str_replace('_', ' ', ucfirst($ticket->order->payment_method)) : 'N/A',
            ucfirst($ticket->status),
            $ticket->check_in_count,
            $ticket->max_entries,
            $ticket->organization ?? '',
            $ticket->designation ?? '',
            $ticket->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
