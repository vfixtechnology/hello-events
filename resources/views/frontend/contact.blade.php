@extends('frontend.layouts.app')
@section('content')
    <section class="mt-5 gemini-glow-bg " >
        <div class="container pb-5">
            <div class="section-header text-center" data-aos="fade-up">
                <h2>Contact Us</h2>
                <p>Get in touch with us for any inquiries or support</p>
                <div class="accent-line"></div>
            </div>

            <div class="row g-5">
                <div class="col-lg-5" data-aos="fade-up" data-aos-delay="100">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-4">
                                <i class="fas fa-info-circle text-primary me-2"></i>Get in Touch
                            </h4>

                            @if($setting?->address)
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-semibold mb-1">Address</h6>
                                    <p class="text-muted mb-0">{{ $setting->address }}</p>
                                </div>
                            </div>
                            @endif

                            @if($setting?->phone || $setting?->whatsapp)
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-phone text-primary"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-semibold mb-1">Phone</h6>
                                    @if($setting->phone)
                                    <p class="text-muted mb-1">{{ $setting->phone }}</p>
                                    @endif
                                    @if($setting->whatsapp)
                                    <p class="text-muted mb-0">
                                        <i class="fab fa-whatsapp text-success me-1"></i>{{ $setting->whatsapp }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @if($setting?->email || $setting?->email2)
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-envelope text-primary"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-semibold mb-1">Email</h6>
                                    @if($setting->email)
                                    <p class="text-muted mb-1">{{ $setting->email }}</p>
                                    @endif
                                    @if($setting->email2)
                                    <p class="text-muted mb-0">{{ $setting->email2 }}</p>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @if($setting?->facebook || $setting?->instagram || $setting?->twitter || $setting?->linkedin || $setting?->youtube)
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-share-nodes text-primary"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-semibold mb-2">Follow Us</h6>
                                    <div class="d-flex gap-2">
                                        @if($setting->facebook)
                                        <a href="{{ $setting->facebook }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 36px; height: 36px;">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                        @endif
                                        @if($setting->instagram)
                                        <a href="{{ $setting->instagram }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-circle" style="width: 36px; height: 36px;">
                                            <i class="fab fa-instagram"></i>
                                        </a>
                                        @endif
                                        @if($setting->twitter)
                                        <a href="{{ $setting->twitter }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-circle" style="width: 36px; height: 36px;">
                                            <i class="fab fa-x-twitter"></i>
                                        </a>
                                        @endif
                                        @if($setting->linkedin)
                                        <a href="{{ $setting->linkedin }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 36px; height: 36px;">
                                            <i class="fab fa-linkedin-in"></i>
                                        </a>
                                        @endif
                                        @if($setting->youtube)
                                        <a href="{{ $setting->youtube }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-circle" style="width: 36px; height: 36px;">
                                            <i class="fab fa-youtube"></i>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-7" data-aos="fade-up" data-aos-delay="200">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-4">
                                <i class="fas fa-paper-plane text-primary me-2"></i>Send a Message
                            </h4>
                            <form>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Your Name</label>
                                        <input type="text" class="form-control form-control-lg" placeholder="John Doe" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Your Email</label>
                                        <input type="email" class="form-control form-control-lg" placeholder="john@example.com" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-medium">Phone Number</label>
                                        <input type="tel" class="form-control form-control-lg" placeholder="+1 234 567 890">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-medium">Message</label>
                                        <textarea class="form-control form-control-lg" rows="2" placeholder="Write your message here..." required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-lg px-5">
                                            <i class="fas fa-paper-plane me-2"></i>Send Message
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('css')
@stop
