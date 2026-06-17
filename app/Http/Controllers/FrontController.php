<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use App\Models\Setting;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class FrontController extends Controller
{
    protected Setting $setting;

    public function __construct()
    {
        $this->setting = Setting::first() ?? new Setting();
        view()->share('setting', $this->setting);
    }
    public function index()
    {
        $categories = Category::with('media')->get();
        $upcomingEvents = Event::where('end_datetime', '>', now())->latest()->get();
        $pastEvents = Event::where('end_datetime', '<=', now())->latest()->get();

        $seo = new SEOData(
            title: $this->setting->bname ?? 'Vfix Technology',
            description: 'Discover and book tickets for the best events in your city. From concerts and festivals to workshops and conferences.',
        );

        return view('frontend.index', compact('categories', 'upcomingEvents', 'pastEvents', 'seo'));
    }

    public function eventDetail($slug)
    {
        $event = Event::whereSlug($slug)->with('ticketTypes', 'addOns', 'taxRate')->firstOrFail();

        $seo = $event->getDynamicSEOData();

        return view('frontend.event-detail', compact('event', 'seo'));
    }

    public function events()
    {
        $upcomingEvents = Event::where('end_datetime', '>', now())->latest()->paginate(12);
        $pastEvents = Event::where('end_datetime', '<=', now())->latest()->paginate(12);

        $seo = new SEOData(
            title: 'Explore Events - ' . ($this->setting->bname ?? 'Vfix Technology'),
            description: 'Discover amazing events happening near you. Browse upcoming and past events and book your tickets today.',
        );

        return view('frontend.events', compact('upcomingEvents', 'pastEvents', 'seo'));
    }

    public function categoryEvents($slug)
    {
        $category = Category::whereSlug($slug)->with('media')->firstOrFail();

        $upcomingEvents = $category->events()
            ->where('end_datetime', '>', now())
            ->latest()
            ->paginate(12);

        $pastEvents = $category->events()
            ->where('end_datetime', '<=', now())
            ->latest()
            ->paginate(12);

        $seo = new SEOData(
            title: $category->seo?->title ?? "{$category->title} Events - " . ($this->setting->bname ?? 'Vfix Technology'),
            description: $category->seo?->description ?? "Browse and book tickets for {$category->title} events.",
        );

        return view('frontend.events', compact('upcomingEvents', 'pastEvents', 'seo', 'category'));
    }

    public function contact()
    {
        $seo = new SEOData(
            title: 'Contact Us - ' . ($this->setting->bname ?? 'Vfix Technology'),
            description: 'Get in touch with us for any inquiries or support. We are here to help you with your event booking experience.',
        );

        return view('frontend.contact', compact('seo'));
    }


    public function booking(Request $request, $slug)
    {
        $event = Event::whereSlug($slug)->with(['ticketTypes', 'addOns', 'taxRate'])->firstOrFail();

        if ($event->end_datetime->isPast()) {
            return redirect()->route('event.detail', $event->slug)->with('error', 'This event has already passed.');
        }

        $event->load(['ticketTypes' => function ($query) {
            $query->select('id', 'event_id', 'title', 'body', 'price', 'compare_at_price', 'quantity', 'min_quantity', 'max_entries');
        }]);

        // Get attendee data from session if exists
        $attendeeData = session("booking_attendees_{$event->id}", []);

        $seo = new SEOData(
            title: "Book Tickets - {$event->title}",
            description: "Book your tickets for {$event->title}. Select ticket types, add-ons, and attendee information.",
        );

        return view('frontend.booking', compact('event', 'attendeeData', 'seo'));
    }

    public function process(Request $request, Event $event)
    {
        if ($event->end_datetime->isPast()) {
            return redirect()->route('event.detail', $event->slug)->with('error', 'This event has already passed.');
        }

        $bookingData = json_decode($request->attendees, true);

        if (is_null($bookingData) || ! is_array($bookingData) || empty($bookingData)) {
            return back()->withErrors(['message' => 'Please select at least one ticket.']);
        }

        $validator = Validator::make($bookingData, [
            '*.ticketTypeId' => 'required|integer|exists:ticket_types,id',
            '*.name' => 'required|string|max:255',
            '*.email' => 'required|email|max:255',
            '*.phone' => 'nullable|digits:10',
            '*.add_ons' => 'nullable|array',
            '*.add_ons.*' => 'integer|exists:add_ons,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validatedAttendees = $validator->validated();

        $requestedTickets = [];
        foreach ($validatedAttendees as $attendee) {
            $ticketTypeId = $attendee['ticketTypeId'];
            if (! isset($requestedTickets[$ticketTypeId])) {
                $requestedTickets[$ticketTypeId] = 0;
            }
            $requestedTickets[$ticketTypeId]++;
        }

        foreach ($requestedTickets as $ticketTypeId => $quantity) {
            $ticketType = TicketType::find($ticketTypeId);

            if (! $ticketType || $ticketType->event_id !== $event->id) {
                throw ValidationException::withMessages(['message' => 'An invalid ticket type was selected.']);
            }

            $availableTickets = $ticketType->available_tickets;

            if ($availableTickets <= 0) {
                throw ValidationException::withMessages(['message' => "Sorry, {$ticketType->title} tickets are sold out."]);
            }

            if ($quantity < $ticketType->min_quantity) {
                throw ValidationException::withMessages(['message' => "You must select at least {$ticketType->min_quantity} of {$ticketType->title} tickets."]);
            }

            if ($quantity > $availableTickets) {
                throw ValidationException::withMessages(['message' => "Sorry, only {$availableTickets} of {$ticketType->title} tickets are remaining."]);
            }
        }

        session([
            'booking_details' => [
                'event_id' => $event->id,
                'attendees' => $validatedAttendees,
            ],
        ]);

        return redirect()->route('checkout.show');
    }
}
