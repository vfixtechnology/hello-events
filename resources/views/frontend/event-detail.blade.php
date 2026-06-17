@extends('frontend.layouts.app')
@section('content')
    <!-- Main Content -->
    <div style="margin-top:50px;" class="gemini-glow-bg">
    <div class="container">
        @php $eventTz = $event->timezone ?? config('app.timezone'); @endphp
        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Event Banner -->
                    <img src="{{ $event->image() }}" class="img-fluid rounded mb-4"
                        alt="{{ $event->title }}">

                <!-- Event Title and Badge -->
                <div class="mb-3">
                    <div class="row g-4 align-items-center">
                        <div class="col-12 col-md">
                            <h1 class="h2 mb-0">
                                {{ $event->title }}
                            </h1>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#shareModal"
                                data-url="{{ url()->current() }}"
                                data-title="{{ $event->title }}">
                                <i class="fas fa-share-alt"></i>
                            </button>
                        </div>
                        <div class="col-auto ms-md-auto">
                            @if($event->end_datetime->setTimezone($eventTz)->isPast())
                                <span class="badge badge-passed">Expired</span>
                            @else
                                <span class="badge badge-upcoming">Upcoming</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Event Info -->
                <div class="row mb-4">
                    <div class="col-md-6" data-aos="fade-up">
                        <div class="event-info-item bg-white">
                            <div class="event-info-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <strong>Date & Time</strong><br>
                                <span class="text-muted">{{ $event->start_datetime->setTimezone($eventTz)->format('d M Y') }} at
                                    {{ $event->start_datetime->setTimezone($eventTz)->format('g:i A') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="event-info-item bg-white">
                            <div class="event-info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <strong>Venue</strong><br>
                                <span class="text-muted">
                                    {{ $event->venue }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                @unless($event->end_datetime->setTimezone($eventTz)->isPast())
                <!-- Countdown Timer -->
                <div class="countdown-timer" style="background: #1a1a2e !important;" data-aos="fade-up">
                    <h5 class="mb-3">Event Starts In:</h5>
                    <div class="d-flex justify-content-center" id="countdown">
                        <div class="countdown-box">
                            <span class="countdown-number" id="days">00</span>
                            <span class="countdown-label">Days</span>
                        </div>
                        <div class="countdown-box">
                            <span class="countdown-number" id="hours">00</span>
                            <span class="countdown-label">Hours</span>
                        </div>
                        <div class="countdown-box">
                            <span class="countdown-number" id="minutes">00</span>
                            <span class="countdown-label">Minutes</span>
                        </div>
                        <div class="countdown-box">
                            <span class="countdown-number" id="seconds">00</span>
                            <span class="countdown-label">Seconds</span>
                        </div>
                    </div>
                </div>
                @endunless

                <!-- About Event -->
                <div class="mb-5" data-aos="fade-up">
                    <h3 class="mb-3">About this Event</h3>
                    {!! $event->body !!}
                </div>

                <!-- Ticket Selection -->
                @unless($event->end_datetime->setTimezone($eventTz)->isPast())
                <div class="mb-5" data-aos="fade-up">
                    <h3 class="mb-4">Choose Your Tickets</h3>

                    @foreach ($event->ticketTypes as $ticket)
                        <div class="ticket-card bg-white" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 5) * 50 }}">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="mb-2">
                                        {{ $ticket->title }}
                                        @if(isset($ticket->max_entries) && $ticket->max_entries > 1)
                                        <span class="badge bg-info ms-2">{{ $ticket->max_entries }} Entries</span>
                                        @endif
                                    </h5>
                                    <p class="text-muted mb-0">
                                        {{ $ticket->body }}
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <div class="h4 text-primary mb-2">@if($ticket->compare_at_price)<span class="text-muted text-decoration-line-through me-2">{{ Number::currency($ticket->compare_at_price, config('app.currency')) }}</span>@endif{{ Number::currency($ticket->price, config('app.currency')) }}</div>
                                        <a href="{{ route('booking',$ticket->event->slug) }}" class="btn btn-primary btn-sm rounded-pill px-3">Select</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endunless
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-4">
                <div class="info-sidebar">
                    <!-- Map -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-map-marker-alt me-2"></i>Event Location</h6>

                            @if($event->map_link && $coords = $event->map_coords)
                                <a href="{{ $event->map_link }}" target="_blank">
                                    <iframe src="https://www.openstreetmap.org/export/embed.html?bbox={{ $coords['lng'] - 0.01 }},{{ $coords['lat'] - 0.01 }},{{ $coords['lng'] + 0.01 }},{{ $coords['lat'] + 0.01 }}&layer=mapnik&marker={{ $coords['lat'] }},{{ $coords['lng'] }}"
                                        style="border:0; width:100%; height:200px; border-radius:8px;" loading="lazy"></iframe>
                                </a>
                            @elseif($event->map_link)
                                <a href="{{ $event->map_link }}" target="_blank">
                                    <img src="https://placehold.co/350x200/0d6efd/ffffff?text=View+on+Google+Maps"
                                        class="img-fluid rounded mb-3" alt="View on Google Maps" style="width: 100%;">
                                </a>
                            @endif

                            <div class="mt-3">
                                <p class="mb-2">
                                    <i class="fas fa-building text-primary me-2"></i>
                                    <strong>Venue:</strong> {{ $event->venue }}
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    <strong>Location:</strong> {{ $event->location }}{{ $event->city ? ', '.$event->city : '' }}{{ $event->state ? ', '.$event->state : '' }}
                                </p>
                            </div>

                            @if($event->map_link)
                            <a href="{{ $event->map_link }}" target="_blank" class="btn btn-primary w-100 mt-2 rounded-pill">
                                <i class="fas fa-map-marked-alt me-1 "></i> View on Google Maps
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Organizer Info -->
                    @php
                        $hasOrganizer = $event->host_name || $event->host_email || $event->host_phone || $event->host_website
                            || $event->host_facebook || $event->host_instagram || $event->host_twitter || $event->host_linkedin
                            || $event->getFirstMediaUrl('organizer_image', 'thumb');
                    @endphp
                    @if($hasOrganizer)
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if($event->getFirstMediaUrl('organizer_image', 'thumb'))
                                <img src="{{ $event->getFirstMediaUrl('organizer_image', 'thumb') }}"
                                    class="organizer-avatar-img mb-3" alt="{{ $event->host_name ?? 'Organizer' }}">
                                @else
                                <div class="organizer-avatar mb-3">
                                    <span>{{ substr($event->host_name ?? 'O', 0, 1) }}</span>
                                </div>
                                @endif
                                <h5 class="mb-1">{{ $event->host_name ?: 'Event Organizer' }}</h5>
                                <p class="text-muted small mb-0">Event Organizer</p>
                            </div>

                            @if($event->host_email || $event->host_phone || $event->host_website)
                            <hr class="my-3">
                            <div class="text-start">
                                @if($event->host_email)
                                <p class="mb-2">
                                    <i class="fas fa-envelope me-2"></i>
                                    <a href="mailto:{{ $event->host_email }}" class="text-decoration-none text-reset">{{ $event->host_email }}</a>
                                </p>
                                @endif

                                @if($event->host_phone)
                                <p class="mb-2">
                                    <i class="fas fa-phone me-2"></i>
                                    <a href="tel:{{ $event->host_phone }}" class="text-decoration-none text-reset">{{ $event->host_phone }}</a>
                                </p>
                                @endif

                                @if($event->host_website)
                                <p class="mb-2">
                                    <i class="fas fa-globe me-2"></i>
                                    <a href="{{ $event->host_website }}" target="_blank" class="text-decoration-none text-reset">{{ $event->host_website }}</a>
                                </p>
                                @endif
                            </div>
                            @endif

                            @if($event->host_facebook || $event->host_instagram || $event->host_twitter || $event->host_linkedin)
                            <hr class="my-3">
                            <div class="d-flex justify-content-center gap-2">
                                @if($event->host_facebook)
                                <a href="{{ $event->host_facebook }}" target="_blank" class="social-icon" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                                @endif
                                @if($event->host_instagram)
                                <a href="{{ $event->host_instagram }}" target="_blank" class="social-icon" title="Instagram"><i class="fab fa-instagram"></i></a>
                                @endif
                                @if($event->host_twitter)
                                <a href="{{ $event->host_twitter }}" target="_blank" class="social-icon" title="Twitter"><i class="fab fa-twitter"></i></a>
                                @endif
                                @if($event->host_linkedin)
                                <a href="{{ $event->host_linkedin }}" target="_blank" class="social-icon" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
@stop

{{-- Share Modal --}}
<div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Share Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="fw-semibold mb-3 fs-5" id="shareTitle"></p>
                <div class="d-flex flex-nowrap gap-2 justify-content-center mb-4">
                    <a href="#" id="shareWhatsapp" target="_blank" class="text-decoration-none text-center" style="width: 70px;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1" style="width: 50px; height: 50px; background: #25D366;">
                            <i class="fab fa-whatsapp text-white fs-5"></i>
                        </div>
                        <small class="text-muted">WhatsApp</small>
                    </a>
                    <a href="#" id="shareEmail" target="_blank" class="text-decoration-none text-center" style="width: 70px;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1" style="width: 50px; height: 50px; background: #6c757d;">
                            <i class="fas fa-envelope text-white fs-5"></i>
                        </div>
                        <small class="text-muted">Email</small>
                    </a>
                    <a href="#" id="shareFacebook" target="_blank" class="text-decoration-none text-center" style="width: 70px;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1" style="width: 50px; height: 50px; background: #1877F2;">
                            <i class="fab fa-facebook-f text-white fs-5"></i>
                        </div>
                        <small class="text-muted">Facebook</small>
                    </a>
                    <a href="#" id="shareTwitter" target="_blank" class="text-decoration-none text-center" style="width: 70px;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1" style="width: 50px; height: 50px; background: #000;">
                            <i class="fab fa-x-twitter text-white fs-5"></i>
                        </div>
                        <small class="text-muted">X</small>
                    </a>
                    <a href="#" id="shareLinkedin" target="_blank" class="text-decoration-none text-center" style="width: 70px;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1" style="width: 50px; height: 50px; background: #0A66C2;">
                            <i class="fab fa-linkedin-in text-white fs-5"></i>
                        </div>
                        <small class="text-muted">LinkedIn</small>
                    </a>
                    <a href="#" id="shareTelegram" target="_blank" class="text-decoration-none text-center" style="width: 70px;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1" style="width: 50px; height: 50px; background: #0088cc;">
                            <i class="fab fa-telegram-plane text-white fs-5"></i>
                        </div>
                        <small class="text-muted">Telegram</small>
                    </a>
                </div>
                <hr>
                <div class="input-group input-group-lg">
                    <input type="text" class="form-control" id="shareUrl" readonly>
                    <button class="btn btn-primary" type="button" id="copyLinkBtn">Copy</button>
                </div>
            </div>
        </div>
    </div>
</div>

@section('css')
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var startTime = new Date('{{ $event->start_datetime->format('Y/m/d H:i:s') }}').getTime();

        function updateCountdown() {
            var now = new Date().getTime();
            var diff = startTime - now;

            if (diff <= 0) {
                document.getElementById('days').textContent = '00';
                document.getElementById('hours').textContent = '00';
                document.getElementById('minutes').textContent = '00';
                document.getElementById('seconds').textContent = '00';
                return;
            }

            var days = Math.floor(diff / (1000 * 60 * 60 * 24));
            var hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((diff % (1000 * 60)) / 1000);

            document.getElementById('days').textContent = String(days).padStart(2, '0');
            document.getElementById('hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    });

    // Share modal
    const shareModal = document.getElementById('shareModal');
    if (shareModal) {
        shareModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            const url = btn.getAttribute('data-url');
            const title = btn.getAttribute('data-title');
            const encodedUrl = encodeURIComponent(url);
            const encodedTitle = encodeURIComponent(title);

            document.getElementById('shareTitle').textContent = title;
            document.getElementById('shareUrl').value = url;

            document.getElementById('shareWhatsapp').href = 'https://wa.me/?text=' + encodedTitle + '%20' + encodedUrl;
            document.getElementById('shareEmail').href = 'mailto:?subject=' + encodedTitle + '&body=' + encodedUrl;
            document.getElementById('shareFacebook').href = 'https://www.facebook.com/sharer/sharer.php?u=' + encodedUrl;
            document.getElementById('shareTwitter').href = 'https://twitter.com/intent/tweet?text=' + encodedTitle + '&url=' + encodedUrl;
            document.getElementById('shareLinkedin').href = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodedUrl;
            document.getElementById('shareTelegram').href = 'https://t.me/share/url?url=' + encodedUrl + '&text=' + encodedTitle;
        });

        document.getElementById('copyLinkBtn').addEventListener('click', function () {
            const input = document.getElementById('shareUrl');
            input.select();
            navigator.clipboard.writeText(input.value).then(function () {
                const btn = document.getElementById('copyLinkBtn');
                btn.textContent = 'Copied!';
                setTimeout(function () { btn.textContent = 'Copy'; }, 2000);
            });
        });
    }
</script>
@stop
