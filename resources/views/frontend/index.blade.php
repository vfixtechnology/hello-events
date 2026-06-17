@extends('frontend.layouts.app')
@section('content')
    <!-- Hero Slider -->
    <section class="hero-slider">
        <div class="swiper heroSwiper">
            <div class="swiper-wrapper">
                @forelse($setting->getMedia('banners') as $banner)
                    <div class="swiper-slide">
                        <div class="slide-bg" style="background-image: url('{{ $banner->getUrl() }}')"></div>
                    </div>
                @empty
                    <div class="swiper-slide">
                        <div class="slide-bg"
                            style="background-image: url('https://placehold.co/1920x600/0d6efd/ffffff?text=Discover+Events')">
                        </div>
                    </div>
                @endforelse
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"><i class="fas fa-chevron-right"></i></div>
            <div class="swiper-button-prev"><i class="fas fa-chevron-left"></i></div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-70 bg-white">
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <h2>Browse by Category</h2>
                <p>Explore events across different categories and find your perfect experience</p>
                <div class="accent-line"></div>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach ($categories as $category)
                    <div class="col-md-3" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        <a href="{{ route('category.events', $category->slug) }}" class="text-decoration-none">
                            <div class="card card-hover border-0 shadow-sm">
                                <img class="card-img-top rounded-top" src="{{ $category->image() }}" alt="{{ $category->title }}">
                                <div class="card-body text-center">
                                    <h2 class="h5 mb-0">{{ $category->title }}</h2>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Upcoming Events -->
    <section class="gemini-glow-bg" >
        <div class="container">
            <div class="section-header text-center" data-aos="fade-up">
                <h2>Upcoming Events</h2>
                <p>Don't miss out on these exciting upcoming events</p>
                <div class="accent-line"></div>
            </div>
            <div class="row g-4">
                @forelse ($upcomingEvents as $event)
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
                                            <i class="fas fa-clock me-1"></i>{{ $event->start_datetime->format('g:i A') }}
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
                                    <button class="btn btn-sm btn-outline-primary rounded-pill me-1" data-bs-toggle="modal"
                                        data-bs-target="#shareModal" data-url="{{ route('event.detail', $event->slug) }}"
                                        data-title="{{ $event->title }}">
                                        <i class="fas fa-share-alt"></i>
                                    </button>
                                    <a href="{{ route('event.detail', $event->slug) }}"
                                        class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">View Event</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5" data-aos="fade-up">
                        <p class="text-muted fs-5">No upcoming events at the moment. Check back later!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Past Events -->
    @if ($pastEvents->isNotEmpty())
        <section class="py-5 bg-white">
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
                                    <span class="badge bg-secondary position-absolute top-0 start-0 m-3">Expired</span>
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

@section('css')
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Swiper('.heroSwiper', {
                loop: true,
                speed: 800,
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });

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
