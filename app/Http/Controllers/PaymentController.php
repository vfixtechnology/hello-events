<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Models\Country;
use App\Models\Event;
use App\Models\Order;
use App\Models\State;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Services\Payment\PaymentFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Display available payment methods
     */
    public function index()
    {
        $bookingDetails = session('booking_details');

        if (! $bookingDetails) {
            return redirect()->route('home')->with('error', 'No booking details found.');
        }

        $event = Event::findOrFail($bookingDetails['event_id']);

        if ($event->end_datetime->isPast()) {
            session()->forget('booking_details');
            return redirect()->route('home')->with('error', 'This event has already passed.');
        }

        $attendees = $bookingDetails['attendees'];

        $paymentMethods = PaymentFactory::getAvailableMethods();

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
                    $addOn = \App\Models\AddOn::find($addOnId);
                    if ($addOn) {
                        $addOnsTotal += $addOn->price;
                    }
                }
            }
        }

        $couponDiscount = 0;
        if (session()->has('coupon')) {
            $coupon = session('coupon');
            if ($coupon['type'] === 'fixed') {
                $couponDiscount = $coupon['value'];
            } else {
                $couponDiscount = ($subtotal + $addOnsTotal) * ($coupon['value'] / 100);
            }
        }

        $subtotalWithAddons = $subtotal + $addOnsTotal;
        $taxRate = $event->taxRate ? $event->taxRate->rate : 0;
        $taxes = ($subtotalWithAddons - $couponDiscount) * ($taxRate / 100);
        $total = $subtotalWithAddons - $couponDiscount + $taxes;

        $summary = [
            'tickets' => $ticketsSummary,
            'add_ons_total' => $addOnsTotal,
            'subtotal' => $subtotalWithAddons,
            'coupon_discount' => $couponDiscount,
            'taxes' => $taxes,
            'total' => $total,
        ];

        return view('frontend.payment', compact('event', 'attendees', 'summary', 'paymentMethods'));
    }

    /**
     * Process the payment
     */
    public function process(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $bookingDetails = session('booking_details');

        if (! $bookingDetails) {
            return redirect()->route('home')->with('error', 'No booking details found.');
        }

        $paymentMethod = $request->payment_method;

        if (! PaymentFactory::isMethodActive($paymentMethod)) {
            return back()->withErrors(['message' => 'Invalid payment method selected.']);
        }

        $event = Event::findOrFail($bookingDetails['event_id']);

        if ($event->end_datetime->isPast()) {
            session()->forget('booking_details');
            return redirect()->route('home')->with('error', 'This event has already passed.');
        }

        $attendees = $bookingDetails['attendees'];

        // Calculate totals (same as index method)
        $subtotal = 0;
        $addOnsTotal = 0;
        $ticketCounts = array_count_values(array_column($attendees, 'ticketTypeId'));

        foreach ($ticketCounts as $ticketTypeId => $quantity) {
            $ticketType = TicketType::find($ticketTypeId);
            if ($ticketType) {
                $subtotal += $ticketType->price * $quantity;
            }
        }

        foreach ($attendees as $attendee) {
            if (! empty($attendee['add_ons'])) {
                foreach ($attendee['add_ons'] as $addOnId) {
                    $addOn = \App\Models\AddOn::find($addOnId);
                    if ($addOn) {
                        $addOnsTotal += $addOn->price;
                    }
                }
            }
        }

        $couponDiscount = 0;
        $couponCode = null;
        if (session()->has('coupon')) {
            $coupon = session('coupon');
            $couponCode = $coupon['code'];
            if ($coupon['type'] === 'fixed') {
                $couponDiscount = $coupon['value'];
            } else {
                $couponDiscount = ($subtotal + $addOnsTotal) * ($coupon['value'] / 100);
            }
        }

        $subtotalWithAddons = $subtotal + $addOnsTotal;
        $taxRate = $event->taxRate ? $event->taxRate->rate : 0;
        $taxes = ($subtotalWithAddons - $couponDiscount) * ($taxRate / 100);
        $grandTotal = max(0, $subtotalWithAddons - $couponDiscount + $taxes);

        // Create order in database
        $order = DB::transaction(function () use ($request, $attendees, $subtotal, $couponDiscount, $couponCode, $taxes, $grandTotal) {
            // Create order
            $order = Order::create([
                'order_number' => 'ORD-'.strtoupper(Str::random(10)),
                'user_id' => Auth::check() ? Auth::user()->id : null,
                'buyer_name' => $request->buyer_name,
                'buyer_email' => $request->buyer_email,
                'buyer_phone' => $request->buyer_phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => State::find($request->state)?->name ?? $request->state,
                'country' => Country::find($request->country)?->name ?? $request->country,
                'pincode' => $request->pincode,
                'subtotal' => $subtotal,
                'coupon_code' => $couponCode,
                'discount_amount' => $couponDiscount,
                'tax_amount' => $taxes,
                'grand_total' => $grandTotal,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'currency' => config('payment.default.currency', 'INR'),
            ]);

            // Create tickets for each attendee
            foreach ($attendees as $attendee) {
                $ticketType = TicketType::find($attendee['ticketTypeId']);

                $ticket = Ticket::create([
                    'order_id' => $order->id,
                    'ticket_type_id' => $attendee['ticketTypeId'],
                    'uuid' => (string) Str::uuid(),
                    'attendee_name' => $attendee['name'],
                    'attendee_email' => $attendee['email'],
                    'attendee_phone' => $attendee['phone'] ?? null,
                    'max_entries' => $ticketType->max_entries ?? 1,
                ]);

                // Attach add-ons to ticket
                if (! empty($attendee['add_ons'])) {
                    $ticket->addOns()->attach($attendee['add_ons']);
                }
            }

            // Process payment
            $gateway = PaymentFactory::getGateway($request->payment_method);
            $paymentResult = $gateway->processPayment($order, $request);

            if (! $paymentResult['success']) {
                throw new \Exception($paymentResult['message']);
            }

            return $order;
        });

        // Clear all session data
        $eventId = $bookingDetails['event_id'] ?? null;
        session()->forget('booking_details');
        session()->forget('coupon');
        session()->forget('billing_data');
        if ($eventId) {
            session()->forget("booking_tickets_{$eventId}");
            session()->forget("booking_attendees_{$eventId}");
        }

        // Fire event for notifications
        event(new OrderCreated($order));

        return redirect()->route('payment.success', ['order' => $order->order_number])
            ->with('success', 'Payment successful! Your tickets have been booked.');
    }

    /**
     * Initiate a Razorpay order (AJAX endpoint for frontend checkout)
     */
    public function initRazorpay(Request $request)
    {
        $bookingDetails = session('booking_details');

        if (! $bookingDetails) {
            return response()->json(['success' => false, 'message' => 'No booking details found.'], 400);
        }

        $event = Event::findOrFail($bookingDetails['event_id']);
        $attendees = $bookingDetails['attendees'];

        // Calculate totals
        $subtotal = 0;
        $addOnsTotal = 0;
        $ticketCounts = array_count_values(array_column($attendees, 'ticketTypeId'));

        foreach ($ticketCounts as $ticketTypeId => $quantity) {
            $ticketType = TicketType::find($ticketTypeId);
            if ($ticketType) {
                $subtotal += $ticketType->price * $quantity;
            }
        }

        foreach ($attendees as $attendee) {
            if (! empty($attendee['add_ons'])) {
                foreach ($attendee['add_ons'] as $addOnId) {
                    $addOn = \App\Models\AddOn::find($addOnId);
                    if ($addOn) {
                        $addOnsTotal += $addOn->price;
                    }
                }
            }
        }

        $couponDiscount = 0;
        if (session()->has('coupon')) {
            $coupon = session('coupon');
            if ($coupon['type'] === 'fixed') {
                $couponDiscount = $coupon['value'];
            } else {
                $couponDiscount = ($subtotal + $addOnsTotal) * ($coupon['value'] / 100);
            }
        }

        $subtotalWithAddons = $subtotal + $addOnsTotal;
        $taxRate = $event->taxRate ? $event->taxRate->rate : 0;
        $taxes = ($subtotalWithAddons - $couponDiscount) * ($taxRate / 100);
        $grandTotal = max(0, $subtotalWithAddons - $couponDiscount + $taxes);

        $currency = config('payment.default.currency', 'INR');
        $receipt = 'TMP-'.strtoupper(\Illuminate\Support\Str::random(10));

        $gateway = PaymentFactory::getGateway('razorpay');
        $result = $gateway->initiatePayment((int) ($grandTotal * 100), $currency, $receipt);

        if (! $result['success']) {
            return response()->json($result, 500);
        }

        $result['razorpay_key_id'] = config('services.razorpay.key_id');
        $result['amount'] = (int) ($grandTotal * 100);
        $result['currency'] = $currency;
        $result['prefill'] = [
            'name' => $request->input('buyer_name', ''),
            'email' => $request->input('buyer_email', ''),
            'contact' => $request->input('buyer_phone', ''),
        ];

        return response()->json($result);
    }

    /**
     * Initiate a Stripe Checkout Session (AJAX endpoint)
     */
    public function initStripePayment(Request $request)
    {
        $bookingDetails = session('booking_details');

        if (! $bookingDetails) {
            return response()->json(['success' => false, 'message' => 'No booking details found.'], 400);
        }

        $event = Event::findOrFail($bookingDetails['event_id']);

        if ($event->end_datetime->isPast()) {
            session()->forget('booking_details');
            return response()->json(['success' => false, 'message' => 'This event has already passed.'], 400);
        }

        $attendees = $bookingDetails['attendees'];

        // Calculate totals
        $subtotal = 0;
        $addOnsTotal = 0;
        $ticketCounts = array_count_values(array_column($attendees, 'ticketTypeId'));

        foreach ($ticketCounts as $ticketTypeId => $quantity) {
            $ticketType = TicketType::find($ticketTypeId);
            if ($ticketType) {
                $subtotal += $ticketType->price * $quantity;
            }
        }

        foreach ($attendees as $attendee) {
            if (! empty($attendee['add_ons'])) {
                foreach ($attendee['add_ons'] as $addOnId) {
                    $addOn = \App\Models\AddOn::find($addOnId);
                    if ($addOn) {
                        $addOnsTotal += $addOn->price;
                    }
                }
            }
        }

        $couponDiscount = 0;
        if (session()->has('coupon')) {
            $coupon = session('coupon');
            if ($coupon['type'] === 'fixed') {
                $couponDiscount = $coupon['value'];
            } else {
                $couponDiscount = ($subtotal + $addOnsTotal) * ($coupon['value'] / 100);
            }
        }

        $subtotalWithAddons = $subtotal + $addOnsTotal;
        $taxRate = $event->taxRate ? $event->taxRate->rate : 0;
        $taxes = ($subtotalWithAddons - $couponDiscount) * ($taxRate / 100);
        $grandTotal = max(0, $subtotalWithAddons - $couponDiscount + $taxes);

        $currency = config('payment.default.currency', 'INR');

        // Store billing info in session so stripeSuccess can access it
        session([
            'billing_data' => [
                'buyer_name' => $request->input('buyer_name'),
                'buyer_email' => $request->input('buyer_email'),
                'buyer_phone' => $request->input('buyer_phone'),
                'address' => $request->input('address'),
                'city' => $request->input('city'),
                'state' => State::find($request->input('state'))?->name ?? $request->input('state'),
                'country' => Country::find($request->input('country'))?->name ?? $request->input('country'),
                'pincode' => $request->input('pincode'),
            ],
        ]);

        $successUrl = route('payment.stripe.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = route('payment.stripe.cancel', [], true);

        $countryId = $request->input('country');
        $countryName = $countryId ? optional(\App\Models\Country::find($countryId))->name : null;

        $customer = [
            'name' => $request->input('buyer_name'),
            'email' => $request->input('buyer_email'),
            'phone' => $request->input('buyer_phone'),
            'address' => [
                'line1' => $request->input('address'),
                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'postal_code' => $request->input('pincode'),
                'country' => $countryName,
            ],
        ];

        $gateway = PaymentFactory::getGateway('stripe');
        $result = $gateway->initiatePayment(
            (int) ($grandTotal * 100),
            $currency,
            $successUrl,
            $cancelUrl,
            $customer
        );

        if (! $result['success']) {
            return response()->json($result, 500);
        }

        return response()->json($result);
    }

    /**
     * Stripe success callback (redirect from Stripe Checkout)
     */
    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (empty($sessionId)) {
            return redirect()->route('checkout.show')->with('error', 'Invalid payment response.');
        }

        $bookingDetails = session('booking_details');

        if (! $bookingDetails) {
            return redirect()->route('home')->with('error', 'Session expired. Please book again.');
        }

        $event = Event::findOrFail($bookingDetails['event_id']);

        if ($event->end_datetime->isPast()) {
            session()->forget('booking_details');
            return redirect()->route('home')->with('error', 'This event has already passed.');
        }

        $attendees = $bookingDetails['attendees'];

        // Calculate totals
        $subtotal = 0;
        $addOnsTotal = 0;
        $ticketCounts = array_count_values(array_column($attendees, 'ticketTypeId'));

        foreach ($ticketCounts as $ticketTypeId => $quantity) {
            $ticketType = TicketType::find($ticketTypeId);
            if ($ticketType) {
                $subtotal += $ticketType->price * $quantity;
            }
        }

        foreach ($attendees as $attendee) {
            if (! empty($attendee['add_ons'])) {
                foreach ($attendee['add_ons'] as $addOnId) {
                    $addOn = \App\Models\AddOn::find($addOnId);
                    if ($addOn) {
                        $addOnsTotal += $addOn->price;
                    }
                }
            }
        }

        $couponDiscount = 0;
        $couponCode = null;
        if (session()->has('coupon')) {
            $coupon = session('coupon');
            $couponCode = $coupon['code'];
            if ($coupon['type'] === 'fixed') {
                $couponDiscount = $coupon['value'];
            } else {
                $couponDiscount = ($subtotal + $addOnsTotal) * ($coupon['value'] / 100);
            }
        }

        $subtotalWithAddons = $subtotal + $addOnsTotal;
        $taxRate = $event->taxRate ? $event->taxRate->rate : 0;
        $taxes = ($subtotalWithAddons - $couponDiscount) * ($taxRate / 100);
        $grandTotal = max(0, $subtotalWithAddons - $couponDiscount + $taxes);

        $billingData = session('billing_data', []);

        // Create order and process payment
        try {
            $order = DB::transaction(function () use ($sessionId, $attendees, $subtotal, $couponDiscount, $couponCode, $taxes, $grandTotal, $billingData) {
                $order = Order::create([
                    'order_number' => 'ORD-'.strtoupper(Str::random(10)),
                    'user_id' => Auth::check() ? Auth::user()->id : null,
                    'buyer_name' => $billingData['buyer_name'] ?? null,
                    'buyer_email' => $billingData['buyer_email'] ?? null,
                    'buyer_phone' => $billingData['buyer_phone'] ?? null,
                    'address' => $billingData['address'] ?? null,
                    'city' => $billingData['city'] ?? null,
                    'state' => isset($billingData['state']) ? (State::find($billingData['state'])?->name ?? $billingData['state']) : null,
                    'country' => isset($billingData['country']) ? (Country::find($billingData['country'])?->name ?? $billingData['country']) : null,
                    'pincode' => $billingData['pincode'] ?? null,
                    'subtotal' => $subtotal,
                    'coupon_code' => $couponCode,
                    'discount_amount' => $couponDiscount,
                    'tax_amount' => $taxes,
                    'grand_total' => $grandTotal,
                    'payment_method' => 'stripe',
                    'status' => 'pending',
                    'currency' => config('payment.default.currency', 'INR'),
                ]);

                foreach ($attendees as $attendee) {
                    $ticketType = TicketType::find($attendee['ticketTypeId']);

                    $ticket = Ticket::create([
                        'order_id' => $order->id,
                        'ticket_type_id' => $attendee['ticketTypeId'],
                        'uuid' => (string) Str::uuid(),
                        'attendee_name' => $attendee['name'],
                        'attendee_email' => $attendee['email'],
                        'attendee_phone' => $attendee['phone'] ?? null,
                        'max_entries' => $ticketType->max_entries ?? 1,
                    ]);

                    if (! empty($attendee['add_ons'])) {
                        $ticket->addOns()->attach($attendee['add_ons']);
                    }
                }

                // Add session_id to request so processPayment can read it
                $paymentRequest = new Request([
                    'stripe_session_id' => $sessionId,
                ]);

                $gateway = PaymentFactory::getGateway('stripe');
                $paymentResult = $gateway->processPayment($order, $paymentRequest);

                if (! $paymentResult['success']) {
                    throw new \Exception($paymentResult['message']);
                }

                return $order;
            });

            // Clear session
            $eventId = $bookingDetails['event_id'] ?? null;
            session()->forget('booking_details');
            session()->forget('coupon');
            session()->forget('billing_data');
            if ($eventId) {
                session()->forget("booking_tickets_{$eventId}");
                session()->forget("booking_attendees_{$eventId}");
            }

            event(new OrderCreated($order));

            return redirect()->route('payment.success', ['order' => $order->order_number])
                ->with('success', 'Payment successful! Your tickets have been booked.');
        } catch (\Exception $e) {
            return redirect()->route('payment.failure')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Stripe cancel callback (redirect from Stripe Checkout)
     */
    public function stripeCancel(Request $request)
    {
        return redirect()->route('checkout.show')
            ->with('error', 'Payment was cancelled. Please try again.');
    }

    /**
     * Display success page
     */
    public function success(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return view('frontend.payment-success', compact('order'));
    }

    /**
     * Display failure page
     */
    public function failure(Request $request)
    {
        return view('frontend.payment-failure')->with('error', $request->message ?? 'Payment failed. Please try again.');
    }
}
