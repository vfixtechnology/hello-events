@extends('frontend.layouts.app')

@section('content')
    <div style="margin-top:50px;" class="gemini-glow-bg">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-none" role="alert" id="success-alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-none" role="alert" id="error-alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <strong>Whoops!</strong> There were some problems with your input.
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            {{-- Event Info Header --}}
            <div class="col-12 mb-3">
                <h2 class="fw-bold">{{ $event->title }}</h2>
                <p class="text-muted">
                    <i class="bi bi-calendar-event"></i> {{ $event->start_datetime->format('D, M d, Y') }} at
                    {{ $event->start_datetime->format('g:i A') }} |
                    <i class="bi bi-geo-alt-fill"></i> {{ $event->venue }}
                </p>
            </div>

            {{-- Main Content (Left Side) --}}
            <div class="col-lg-8 order-2 order-md-1 ">
                <form id="booking-form" action="{{ route('booking.process', $event) }}" method="POST">
                    @csrf

                    <div class="card shadow-sm p-4 mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-ticket-fill me-2 text-primary"></i> Select Tickets</h5>

                        @php
                            $selectedTickets = session("booking_tickets_{$event->id}", []);
                            $totalTickets = array_sum($selectedTickets);
                        @endphp

                        @if($totalTickets > 0)
                            @foreach($selectedTickets as $ticketTypeId => $quantity)
                                @php
                                    $ticketType = $event->ticketTypes->firstWhere('id', $ticketTypeId);
                                    if (!$ticketType) continue;
                                    $lineTotal = $ticketType->price * $quantity;
                                @endphp
                                <div class="card shadow-sm mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="fw-bold mb-1">{{ $ticketType->title }}</h6>
                                                <p class="text-muted small mb-0">
                                                    {{ Number::currency($ticketType->price, config('app.currency')) }} × {{ $quantity }} =
                                                    <span class="fw-bold text-primary">{{ Number::currency($lineTotal, config('app.currency')) }}</span>
                                                </p>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-primary">{{ $quantity }} Tickets</span>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="removeTicketFromTable({{ $ticketTypeId }}, {{ $quantity }})"
                                                    title="Remove">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm rounded-pill" type="button" data-bs-toggle="modal" data-bs-target="#ticketModal">
                                    <i class="bi bi-plus-circle me-1"></i> Add More Tickets
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="clearAllTickets()">
                                    <i class="bi bi-trash me-1"></i> Clear All
                                </button>
                            </div>
                        @else
                            <p class="text-muted">Click the button to choose your ticket types and quantities.</p>
                            <button class="btn btn-lg btn-outline-primary border-dotted rounded-pill" type="button" data-bs-toggle="modal" data-bs-target="#ticketModal">
                                <i class="bi bi-plus-circle me-2 "></i> Add / Edit Tickets
                            </button>
                        @endif
                    </div>

                    <div class="">
                        <h5 class="fw-bold mb-3"><i class="bi bi-people-fill me-2 text-primary"></i> Attendee Information
                            (<span id="total-attendees-required">{{ $totalTickets }}</span> Required)</h5>

                        @if($totalTickets > 0)
                            @php
                                $attendeeIndex = 0;
                                $sessionAttendees = $attendeeData ?? [];
                            @endphp
                            @foreach($selectedTickets as $ticketTypeId => $quantity)
                                @php
                                    $ticketType = $event->ticketTypes->firstWhere('id', $ticketTypeId);
                                    if (!$ticketType) continue;
                                @endphp
                                <div class="card border mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="fw-bold mb-0">{{ $ticketType->title }} <span class="badge bg-primary">{{ $quantity }} attendees</span></h6>
                                    </div>
                                    <div class="card-body">
                                        @for($i = 0; $i < $quantity; $i++)
                                            <div class="attendee-form border rounded p-3 mb-3 bg-white" id="attendee-{{ $attendeeIndex }}">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="fw-bold text-primary mb-0">Attendee {{ $attendeeIndex + 1 }}</h6>
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" onclick="removeAttendee({{ $ticketTypeId }}, {{ $attendeeIndex }}, {{ $ticketType->min_quantity ?? 1 }})">
                                                        <i class="bi bi-trash"></i> Remove
                                                    </button>
                                                </div>
                                                <div class="row g-3">
                                                    <input type="hidden" name="attendees[{{ $attendeeIndex }}][ticketTypeId]" value="{{ $ticketTypeId }}">
                                                    <div class="col-md-12">
                                                        <label class="form-label mb-1">Full Name</label>
                                                        <input type="text" class="form-control" name="attendees[{{ $attendeeIndex }}][name]" value="{{ old('attendees.' . $attendeeIndex . '.name', $sessionAttendees[$attendeeIndex]['name'] ?? '') }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label mb-1">Email Address</label>
                                                        <input type="email" class="form-control" name="attendees[{{ $attendeeIndex }}][email]" value="{{ old('attendees.' . $attendeeIndex . '.email', $sessionAttendees[$attendeeIndex]['email'] ?? '') }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label mb-1">Phone Number</label>
                                                        <input type="tel" class="form-control" name="attendees[{{ $attendeeIndex }}][phone]" value="{{ old('attendees.' . $attendeeIndex . '.phone', $sessionAttendees[$attendeeIndex]['phone'] ?? '') }}">
                                                    </div>
                                                </div>
                                                @if($event->addOns->isNotEmpty())
                                                <h6 class="fw-bold mt-3 mb-2">Add-ons:</h6>
                                                <div class="d-flex flex-wrap">
                                                    @php
                                                        $savedAddons = $sessionAttendees[$attendeeIndex]['add_ons'] ?? [];
                                                    @endphp
                                                    @foreach($event->addOns as $addOn)
                                                        <div class="form-check me-3">
                                                            <input class="form-check-input" type="checkbox" name="attendees[{{ $attendeeIndex }}][add_ons][]" value="{{ $addOn->id }}" id="addon-{{ $addOn->id }}-{{ $attendeeIndex }}"
                                                                {{ (is_array(old('attendees.' . $attendeeIndex . '.add_ons')) && in_array($addOn->id, old('attendees.' . $attendeeIndex . '.add_ons'))) || (is_array($savedAddons) && in_array($addOn->id, $savedAddons)) ? 'checked' : '' }}>
                                                            <label class="form-check-label small" for="addon-{{ $addOn->id }}-{{ $attendeeIndex }}">
                                                                {{ $addOn->title }} (<span class="fw-bold">@if($addOn->compare_at_price && $addOn->compare_at_price > $addOn->price)<span class="text-decoration-line-through text-muted me-1">{{ Number::currency($addOn->compare_at_price, config('app.currency')) }}</span>@endif{{ Number::currency($addOn->price, config('app.currency')) }}</span>)
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @endif
                                            </div>
                                            @php $attendeeIndex++; @endphp
                                        @endfor
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Please add tickets to see attendee forms.</p>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-lg btn-success w-100 rounded-pill" {{ $totalTickets == 0 ? 'disabled' : '' }}>
                        <i class="bi bi-arrow-right-circle me-2 "></i>Proceed to Checkout
                    </button>
                </form>
            </div>

            {{-- Sidebar (Right Side) --}}
            <div class="col-lg-4 order-1 order-md-2">
                <div class="card shadow-sm px-3 pt-3 pb-2 sticky-top" style="top: 80px; z-index: 100;">
                    <img src="{{ $event->image() }}" alt="{{ $event->title }}" class="img-fluid rounded">
                    <h5 class="fw-bold mb-3 mt-3">Order Summary</h5>
                    <p class="text-muted mb-1">{{ $event->title }}</p>
                    <p class="text-muted">
                        <i class="bi bi-calendar-event"></i> {{ $event->start_datetime->format('D, M d, Y') }} at
                        {{ $event->start_datetime->format('g:i A') }} |
                        <i class="bi bi-geo-alt-fill"></i> {{ $event->venue }}
                    </p>

                    <ul class="list-group list-group-flush mb-3">
                        @php
                            $subtotal = 0;
                        @endphp
                        @foreach($selectedTickets as $ticketTypeId => $quantity)
                            @php
                                $ticketType = $event->ticketTypes->firstWhere('id', $ticketTypeId);
                                if (!$ticketType) continue;
                                $lineTotal = $ticketType->price * $quantity;
                                $subtotal += $lineTotal;
                            @endphp
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ $ticketType->title }} (x{{ $quantity }})</span>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Hidden pricing - shown on checkout page --}}
                    <ul class="list-group border-0 list-group-flush mb-3 d-none">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Subtotal</span>
                            <span id="summary-subtotal">{{ Number::currency($subtotal, config('app.currency')) }}</span>
                        </li>
                        @php
                            $taxRate = $event->taxRate ? $event->taxRate->rate : 0;
                            $taxes = $subtotal * ($taxRate / 100);
                        @endphp
                        @if($taxes > 0)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $event->taxRate->title ?? 'Tax' }} ({{ $taxRate }}%)</span>
                            <span id="summary-taxes">{{ Number::currency($taxes, config('app.currency')) }}</span>
                        </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between fw-bold fs-5">
                            <span>Total Payable</span>
                            <span class="text-primary" id="summary-total">{{ Number::currency($subtotal + $taxes, config('app.currency')) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Ticket Selection Modal (Outside main form to avoid nested forms) --}}
    <div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketModalLabel">Select Your Tickets</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body scrollable-modal-body">
                    @foreach ($event->ticketTypes as $ticketType)
                        @php
                            $available = $ticketType->available_tickets;
                            $soldOut = $available <= 0;
                            $currentQty = $selectedTickets[$ticketType->id] ?? 0;
                        @endphp
                        <div class="d-flex justify-content-between align-items-center border-bottom py-3 {{ $soldOut ? 'opacity-50' : '' }}">
                            <div>
                                <div class="d-flex justify-content-between">
                                    <h6 class="fw-bold">{{ $ticketType->title }}</h6>
                                    @if($soldOut)
                                        <span class="badge bg-danger ms-2">Sold Out</span>
                                    @endif
                                    <p class="fw-bold mb-0 ms-auto pe-0 pe-sm-4 text-end">
                                        {{ Number::currency($ticketType->price, config('app.currency')) }}</p>
                                </div>
                                <p class="small text-muted mb-1 pe-3">{{ $ticketType->body }}</p>

                                <div class="mt-2">
                                    <small class="{{ $available > 5 ? 'text-success' : ($available > 0 ? 'text-warning' : 'text-danger') }}">
                                        @if($soldOut)
                                            Sold Out
                                        @else
                                            Available: {{ $available }} tickets
                                        @endif
                                    </small>
                                    @if ($ticketType->min_quantity > 1)
                                        <small class="text-muted ms-3"> | Min Purchase: {{ $ticketType->min_quantity }}</small>
                                    @endif
                                    @if(isset($ticketType->max_entries) && $ticketType->max_entries > 1)
                                        <small class="text-info ms-3"> | {{ $ticketType->max_entries }} Entries per ticket</small>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                @if(!$soldOut)
                                    <div class="qty-stepper">
                                        <button type="button" class="qty-btn qty-minus"
                                            onclick="updateQty('{{ $ticketType->id }}', -{{ $ticketType->min_quantity ?? 1 }}, {{ $available }})">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" form="apply-tickets-form" name="quantities[{{ $ticketType->id }}]"
                                            id="qty-{{ $ticketType->id }}"
                                            class="qty-input"
                                            min="0" max="{{ $available }}"
                                            value="{{ $currentQty }}" onchange="validateQty(this, {{ $available }})">
                                        <button type="button" class="qty-btn qty-plus"
                                            onclick="updateQty('{{ $ticketType->id }}', {{ $ticketType->min_quantity ?? 1 }}, {{ $available }})">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-muted">Sold Out</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('booking.tickets.apply', $event) }}" method="POST" id="apply-tickets-form">
                        @csrf
                        <button type="submit" class="btn btn-primary rounded-pill">Apply Tickets</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

@stop

@section('css')
@stop

@section('js')
    <script>
        // Show toast notifications on page load
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: @json(session('success')),
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: @json(session('error')),
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif
        });

        function updateQty(ticketTypeId, amount, maxAvailable) {
            const input = document.getElementById('qty-' + ticketTypeId);
            let currentQty = parseInt(input.value) || 0;
            let newQty = currentQty + amount;

            if (newQty < 0) newQty = 0;
            if (newQty > maxAvailable) newQty = maxAvailable;

            input.value = newQty;
        }

        function validateQty(input, maxAvailable) {
            let qty = parseInt(input.value) || 0;
            if (qty < 0) qty = 0;
            if (qty > maxAvailable) qty = maxAvailable;
            input.value = qty;
        }

        function removeAttendee(ticketTypeId, attendeeIndex, minQty) {
            let alertText = "Do you want to remove this attendee?";
            if (minQty > 1) {
                alertText = "This will remove " + minQty + " tickets (minimum purchase quantity).";
            }

            Swal.fire({
                title: 'Are you sure?',
                text: alertText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove them!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    var csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'CSRF token not found. Please refresh the page.',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        return;
                    }

                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("booking.tickets.remove", $event) }}';
                    form.style.display = 'none';

                    var csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken.content;

                    var ticketTypeInput = document.createElement('input');
                    ticketTypeInput.type = 'hidden';
                    ticketTypeInput.name = 'ticket_type_id';
                    ticketTypeInput.value = ticketTypeId;

                    var quantityInput = document.createElement('input');
                    quantityInput.type = 'hidden';
                    quantityInput.name = 'quantity';
                    quantityInput.value = 1;

                    form.appendChild(csrfInput);
                    form.appendChild(ticketTypeInput);
                    form.appendChild(quantityInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function clearAllTickets() {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will remove all selected tickets. Are you sure you want to clear all?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, clear all!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    var csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'CSRF token not found. Please refresh the page.',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        return;
                    }

                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("booking.tickets.clear", $event) }}';
                    form.style.display = 'none';

                    var csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken.content;

                    form.appendChild(csrfInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function removeTicketFromTable(ticketTypeId, currentQty) {
            let alertText = "Do you want to remove all " + currentQty + " tickets of this type?";

            Swal.fire({
                title: 'Are you sure?',
                text: alertText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove all!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    var csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'CSRF token not found. Please refresh the page.',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        return;
                    }

                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("booking.tickets.remove", $event) }}';
                    form.style.display = 'none';

                    var csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken.content;

                    var ticketTypeInput = document.createElement('input');
                    ticketTypeInput.type = 'hidden';
                    ticketTypeInput.name = 'ticket_type_id';
                    ticketTypeInput.value = ticketTypeId;

                    var quantityInput = document.createElement('input');
                    quantityInput.type = 'hidden';
                    quantityInput.name = 'quantity';
                    quantityInput.value = currentQty; // Remove all tickets

                    form.appendChild(csrfInput);
                    form.appendChild(ticketTypeInput);
                    form.appendChild(quantityInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@stop
