@extends('adminlte::page')

@section('title', 'Order Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-0">
                Order #{{ $order->order_number }}
                <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : ($order->status === 'failed' ? 'danger' : 'secondary')) }} ml-2">
                    {{ ucfirst($order->status) }}
                </span>
            </h1>
            <small class="text-muted">Placed {{ $order->created_at->format('d M Y, g:i A') }}</small>
        </div>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-shopping-cart mr-2"></i>Order Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted" style="width:120px">Order #</td>
                                    <td class="font-weight-bold">{{ $order->order_number }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Date</td>
                                    <td>{{ $order->created_at->format('d M Y, g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Payment</td>
                                    <td>
                                        <span class="badge badge-info">{{ str_replace('_', ' ', ucfirst($order->payment_method)) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Payment ID</td>
                                    <td>{{ $order->payment_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Currency</td>
                                    <td>{{ $order->currency }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted" style="width:120px">Subtotal</td>
                                    <td>{{ Number::currency($order->subtotal, config('app.currency')) }}</td>
                                </tr>
                                @if($order->discount_amount > 0)
                                <tr>
                                    <td class="text-muted">Discount</td>
                                    <td class="text-danger">-{{ Number::currency($order->discount_amount, config('app.currency')) }} {!! $order->coupon_code ? "<small class='text-muted'>($order->coupon_code)</small>" : '' !!}</td>
                                </tr>
                                @endif
                                @if($order->tax_amount > 0)
                                <tr>
                                    <td class="text-muted">Tax</td>
                                    <td>{{ Number::currency($order->tax_amount, config('app.currency')) }}</td>
                                </tr>
                                @endif
                                <tr class="border-top">
                                    <td class="text-muted font-weight-bold">Grand Total</td>
                                    <td class="font-weight-bold text-lg">{{ Number::currency($order->grand_total, config('app.currency')) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user mr-2"></i>Buyer Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted" style="width:100px">Name</td>
                                    <td class="font-weight-bold">{{ $order->buyer_name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Email</td>
                                    <td><a href="mailto:{{ $order->buyer_email }}">{{ $order->buyer_email }}</a></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Phone</td>
                                    <td>{{ $order->buyer_phone ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted" style="width:80px">Address</td>
                                    <td>
                                        {{ $order->address ?? 'N/A' }}<br>
                                        {{ $order->city ?? '' }}{{ $order->city && $order->state ? ', ' : '' }}{{ ucwords($order->state) ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Country</td>
                                    <td>{{ ucwords($order->country) ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Pincode</td>
                                    <td>{{ $order->pincode ?? 'N/A' }}</td>
                                </tr>
                                @if($order->user)
                                <tr>
                                    <td class="text-muted">Account</td>
                                    <td>{{ $order->user->name }} ({{ $order->user->email }})</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>Financial Summary</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <span class="text-muted text-sm">Grand Total</span>
                        <div class="font-weight-bold" style="font-size: 28px;">{{ Number::currency($order->grand_total, config('app.currency')) }}</div>
                    </div>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Subtotal</td>
                            <td class="text-right">{{ Number::currency($order->subtotal, config('app.currency')) }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                        <tr>
                            <td class="text-muted">Discount <small>({{ $order->coupon_code ?? '-' }})</small></td>
                            <td class="text-right text-danger">-{{ Number::currency($order->discount_amount, config('app.currency')) }}</td>
                        </tr>
                        @endif
                        @if($order->tax_amount > 0)
                        <tr>
                            <td class="text-muted">Tax</td>
                            <td class="text-right">{{ Number::currency($order->tax_amount, config('app.currency')) }}</td>
                        </tr>
                        @endif
                    </table>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Tickets</span>
                        <span class="badge badge-primary">{{ $order->tickets->count() }} ticket{{ $order->tickets->count() !== 1 ? 's' : '' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="text-muted">Payment</span>
                        <span class="badge badge-info">{{ str_replace('_', ' ', ucfirst($order->payment_method)) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-ticket-alt mr-2"></i>Tickets ({{ $order->tickets->count() }})</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Attendee</th>
                        <th>Ticket Type</th>
                        <th>Event</th>
                        <th>Status</th>
                        <th class="text-center">Check-ins</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->tickets as $ticket)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ $ticket->attendee_name }}<br>
                                <small class="text-muted">{{ $ticket->attendee_email }}</small>
                            </td>
                            <td><span class="badge badge-secondary">{{ $ticket->ticketType->title ?? 'N/A' }}</span></td>
                            <td>{{ Str::limit($ticket->ticketType->event->title ?? 'N/A', 35) }}</td>
                            <td>
                                <span class="badge badge-{{ $ticket->status === 'valid' ? 'success' : ($ticket->status === 'used' ? 'danger' : ($ticket->status === 'cancelled' ? 'secondary' : 'warning')) }}">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </td>
                            <td class="text-center">{{ $ticket->check_in_count }} / {{ $ticket->max_entries }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
<style>
.text-lg { font-size: 1.2rem; }
.card-outline { border-top: 3px solid; }
</style>
@stop

@section('js')
    @if (session('success'))
        <script>
            $(function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    type: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif
@stop
