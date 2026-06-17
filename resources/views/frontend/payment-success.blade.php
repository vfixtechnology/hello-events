@extends('frontend.layouts.app')

@section('content')
    <div class="container py-5 my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="card shadow-sm p-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 80px;"></i>
                    </div>
                    <h2 class="fw-bold text-success mb-3">Payment Successful!</h2>
                    <p class="text-muted">Thank you for your booking. Your tickets have been confirmed.</p>
                    
                    <div class="bg-light p-4 rounded mb-4">
                        <p class="mb-1"><strong>Order Number:</strong> {{ $order->order_number }}</p>
                        <p class="mb-1"><strong>Amount Paid:</strong> {{ Number::currency($order->grand_total, config('app.currency')) }}</p>
                        <p class="mb-0"><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                    </div>

                    <p>A confirmation email has been sent to <strong>{{ $order->buyer_email }}</strong> with your tickets and QR codes.</p>

                    <div class="d-flex gap-3 justify-content-center">
                        <a href="{{ route('home') }}" class="btn btn-primary">Back to Home</a>
                        <a href="{{ route('event.detail', $order->tickets->first()->ticketType->event->slug ?? '') }}" class="btn btn-outline-secondary">View Event</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
@stop

@section('js')
@stop
