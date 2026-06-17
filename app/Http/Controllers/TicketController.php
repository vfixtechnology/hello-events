<?php

namespace App\Http\Controllers;

use App\Helpers\QrCodeHelper;
use App\Models\Ticket;
use App\Notifications\TicketBookingNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\TicketsExport;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:ticket list')->only(['index']);
        $this->middleware('can:ticket show')->only(['show']);
        $this->middleware('can:ticket edit')->only(['edit', 'update']);
        $this->middleware('can:ticket update-status')->only(['updateStatus']);
        $this->middleware('can:ticket delete')->only(['destroy']);
        $this->middleware('can:ticket check-in')->only(['checkIn']);
        $this->middleware('can:ticket download-pdf')->only(['downloadPdf']);
        $this->middleware('can:ticket resend-email')->only(['resendEmail']);
        $this->middleware('can:ticket export')->only(['export']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $eventId = $request->input('event_id');
        $ticketTypeId = $request->input('ticket_type_id');
        $perPage = in_array($request->input('per_page'), [20, 50, 100]) ? (int) $request->input('per_page') : 20;

        $excludedUuid = '864f95d3-e831-4086-b936-e6823b67a84c';

        $tickets = Ticket::with(['order', 'ticketType', 'ticketType.event'])
            ->where('uuid', '!=', $excludedUuid)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('attendee_name', 'like', "%{$search}%")
                        ->orWhere('attendee_email', 'like', "%{$search}%")
                        ->orWhere('attendee_phone', 'like', "%{$search}%")
                        ->orWhere('uuid', 'like', "%{$search}%")
                        ->orWhereHas('order', function ($orderQuery) use ($search) {
                            $orderQuery->where('order_number', 'like', "%{$search}%");
                        })
                        ->orWhereHas('ticketType.event', function ($eventQuery) use ($search) {
                            $eventQuery->where('title', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($eventId, function ($query) use ($eventId) {
                $query->whereHas('ticketType', function ($q) use ($eventId) {
                    $q->where('event_id', $eventId);
                });
            })
            ->when($ticketTypeId, function ($query) use ($ticketTypeId) {
                $query->where('ticket_type_id', $ticketTypeId);
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $events = Event::latest()->get();
        $ticketTypes = TicketType::with('event')->latest('id')->get();

        return view('backend.ticket.index', compact('tickets', 'search', 'status', 'eventId', 'ticketTypeId', 'perPage', 'events', 'ticketTypes'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['order', 'ticketType', 'ticketType.event', 'addOns']);

        return view('backend.ticket.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        $ticket->load(['order', 'ticketType', 'ticketType.event']);

        return view('backend.ticket.edit', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'attendee_name' => 'required|string|max:255',
            'attendee_email' => 'required|email|max:255',
            'attendee_phone' => 'nullable|string|max:20',
            'organization' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
        ]);

        $ticket->update([
            'attendee_name' => $request->attendee_name,
            'attendee_email' => $request->attendee_email,
            'attendee_phone' => $request->attendee_phone,
            'organization' => $request->organization,
            'designation' => $request->designation,
        ]);

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Ticket updated successfully.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|in:valid,used,cancelled,refunded',
            'max_entries' => 'required|integer|min:1',
            'cancellation_reason' => 'required_if:status,cancelled|nullable|string',
        ]);

        $ticket->update([
            'status' => $request->status,
            'max_entries' => $request->max_entries,
            'cancellation_reason' => $request->status === 'cancelled' ? $request->cancellation_reason : null,
        ]);

        return back()->with('success', 'Ticket status updated successfully.');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }

    public function downloadPdf(Ticket $ticket)
    {
        $ticket->load(['ticketType.event', 'addOns']);
        $event = $ticket->ticketType->event;
        $qrCode = QrCodeHelper::generateBase64($ticket->uuid, 200);

        $ticketData = $ticket->toArray();
        $ticketData['add_ons'] = $ticket->addOns->map(fn($addOn) => [
            'title' => $addOn->title,
            'price' => $addOn->price,
        ])->toArray();

        $pdf = Pdf::loadView('pdf.ticket', [
            'ticket' => $ticketData,
            'event' => $event,
            'qrCode' => $qrCode,
        ])->setPaper('a4', 'portrait')->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => true,
        ]);

        return $pdf->download('ticket-'.$ticket->uuid.'.pdf');
    }

    public function resendEmail(Ticket $ticket)
    {
        $ticket->load(['order', 'ticketType.event', 'addOns']);
        $event = $ticket->ticketType->event;

        $ticketData = $ticket->toArray();
        $ticketData['add_ons'] = $ticket->addOns->map(fn($addOn) => [
            'title' => $addOn->title,
            'price' => $addOn->price,
        ])->toArray();

        Notification::route('mail', $ticket->attendee_email)
            ->notify(new TicketBookingNotification(
                [$ticketData],
                $ticket->order,
                $ticket->attendee_name
            ));

        return back()->with('success', 'Ticket email resent to '.$ticket->attendee_email);
    }

    public function checkIn(Request $request, Ticket $ticket)
    {
        if ($ticket->status === 'cancelled' || $ticket->status === 'refunded') {
            return back()->with('error', 'This ticket cannot be checked in.');
        }

        if ($ticket->check_in_count >= $ticket->max_entries) {
            return back()->with('error', 'Maximum entry limit reached for this ticket.');
        }

        $ticket->update([
            'check_in_count' => $ticket->check_in_count + 1,
            'first_check_in_at' => $ticket->first_check_in_at ?? now(),
            'last_check_in_at' => now(),
            'status' => $ticket->check_in_count + 1 >= $ticket->max_entries ? 'used' : 'valid',
        ]);

        if ($ticket->order->payment_method === 'cash_on_delivery' && $ticket->order->status !== 'completed') {
            $ticket->order->update(['status' => 'completed']);
        }

        return back()->with('success', 'Ticket checked in successfully!');
    }

    public function export(Request $request)
    {
        $format = $request->input('format', 'xlsx');
        $search = $request->input('search');
        $status = $request->input('status');
        $eventId = $request->input('event_id');
        $ticketTypeId = $request->input('ticket_type_id');

        $export = new TicketsExport($search, $status, $eventId, $ticketTypeId);

        $filename = 'tickets-export.'.($format === 'csv' ? 'csv' : 'xlsx');

        return Excel::download($export, $filename);
    }
}
