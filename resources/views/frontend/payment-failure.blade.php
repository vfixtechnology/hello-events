@extends('frontend.layouts.app')

@section('content')
    <div class="container py-5 my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="card shadow-sm p-5">
                    <div class="mb-4">
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 80px;"></i>
                    </div>
                    <h2 class="fw-bold text-danger mb-3">Payment Failed</h2>
                    <p class="text-muted">Sorry, your payment could not be processed. Please try again.</p>
                    
                    @if(session('error'))
                    <div class="bg-light p-3 rounded mb-4">
                        <p class="text-danger mb-0">{{ session('error') }}</p>
                    </div>
                    @endif

                    <div class="d-flex gap-3 justify-content-center">
                        <a href="{{ route('checkout.show') }}" class="btn btn-primary">Try Again</a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Back to Home</a>
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
