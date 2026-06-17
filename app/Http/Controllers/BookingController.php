<?php

namespace App\Http\Controllers;

use App\Models\AddOn;
use App\Models\Country;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function addTicket(Request $request, Event $event)
    {
        if ($event->end_datetime->isPast()) {
            return back()->with('error', 'This event has already passed.');
        }

        $ticketTypeId = $request->input('ticket_type_id');
        $quantity = (int) $request->input('quantity', 1);

        $ticketType = TicketType::find($ticketTypeId);

        if (! $ticketType || $ticketType->event_id !== $event->id) {
            return back()->with('error', 'Invalid ticket type.');
        }

        $available = $ticketType->available_tickets;
        if ($available < $quantity) {
            return back()->with('error', "Only $available tickets available for {$ticketType->title}.");
        }

        $minQty = $ticketType->min_quantity ?? 1;
        if ($quantity < $minQty) {
            return redirect()->route('booking', $event->slug)->with('error', "Minimum $minQty tickets required for {$ticketType->title}.");
        }

        $sessionKey = "booking_tickets_{$event->id}";
        $tickets = session($sessionKey, []);

        if (! isset($tickets[$ticketTypeId])) {
            $tickets[$ticketTypeId] = 0;
        }

        $tickets[$ticketTypeId] += $quantity;

        if ($tickets[$ticketTypeId] > $available) {
            return redirect()->route('booking', $event->slug)->with('error', "Only $available tickets available for {$ticketType->title}.");
        }

        session([$sessionKey => $tickets]);

        return redirect()->route('booking', $event->slug)->with('success', 'Ticket added successfully.');
    }

    public function removeTicket(Request $request, Event $event)
    {
        $ticketTypeId = (int) $request->input('ticket_type_id');
        $quantity = (int) $request->input('quantity', 1);

        $sessionKey = "booking_tickets_{$event->id}";
        $tickets = session($sessionKey, []);

        // Convert all keys to integers
        $tickets = array_combine(array_map('intval', array_keys($tickets)), array_values($tickets));

        if (isset($tickets[$ticketTypeId])) {
            $currentQty = $tickets[$ticketTypeId];

            // If quantity to remove equals current quantity, remove all
            if ($quantity >= $currentQty) {
                unset($tickets[$ticketTypeId]);
            } else {
                $ticketType = TicketType::find($ticketTypeId);
                $minQty = $ticketType ? ($ticketType->min_quantity ?? 1) : 1;

                if ($quantity == 1) {
                    $tickets[$ticketTypeId] -= $minQty;
                } else {
                    $tickets[$ticketTypeId] -= $quantity;
                }

                if ($tickets[$ticketTypeId] <= 0) {
                    unset($tickets[$ticketTypeId]);
                }
            }

            session([$sessionKey => $tickets]);
        }

        return redirect()->route('booking', $event->slug)->with('success', 'Ticket removed.');
    }

    // public function applyTickets(Request $request, Event $event)
    // {
    //     if ($event->end_datetime->isPast()) {
    //         return redirect()->route('event.detail', $event->slug)->with('error', 'This event has already passed.');
    //     }

    //     $quantities = $request->input('quantities', []);
    //     $sessionKey = "booking_tickets_{$event->id}";
    //     $tickets = [];

    //     foreach ($quantities as $ticketTypeId => $quantity) {
    //         $ticketTypeId = (int) $ticketTypeId;
    //         $quantity = (int) $quantity;
    //         if ($quantity <= 0) {
    //             continue;
    //         }

    //         $ticketType = TicketType::find($ticketTypeId);

    //         if (! $ticketType || $ticketType->event_id !== $event->id) {
    //             continue;
    //         }

    //         $available = $ticketType->available_tickets;
    //         if ($quantity > $available) {
    //             return redirect()->route('booking', $event->slug)->with('error', "Only $available tickets available for {$ticketType->title}.");
    //         }

    //         $minQty = $ticketType->min_quantity ?? 1;
    //         if ($quantity < $minQty) {
    //             return redirect()->route('booking', $event->slug)->with('error', "Minimum $minQty tickets required for {$ticketType->title}.");
    //         }

    //         $tickets[$ticketTypeId] = $quantity;
    //     }

    //     if (empty($tickets)) {
    //         return redirect()->route('booking', $event->slug)->with('error', 'Please select at least one ticket.');
    //     }

    //     session([$sessionKey => $tickets]);

    //     return redirect()->route('booking', $event->slug)->with('success', 'Tickets applied successfully.');
    // }

	public function applyTickets(Request $request, Event $event)
{
    if ($event->end_datetime->isPast()) {
        return redirect()->route('event.detail', $event->slug)->with('error', 'This event has already passed.');
    }

    $allInput = $request->all();
    $quantities = $allInput['quantities'] ?? [];

    if (empty($quantities)) {
        $quantities = $request->get('quantities', []);
    }

    $sessionKey = "booking_tickets_{$event->id}";
    $tickets = [];

    foreach ($quantities as $ticketTypeId => $quantity) {
        if (empty($quantity) || (int)$quantity <= 0) {
            continue;
        }

        $ticketTypeId = (int)$ticketTypeId;
        $quantity = (int)$quantity;

        $ticketType = TicketType::find($ticketTypeId);

        if (!$ticketType) {
            continue;
        }

        // FIX: Cast both to integers for comparison
        if ((int)$ticketType->event_id !== (int)$event->id) {
            continue;
        }

        $available = $ticketType->available_tickets;
        if ($quantity > $available) {
            return redirect()->route('booking', $event->slug)->with('error', "Only $available tickets available for {$ticketType->title}.");
        }

        $minQty = $ticketType->min_quantity ?? 1;
        if ($quantity < $minQty) {
            return redirect()->route('booking', $event->slug)->with('error', "Minimum $minQty tickets required for {$ticketType->title}.");
        }

        $tickets[$ticketTypeId] = $quantity;
    }

    if (empty($tickets)) {
        return redirect()->route('booking', $event->slug)->with('error', 'Please select at least one ticket.');
    }

    session([$sessionKey => $tickets]);
    session()->save();

    return redirect()->route('booking', $event->slug)->with('success', 'Tickets applied successfully.');
}

    public function clearTickets(Event $event)
    {
        session()->forget("booking_tickets_{$event->id}");

        return redirect()->route('booking', $event->slug)->with('success', 'All tickets cleared.');
    }

    // public function process(Request $request, Event $event)
    // {
    //     if ($event->end_datetime->isPast()) {
    //         return redirect()->route('event.detail', $event->slug)->with('error', 'This event has already passed.');
    //     }

    //     // Check session for tickets
    //     $sessionKey = "booking_tickets_{$event->id}";
    //     $selectedTickets = session($sessionKey, []);

    //     if (empty($selectedTickets)) {
    //         return back()->withErrors(['message' => 'Please select at least one ticket.']);
    //     }

    //     // The request data is already an array, so we can use it directly.
    //     // We remove the json_decode line.
    //     $bookingData = $request->input('attendees');

    //     // Basic check to ensure data is in the expected format
    //     if (is_null($bookingData) || ! is_array($bookingData) || empty($bookingData)) {
    //         return back()->withErrors(['message' => 'Please fill in attendee details.']);
    //     }

    //     // Validate ticket count matches session
    //     $requestedTicketCount = count($bookingData);
    //     $sessionTicketCount = array_sum($selectedTickets);

    //     if ($requestedTicketCount !== $sessionTicketCount) {
    //         return back()->withErrors(['message' => 'Ticket count mismatch. Please refresh and try again.']);
    //     }

    //     // --- 2. VALIDATE ALL ATTENDEE DETAILS ---
    //     // The validator can now work directly with the request input
    //     $validator = Validator::make($request->all(), [
    //         'attendees' => 'required|array|min:1',
    //         'attendees.*.name' => 'required|string|max:255',
    //         'attendees.*.email' => 'required|email|max:255',
    //         'attendees.*.phone' => 'nullable|string|max:20',
    //         'attendees.*.ticketTypeId' => 'required|integer|exists:ticket_types,id',
    //         'attendees.*.add_ons' => 'nullable|array',
    //         'attendees.*.add_ons.*' => 'integer|exists:add_ons,id',
    //     ], [
    //         'attendees.*.name.required' => 'The name for attendee #:position is required.',
    //         'attendees.*.email.required' => 'The email for attendee #:position is required.',
    //     ]);

    //     if ($validator->fails()) {
    //         // Save attendee data to session
    //         session(["booking_attendees_{$event->id}" => $request->input('attendees')]);

    //         // When validation fails, Laravel automatically handles redirecting back
    //         // with errors and old input, so you can display them in your form.
    //         return back()->withErrors($validator)->withInput();
    //     }

    //     // Get the fully validated attendees array
    //     $validatedAttendees = $validator->validated()['attendees'];

    //     // --- 3. VALIDATE TICKET COUNTS (This logic remains the same) ---
    //     $requestedTickets = [];
    //     foreach ($validatedAttendees as $attendee) {
    //         $ticketTypeId = $attendee['ticketTypeId'];
    //         if (! isset($requestedTickets[$ticketTypeId])) {
    //             $requestedTickets[$ticketTypeId] = 0;
    //         }
    //         $requestedTickets[$ticketTypeId]++;
    //     }

    //     foreach ($requestedTickets as $ticketTypeId => $quantity) {
    //         $ticketType = TicketType::find($ticketTypeId);

    //         if (! $ticketType || $ticketType->event_id !== $event->id) {
    //             throw ValidationException::withMessages(['message' => 'An invalid ticket type was selected.']);
    //         }
    //         if ($quantity < $ticketType->min_quantity) {
    //             throw ValidationException::withMessages(['message' => "You must select at least {$ticketType->min_quantity} of {$ticketType->title} tickets."]);
    //         }
    //         if ($quantity > $ticketType->quantity) {
    //             throw ValidationException::withMessages(['message' => "Sorry, only {$ticketType->quantity} of {$ticketType->title} tickets are remaining."]);
    //         }
    //     }

    //     // --- If all validation passes, store the data in the session ---
    //     session(['booking_details' => [
    //         'event_id' => $event->id,
    //         'attendees' => $validatedAttendees,
    //     ]]);

    //     // Also save attendee data for booking page persistence
    //     session(["booking_attendees_{$event->id}" => $validatedAttendees]);

    //     // Redirect to the final checkout/payment page
    //     return redirect()->route('checkout.show');
    // }

	public function process(Request $request, Event $event)
{
    if ($event->end_datetime->isPast()) {
        return redirect()->route('event.detail', $event->slug)->with('error', 'This event has already passed.');
    }

    // Check session for tickets
    $sessionKey = "booking_tickets_{$event->id}";
    $selectedTickets = session($sessionKey, []);

    if (empty($selectedTickets)) {
        return back()->withErrors(['message' => 'Please select at least one ticket.']);
    }

    $bookingData = $request->input('attendees');

    if (is_null($bookingData) || ! is_array($bookingData) || empty($bookingData)) {
        return back()->withErrors(['message' => 'Please fill in attendee details.']);
    }

    // Validate ticket count matches session
    $requestedTicketCount = count($bookingData);
    $sessionTicketCount = array_sum($selectedTickets);

    if ($requestedTicketCount !== $sessionTicketCount) {
        return back()->withErrors(['message' => 'Ticket count mismatch. Please refresh and try again.']);
    }

    // --- 2. VALIDATE ALL ATTENDEE DETAILS ---
    $validator = Validator::make($request->all(), [
        'attendees' => 'required|array|min:1',
        'attendees.*.name' => 'required|string|max:255',
        'attendees.*.email' => 'required|email|max:255',
        'attendees.*.phone' => 'nullable|string|max:20',
        'attendees.*.ticketTypeId' => 'required|integer|exists:ticket_types,id',
        'attendees.*.add_ons' => 'nullable|array',
        'attendees.*.add_ons.*' => 'integer|exists:add_ons,id',
    ], [
        'attendees.*.name.required' => 'The name for attendee #:position is required.',
        'attendees.*.email.required' => 'The email for attendee #:position is required.',
    ]);

    if ($validator->fails()) {
        session(["booking_attendees_{$event->id}" => $request->input('attendees')]);
        return back()->withErrors($validator)->withInput();
    }

    $validatedAttendees = $validator->validated()['attendees'];

    // --- 3. VALIDATE TICKET COUNTS (FIXED VERSION) ---
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

        // FIX: Cast both to integers or use loose comparison
        if (! $ticketType || (int)$ticketType->event_id !== (int)$event->id) {
            throw ValidationException::withMessages(['message' => 'An invalid ticket type was selected.']);
        }

        if ($quantity < $ticketType->min_quantity) {
            throw ValidationException::withMessages(['message' => "You must select at least {$ticketType->min_quantity} of {$ticketType->title} tickets."]);
        }

        // FIX: Use available_tickets instead of quantity
        $available = $ticketType->available_tickets;
        if ($quantity > $available) {
            throw ValidationException::withMessages(['message' => "Sorry, only {$available} of {$ticketType->title} tickets are remaining."]);
        }
    }

    // --- If all validation passes, store the data in the session ---
    session(['booking_details' => [
        'event_id' => $event->id,
        'attendees' => $validatedAttendees,
    ]]);

    session(["booking_attendees_{$event->id}" => $validatedAttendees]);

    return redirect()->route('checkout.show');
}

    public function show()
    {
        $bookingDetails = session('booking_details');

        if (! $bookingDetails) {
            return redirect()->route('home')->with('error', 'No booking details found.');
        }

        $event = Event::find($bookingDetails['event_id']);

        if (! $event || $event->end_datetime->isPast()) {
            session()->forget('booking_details');
            return redirect()->route('home')->with('error', 'This event has already passed.');
        }

        $attendees = $bookingDetails['attendees'];

        $ticketsSummary = [];
        $subtotal = 0;
        $addOnsTotal = 0;

        $ticketCounts = array_count_values(array_column($attendees, 'ticketTypeId'));

        foreach ($ticketCounts as $ticketTypeId => $quantity) {
            $ticketType = TicketType::find($ticketTypeId);
            if ($ticketType) {
                $price = $ticketType->price * $quantity;
                $subtotal += $price;
                $ticketsSummary[] = [
                    'title' => $ticketType->title,
                    'quantity' => $quantity,
                    'price' => $price,
                ];
            }
        }

        foreach ($attendees as $attendee) {
            if (! empty($attendee['add_ons'])) {
                foreach ($attendee['add_ons'] as $addOnId) {
                    $addOn = AddOn::find($addOnId);
                    if ($addOn) {
                        $addOnsTotal += $addOn->price;
                    }
                }
            }
        }

        $subtotalWithAddons = $subtotal + $addOnsTotal;
        $taxRate = $event->taxRate ? $event->taxRate->rate : 0;

        $couponDiscount = 0;
        if (session()->has('coupon')) {
            $coupon = session('coupon');
            if ($coupon['type'] === 'fixed') {
                $couponDiscount = $coupon['value'];
            } else {
                $couponDiscount = $subtotalWithAddons * ($coupon['value'] / 100);
            }
        }

        $taxes = ($subtotalWithAddons - $couponDiscount) * ($taxRate / 100);
        $total = max(0, $subtotalWithAddons - $couponDiscount + $taxes);

        $summary = [
            'tickets' => $ticketsSummary,
            'add_ons_total' => $addOnsTotal,
            'subtotal' => $subtotalWithAddons,
            'coupon_discount' => $couponDiscount,
            'taxes' => $taxes,
            'total' => $total,
        ];

        // get country lists
        $countries = Country::all();

        $billingData = session('billing_data', []);

        return view('frontend.checkout', compact('event', 'attendees', 'summary', 'countries', 'billingData'));
    }

    public function saveBilling(Request $request)
    {
        session(['billing_data' => $request->only([
            'buyer_name', 'buyer_email', 'buyer_phone',
            'address', 'city', 'state', 'country', 'pincode',
        ])]);

        return response()->json(['success' => true]);
    }
}
