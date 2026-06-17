@extends('frontend.layouts.app')

@section('content')
    <div style="margin-top:50px;" class="gemini-glow-bg">
    <div class="container">
        <div class="row g-4">


            <div class="col-lg-8 order-2 order-sm-1">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="fw-bold mb-0">Checkout</h2>
                    <a href="{{ route('booking', $event->slug) }}" class="btn btn-outline-danger rounded-pill">
                        <i class="bi bi-pencil-square"></i> Edit Tickets/Attendees
                    </a>
                </div>
                <form action="{{ route('payment.process') }}" method="POST" id="checkout-form">
                    @csrf
                    <div class="card shadow-sm p-4 mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Billing Information
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="buyer_name" class="form-label mb-1">Full Name</label>
                                <input type="text" class="form-control" id="buyer_name" name="buyer_name" placeholder="Enter your full name"
                                    value="{{ old('buyer_name', $billingData['buyer_name'] ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="buyer_email" class="form-label mb-1">Email Address</label>
                                <input type="email" class="form-control" id="buyer_email" name="buyer_email" placeholder="Enter your email address"
                                    value="{{ old('buyer_email', $billingData['buyer_email'] ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="buyer_phone" class="form-label mb-1">Phone Number</label>
                                <input type="tel" class="form-control" id="buyer_phone" name="buyer_phone" placeholder="Enter your phone number"
                                    value="{{ old('buyer_phone', $billingData['buyer_phone'] ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="mb-1" for="">Country</label>
                                    <select name="country" id="country" class="selectpicker form-control"
                                        data-live-search="true">
                                        <option value="">Select Country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}" {{ old('country', $billingData['country'] ?? '') == $country->id ? 'selected' : '' }}>{{ ucwords($country->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="col-md-6">
                                <label class="mb-1" for="">State</label>
                                <select name="state" id="state" class="selectpicker form-control"
                                    data-live-search="true">
                                    <option value="">Select State</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="mb-1" for="">City</label>
                                <select name="city" id="city" class="selectpicker form-control"
                                    data-live-search="true">
                                    <option value="">Select City</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="mb-1" for="pincode" class="">Zip / Pin Code</label>
                                <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Enter zip/pin code"
                                    value="{{ old('pincode', $billingData['pincode'] ?? '') }}" required>
                            </div>
                            {{-- <div class="col-md-12">
                                <label for="address" class="form-label mb-1">Address</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="Enter your address"
                                    value="{{ old('address', $billingData['address'] ?? '') }}">
                            </div> --}}
                        </div>
                    </div>

                    <div class="card shadow-sm p-4 mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-credit-card-fill me-2 text-primary"></i> Payment Method
                        </h5>
                        <div class="row g-3">
                            @php
                                $activeMethods = \App\Services\Payment\PaymentFactory::getAvailableMethods();
                            @endphp

                            @foreach($activeMethods as $key => $method)
                            <div class="col-md-6">
                                <div class="form-check border rounded p-3">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        id="payment_{{ $key }}" value="{{ $key }}" {{ $loop->first ? 'checked' : '' }}>
                                    <label class="form-check-label w-100" for="payment_{{ $key }}">
                                        <div class="d-flex align-items-center">
                                            @if(isset($method['image']))
                                            <img src="{{ asset($method['image']) }}" alt="{{ $method['title'] }}"
                                                class="me-2" style="width: 40px; height: 40px; object-fit: contain;">
                                            @endif
                                            <div>
                                                <strong>{{ $method['title'] }}</strong>
                                                <p class="text-muted small mb-0">{{ $method['description'] }}</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @error('payment_method')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-lg btn-success w-100 rounded-pill" id="pay-btn">
                        <i class="bi bi-credit-card me-2"></i>Pay {{ Number::currency($summary['total'], config('app.currency')) }}
                    </button>
                </form>
            </div>

            <div class="col-lg-4 order-1 order-sm-2 mb-3">
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
                        @foreach ($summary['tickets'] as $ticket)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ $ticket['title'] }} (x{{ $ticket['quantity'] }})</span>
                                <span>{{ Number::currency($ticket['price'], config('app.currency')) }}</span>
                            </li>
                        @endforeach
                        @if ($summary['add_ons_total'] > 0)
                            <li class="list-group-item d-flex justify-content-between text-success fw-bold">
                                <span>Total Add-ons</span>
                                <span>+{{ Number::currency($summary['add_ons_total'], config('app.currency')) }}</span>
                            </li>
                        @endif
                    </ul>

                    <ul class="list-group border-0 list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Subtotal</span>
                            <span>{{ Number::currency($summary['subtotal'], config('app.currency')) }}</span>
                        </li>
                        @if(isset($summary['coupon_discount']) && $summary['coupon_discount'] > 0)
                        <li class="list-group-item d-flex justify-content-between text-success">
                            <span>Discount</span>
                            <span>-{{ Number::currency($summary['coupon_discount'], config('app.currency')) }}</span>
                        </li>
                        @endif
                        @if($summary['taxes'] > 0)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $event->taxRate->title ?? 'Tax' }} ({{ $event->taxRate->rate }}%)</span>
                            <span>{{ Number::currency($summary['taxes'], config('app.currency')) }}</span>
                        </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between fw-bold fs-5">
                            <span>Total Payable</span>
                            <span class="text-primary">{{ Number::currency($summary['total'], config('app.currency')) }}</span>
                        </li>
                    </ul>

                    <div class="coupon-section">
                        @if(session()->has('coupon'))
                        <div class="applied-coupon p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="text-success">{{ session('coupon.code') }}</strong>
                                <span class="text-muted small">
                                    ({{ session('coupon.type') === 'percent' ? session('coupon.value').'%' : Number::currency(session('coupon.value'), config('app.currency')) }} off)
                                </span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-coupon">
                                <i class="bi bi-x-lg"></i> Remove
                            </button>
                        </div>
                        @else
                        <div class="coupon-form mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control" id="coupon_code" placeholder="Enter coupon code">
                               <button class="btn btn-outline-primary" type="button" id="apply-coupon" style="border-top-right-radius: 50rem; border-bottom-right-radius: 50rem; border-top-left-radius: 0; border-bottom-left-radius: 0;">Apply</button>
                            </div>
                            <div class="invalid-feedback d-none" id="coupon-error"></div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('css')
@stop

@section('js')
@stack('payment-scripts')

<script>
    function showToast(message, isError = false) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: isError ? 'error' : 'success',
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    $(document).ready(function() {
        @if(session('success'))
            showToast(@json(session('success')), false);
        @endif

        @if(session('error'))
            showToast(@json(session('error')), true);
        @endif

        function toTitleCase(str) {
            return str.replace(/\w\S*/g, function(txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
        }

        function saveBillingData() {
            $.post('{{ route("checkout.save-billing", [], false) }}', {
                _token: '{{ csrf_token() }}',
                buyer_name: $('input[name="buyer_name"]').val(),
                buyer_email: $('input[name="buyer_email"]').val(),
                buyer_phone: $('input[name="buyer_phone"]').val(),
                address: $('input[name="address"]').val(),
                city: $('select[name="city"]').val(),
                state: $('select[name="state"]').val(),
                country: $('select[name="country"]').val(),
                pincode: $('input[name="pincode"]').val(),
            });
        }

        var saveTimer;
        $('#checkout-form').on('change keyup', 'input, select', function() {
            clearTimeout(saveTimer);
            saveTimer = setTimeout(saveBillingData, 1000);
        });

        var preselectedState = '{{ $billingData["state"] ?? "" }}';
        var preselectedCity = '{{ $billingData["city"] ?? "" }}';

        $('#country').change(function() {
            var country_id = $(this).val();
            $.get('/get-states/' + country_id, function(data) {
                $('#state').empty().append('<option value="">Select State</option>');
                $.each(data, function(index, state) {
                    var sel = state.id == preselectedState ? 'selected' : '';
                    $('#state').append('<option value="' + state.id + '" ' + sel + '>' + toTitleCase(state.name) + '</option>');
                });
                if (preselectedState && data.length) {
                    $('#state').trigger('change');
                }
            });
            $('#city').empty().append('<option value="">Select City</option>');
        });

        $('#state').change(function() {
            var state_id = $(this).val();
            $.get('/get-cities/' + state_id, function(data) {
                $('#city').empty().append('<option value="">Select City</option>');
                $.each(data, function(index, city) {
                    var sel = city.id == preselectedCity ? 'selected' : '';
                    $('#city').append('<option value="' + city.id + '" ' + sel + '>' + toTitleCase(city.name) + '</option>');
                });
            });
        });

        if ($('#country').val()) {
            $('#country').trigger('change');
        }

        $('#checkout-form').on('submit', function(e) {
            var paymentMethod = $('input[name="payment_method"]:checked').val();
            var btn = $('#pay-btn');

            if (paymentMethod !== 'razorpay' && paymentMethod !== 'stripe') {
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
                return;
            }
        });

        function setCouponInvalid(message) {
            $('#coupon_code').addClass('is-invalid');
            $('#coupon-error').removeClass('d-none').text(message);
        }

        function clearCouponInvalid() {
            $('#coupon_code').removeClass('is-invalid');
            $('#coupon-error').addClass('d-none').text('');
        }

        $('#apply-coupon').click(function() {
            clearCouponInvalid();
            const couponCode = $('#coupon_code').val().trim().toUpperCase();
            if (!couponCode) {
                showToast('Please enter a coupon code.', true);
                return;
            }

            const btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            $.ajax({
                url: '{{ route("coupon.apply", [], false) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    coupon_code: couponCode
                },
                success: function(response) {
                    showToast(response.message, !response.success);
                    if (response.success) {
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        setCouponInvalid(response.message);
                        btn.prop('disabled', false).html('Apply');
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Failed to apply coupon.';
                    showToast(message, true);
                    setCouponInvalid(message);
                    btn.prop('disabled', false).html('Apply');
                }
            });
        });

        $('#coupon_code').on('input', clearCouponInvalid);

        $('#coupon_code').keypress(function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#apply-coupon').click();
            }
        });

        $('.remove-coupon').click(function() {
            const btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

            $.ajax({
                url: '{{ route("coupon.remove", [], false) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    showToast(response.message, false);
                    setTimeout(() => window.location.reload(), 1000);
                },
                error: function() {
                    showToast('Failed to remove coupon.', true);
                    btn.prop('disabled', false).html('<i class="bi bi-x-lg"></i> Remove');
                }
            });
        });
    });
</script>
@stop
