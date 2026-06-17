@extends('adminlte::page')

@section('title', 'Orders')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1><i class="fas fa-shopping-cart mr-2 text-primary"></i>All Orders</h1>
    </div>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <form method="GET" action="{{ route('orders.index') }}" class="filter-form">
                <div class="row">
                    <div class="col-md-5">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by order number, buyer name or email..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="per_page" class="form-control" onchange="this.form.submit()">
                            <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 per page</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                        </select>
                    </div>
                    <div class="col-md-auto">
                        @if(request('search') || request('status'))
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 1%">#</th>
                        <th>Order #</th>
                        <th>Buyer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        @can('order show')
                        <th class="text-center" style="width: 10%">Action</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $orders->firstItem() + $loop->index }}</td>
                            <td class="font-weight-bold">{{ $order->order_number }}</td>
                            <td>
                                {{ $order->buyer_name }}<br>
                                <small class="text-muted">{{ $order->buyer_email }}</small>
                            </td>
                            <td><span class="font-weight-bold">{{ Number::currency($order->grand_total, config('app.currency')) }}</span></td>
                            <td><span class="badge badge-info">{{ str_replace('_', ' ', ucfirst($order->payment_method)) }}</span></td>
                            <td>
                                <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : ($order->status === 'failed' ? 'danger' : 'secondary')) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td><span title="{{ $order->created_at->format('d M Y, g:i A') }}">{{ $order->created_at->diffForHumans() }}</span></td>
                            @can('order show')
                            <td class="text-center">
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-info" title="View Order">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                No orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="card-footer">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@stop

@section('css')
<style>
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

    @if (session('error'))
        <script>
            $(function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    type: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 5000
                });
            });
        </script>
    @endif
@stop
