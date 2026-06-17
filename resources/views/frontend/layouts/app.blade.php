<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $setting?->bname ?? 'AllEvents' }} - Discover Events For All The Things You Love</title>
    {{-- CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link href="{{ asset('css/frontend.css') }}" rel="stylesheet">
    @yield('css')

    {!! seo($seo ?? null) !!}

    @if ($setting->gtag)
        {!! $setting->gtag !!}
    @endif
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg modern-nav fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                @if($setting?->getFirstMediaUrl('logo'))
                    <img src="{{ $setting->getFirstMediaUrl('logo') }}" alt="{{ $setting->bname ?? 'AllEvents' }}">
                @else
                    <i class="fas fa-calendar-alt me-2"></i>{{ $setting->bname ?? 'AllEvents' }}
                @endif
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                    <li class="nav-item nav-link-wrap">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item nav-link-wrap">
                        <a class="nav-link {{ request()->routeIs('events') ? 'active' : '' }}" href="{{ route('events') }}">Events</a>
                    </li>
                    <li class="nav-item nav-link-wrap">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-primary nav-cta-btn">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary nav-cta-btn">Login / Sign Up</a>
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Footer -->
    <footer class="modern-footer pt-5 pb-4">
        <div class="container-fluid px-5">
            <div class="row g-5">
                <div class="col-lg-4">
                    <div class="footer-brand-text mb-3">
                        @if($setting?->getFirstMediaUrl('logo'))
                            <img src="{{ $setting->getFirstMediaUrl('logo') }}" alt="{{ $setting->bname ?? 'AllEvents' }}" height="32">
                        @else
                            <i class="fas fa-calendar-alt me-2"></i>{{ $setting->bname ?? 'AllEvents' }}
                        @endif
                    </div>
                    <p class="footer-desc mb-4">Discover and book tickets for the best events in your city. From concerts and festivals to workshops and conferences.</p>
                    <div class="d-flex gap-2">
                        <a href="{{ $setting?->facebook ?? '#' }}" class="footer-social"><i class="fab fa-facebook-f"></i></a>
                        <a href="{{ $setting?->twitter ?? '#' }}" class="footer-social"><i class="fab fa-x-twitter"></i></a>
                        <a href="{{ $setting?->instagram ?? '#' }}" class="footer-social"><i class="fab fa-instagram"></i></a>
                        <a href="{{ $setting?->linkedin ?? '#' }}" class="footer-social"><i class="fab fa-linkedin-in"></i></a>
                        <a href="{{ $setting?->youtube ?? '#' }}" class="footer-social"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6 class="footer-title">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="footer-link">Home</a></li>
                        <li><a href="{{ route('events') }}" class="footer-link">Events</a></li>
                        <li><a href="{{ route('contact') }}" class="footer-link">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6 class="footer-title">Events</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('events') }}" class="footer-link">Upcoming</a></li>
                        <li><a href="{{ route('events') }}" class="footer-link">Popular</a></li>
                        <li><a href="{{ route('events') }}" class="footer-link">Free Events</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h6 class="footer-title">Newsletter</h6>
                    <p class="footer-desc">Subscribe to get the latest events and offers.</p>
                    <div class="footer-newsletter">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Your email address">
                            <button class="btn btn-primary" type="button">Subscribe</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-12 text-center">
                        <p class="mb-0">@ 2017 - {{ date('Y') }} | Proudly Crafted with ❤️ by <a href="https://www.vfixtechnology.com" target="_blank" rel="noopener" class="footer-link">VFIX TECHNOLOGY</a></p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    {{-- JS --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 600,
                easing: 'ease-out-cubic',
                once: true,
                offset: 60,
            });

            // Navbar scroll effect
            const nav = document.querySelector('.modern-nav');
            if (nav) {
                const toggleScrolled = () => nav.classList.toggle('scrolled', window.scrollY > 20);
                window.addEventListener('scroll', toggleScrolled, { passive: true });
                toggleScrolled();
            }
        });
    </script>
    @stack('scripts')
    @yield('js')
</body>
</html>
