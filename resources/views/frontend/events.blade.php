@extends('frontend.layouts.app')
@section('content')
    {{-- Page Hero --}}
    <section class="position-relative d-flex align-items-center"
        style="margin-top: var(--nav-height); min-height: 280px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0d6efd 100%); overflow: hidden;">

        <div class="position-absolute"
            style="bottom: -30%; left: -10%; width: 400px; height: 400px; border-radius: 50%; background: radial-gradient(circle, rgba(124,58,237,0.12) 0%, transparent 70%); pointer-events: none;">
        </div>
        <div class="container position-relative">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8" data-aos="fade-up">
                    @if (isset($category))
                        <div class="mb-2">
                            <img src="{{ $category->image() }}" alt="{{ $category->title }}"
                                class="rounded-circle border border-2 border-white"
                                style="width: 64px; height: 64px; object-fit: cover;">
                        </div>
                        <h1 class="display-4 fw-bold text-white mb-3" style="letter-spacing: -0.5px;">{{ $category->title }}
                        </h1>
                        <p class="lead text-white-50 mb-0">
                            {{ $category->seo?->description ?? "Browse and book tickets for {$category->title} events" }}
                        </p>
                        <a href="{{ route('events') }}" class="btn btn-outline-light btn-sm rounded-pill mt-3 px-4">
                            <i class="fas fa-th-large me-1"></i> All Events
                        </a>
                    @else
                        <h1 class="display-4 fw-bold text-white mb-3" style="letter-spacing: -0.5px;">Explore Events</h1>
                        <p class="lead text-white-50 mb-0">Discover amazing events happening near you and book your tickets
                            today</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="gemini-glow-bg">
        <div class="container">
            @if ($upcomingEvents->isNotEmpty())
                <div class="section-header text-center" data-aos="fade-up">
                    <h2>Upcoming Events</h2>
                    <p>Don't miss out on these exciting experiences</p>
                    <div class="accent-line"></div>
                </div>
                <div class="row g-4 mb-5">
                    @foreach ($upcomingEvents as $event)
                        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                            <div class="event-card-modern">
                                <div class="img-wrap">
                                    <img src="{{ $event->image() }}" class="card-img-top" alt="{{ $event->title }}">
                                    <span class="badge bg-success position-absolute top-0 start-0 m-3">Upcoming</span>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="date-badge flex-shrink-0">
                                            <div class="month">{{ $event->start_datetime->format('M') }}</div>
                                            <div class="day">{{ $event->start_datetime->format('d') }}</div>
                                            <div class="year">{{ $event->start_datetime->format('Y') }}</div>
                                        </div>
                                        <div class="min-w-0">
                                            <h5 class="card-title">{{ $event->title }}</h5>
                                            <div class="meta mb-1">
                                                <i
                                                    class="fas fa-clock me-1"></i>{{ $event->start_datetime->format('g:i A') }}
                                            </div>
                                            <div class="meta">
                                                <i class="fas fa-map-marker-alt me-1"></i>{{ $event->venue }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <span class="attendee-count">
                                        <i class="fas fa-users me-1"></i>{{ $event->formatted_attendees }} Attending
                                    </span>
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary rounded-pill me-1"
                                            data-bs-toggle="modal" data-bs-target="#shareModal"
                                            data-url="{{ route('event.detail', $event->slug) }}"
                                            data-title="{{ $event->title }}">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                        <a href="{{ route('event.detail', $event->slug) }}"
                                            class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">View Event</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center" data-aos="fade-up">
                    {{ $upcomingEvents->links() }}
                </div>
            @elseif ($upcomingEvents->isEmpty() && $pastEvents->isEmpty())
                <div class="text-center py-5" data-aos="fade-up">
                    <div class="mb-3" style="font-size: 4rem; color: #cbd5e1;"><i class="fas fa-calendar-times"></i>
                    </div>
                    <h4 class="fw-bold text-muted">No events found</h4>
                    <p class="text-muted">
                        @if (isset($category))
                            No events in <strong>{{ $category->title }}</strong> yet. Check back later!
                        @else
                            Check back later for new events!
                        @endif
                    </p>
                    @if (isset($category))
                        <a href="{{ route('events') }}" class="btn btn-primary rounded-pill px-4 mt-2">
                            <i class="fas fa-th-large me-1"></i> Browse All Events
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>

    @if ($pastEvents->isNotEmpty())
        <section class="py-5">
            <div class="container">
                <div class="section-header text-center" data-aos="fade-up">
                    <h2>Past Events</h2>
                    <p>Catch up on events that have already happened</p>
                    <div class="accent-line"></div>
                </div>
                <div class="row g-4">
                    @foreach ($pastEvents as $event)
                        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                            <div class="event-card-modern">
                                <div class="img-wrap">
                                    <img src="{{ $event->image() }}" class="card-img-top" alt="{{ $event->title }}">
                                    <span class="badge bg-secondary position-absolute top-0 start-0 m-3">Past</span>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="date-badge flex-shrink-0"
                                            style="background: linear-gradient(135deg, #64748b, #94a3b8);">
                                            <div class="month">{{ $event->start_datetime->format('M') }}</div>
                                            <div class="day">{{ $event->start_datetime->format('d') }}</div>
                                            <div class="year">{{ $event->start_datetime->format('Y') }}</div>
                                        </div>
                                        <div class="min-w-0">
                                            <h5 class="card-title">{{ $event->title }}</h5>
                                            <div class="meta mb-1">
                                                <i
                                                    class="fas fa-clock me-1"></i>{{ $event->start_datetime->format('g:i A') }}
                                            </div>
                                            <div class="meta">
                                                <i class="fas fa-map-marker-alt me-1"></i>{{ $event->venue }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <span class="attendee-count">
                                        <i class="fas fa-users me-1"></i>{{ $event->formatted_attendees }} Attended
                                    </span>
                                    <div>
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill me-1"
                                            data-bs-toggle="modal" data-bs-target="#shareModal"
                                            data-url="{{ route('event.detail', $event->slug) }}"
                                            data-title="{{ $event->title }}">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                        <a href="{{ route('event.detail', $event->slug) }}"
                                            class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold">View
                                            Event</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-4" data-aos="fade-up">
                    {{ $pastEvents->links() }}
                </div>
            </div>
        </section>
    @endif


    {{-- Share Modal --}}
    <div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Share Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-semibold mb-3" id="shareTitle"></p>
                    <div class="d-flex flex-nowrap gap-2 justify-content-center">
                        <a href="#" id="shareWhatsapp" target="_blank" class="text-decoration-none text-center"
                            style="width: 70px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1"
                                style="width: 50px; height: 50px; background: #25D366;">
                                <i class="fab fa-whatsapp text-white fs-5"></i>
                            </div>
                            <small>WhatsApp</small>
                        </a>
                        <a href="#" id="shareEmail" target="_blank" class="text-decoration-none text-center"
                            style="width: 70px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1"
                                style="width: 50px; height: 50px; background: #6c757d;">
                                <i class="fas fa-envelope text-white fs-5"></i>
                            </div>
                            <small>Email</small>
                        </a>
                        <a href="#" id="shareFacebook" target="_blank" class="text-decoration-none text-center"
                            style="width: 70px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1"
                                style="width: 50px; height: 50px; background: #1877F2;">
                                <i class="fab fa-facebook-f text-white fs-5"></i>
                            </div>
                            <small>Facebook</small>
                        </a>
                        <a href="#" id="shareTwitter" target="_blank" class="text-decoration-none text-center"
                            style="width: 70px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1"
                                style="width: 50px; height: 50px; background: #000;">
                                <i class="fab fa-x-twitter text-white fs-5"></i>
                            </div>
                            <small>X</small>
                        </a>
                        <a href="#" id="shareLinkedin" target="_blank" class="text-decoration-none text-center"
                            style="width: 70px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1"
                                style="width: 50px; height: 50px; background: #0A66C2;">
                                <i class="fab fa-linkedin-in text-white fs-5"></i>
                            </div>
                            <small>LinkedIn</small>
                        </a>
                        <a href="#" id="shareTelegram" target="_blank" class="text-decoration-none text-center"
                            style="width: 70px;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1"
                                style="width: 50px; height: 50px; background: #0088cc;">
                                <i class="fab fa-telegram-plane text-white fs-5"></i>
                            </div>
                            <small>Telegram</small>
                        </a>
                    </div>
                    <hr>
                    <div class="input-group">
                        <input type="text" class="form-control" id="shareUrl" readonly>
                        <button class="btn btn-primary" type="button" id="copyLinkBtn">Copy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const shareModal = document.getElementById('shareModal');
            if (!shareModal) return;

            shareModal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                const url = btn.getAttribute('data-url');
                const title = btn.getAttribute('data-title');
                const encodedUrl = encodeURIComponent(url);
                const encodedTitle = encodeURIComponent(title);

                document.getElementById('shareTitle').textContent = title;
                document.getElementById('shareUrl').value = url;

                document.getElementById('shareWhatsapp').href = 'https://wa.me/?text=' + encodedTitle +
                    '%20' + encodedUrl;
                document.getElementById('shareEmail').href = 'mailto:?subject=' + encodedTitle + '&body=' +
                    encodedUrl;
                document.getElementById('shareFacebook').href =
                    'https://www.facebook.com/sharer/sharer.php?u=' + encodedUrl;
                document.getElementById('shareTwitter').href = 'https://twitter.com/intent/tweet?text=' +
                    encodedTitle + '&url=' + encodedUrl;
                document.getElementById('shareLinkedin').href =
                    'https://www.linkedin.com/sharing/share-offsite/?url=' + encodedUrl;
                document.getElementById('shareTelegram').href = 'https://t.me/share/url?url=' + encodedUrl +
                    '&text=' + encodedTitle;
            });

            document.getElementById('copyLinkBtn').addEventListener('click', function() {
                const input = document.getElementById('shareUrl');
                input.select();
                navigator.clipboard.writeText(input.value).then(function() {
                    const btn = document.getElementById('copyLinkBtn');
                    btn.textContent = 'Copied!';
                    setTimeout(function() {
                        btn.textContent = 'Copy';
                    }, 2000);
                });
            });
        });
    </script>
@stop
