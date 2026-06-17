@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>
            <i class="fas fa-tachometer-alt mr-2 text-primary"></i> Dashboard
        </h1>
        @if($showAdminStats)
        <div class="btn-group btn-group-sm" role="group" id="periodFilter">
            <button type="button" class="btn btn-outline-secondary period-btn" data-period="all">
                <i class="fas fa-globe mr-1"></i> All
            </button>
            <button type="button" class="btn btn-outline-secondary period-btn" data-period="today">
                <i class="fas fa-calendar-day mr-1"></i> Today
            </button>
            <button type="button" class="btn btn-outline-secondary period-btn" data-period="week">
                <i class="fas fa-calendar-week mr-1"></i> This Week
            </button>
            <button type="button" class="btn btn-outline-secondary period-btn" data-period="month">
                <i class="fas fa-calendar-alt mr-1"></i> This Month
            </button>
            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#customRangeModal">
                <i class="fas fa-clock mr-1"></i> Custom
            </button>
        </div>
        @endif
    </div>
@stop

@section('content')
<style>
#statsRow .small-box { position: relative; overflow: hidden; }
#statsRow .small-box::before {
    position: absolute;
    right: -15px;
    bottom: -15px;
    font-size: 120px;
    opacity: 0.12;
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    pointer-events: none;
    z-index: 1;
    line-height: 1;
}
#stat-events.small-box::before { content: '\f073'; color: #fff; }
#stat-categories.small-box::before { content: '\f02c'; color: #fff; }
#stat-orders.small-box::before { content: '\f07a'; color: #fff; }
#stat-revenue.small-box::before { content: '\f155'; color: #fff; }
#stat-tickets.small-box::before { content: '\f3ff'; color: #fff; }
#stat-upcoming.small-box::before { content: '\f017'; color: #fff; }
</style>
@if($showAdminStats)
{{-- admin stats --}}
<div class="row" id="statsRow">
    <div class="col-lg-4 col-md-6 col-sm-6">
        <x-adminlte-small-box title="{{ $totalEvents }}" text="Total Events"
            icon="fas fa-calendar-alt" theme="primary"
            :url="route('event.index')" url-text="Manage Events"
            id="stat-events"/>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6">
        <x-adminlte-small-box title="{{ $totalCategories }}" text="Categories"
            icon="fas fa-tags" theme="success"
            :url="route('category.index')" url-text="View Categories"
            id="stat-categories"/>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6">
        <x-adminlte-small-box title="{{ $totalOrders }}" text="Total Orders"
            icon="fas fa-shopping-cart" theme="info"
            :url="route('tickets.index')" url-text="View Orders"
            id="stat-orders"/>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6">
        <x-adminlte-small-box title="{{ Number::currency($totalRevenue, config('app.currency')) }}" text="Total Revenue"
            icon="fas fa-dollar-sign" theme="warning"
            :url="route('tickets.index')" url-text="View Transactions"
            id="stat-revenue"/>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6">
        <x-adminlte-small-box title="{{ $totalTicketsSold }}" text="Tickets Sold"
            icon="fas fa-ticket-alt" theme="danger"
            :url="route('tickets.index')" url-text="View Tickets"
            id="stat-tickets"/>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6">
        <x-adminlte-small-box title="{{ $upcomingEventsCount }}" text="Upcoming Events"
            icon="fas fa-clock" theme="secondary"
            :url="route('event.index')" url-text="View Events"
            id="stat-upcoming"/>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <x-adminlte-card title="Monthly Revenue" icon="fas fa-chart-area text-primary"
            theme="primary" theme-mode="outline" maximizable>
            <div style="height:260px">
                <canvas id="revenueChart"></canvas>
            </div>
        </x-adminlte-card>
    </div>
    <div class="col-lg-4">
        <x-adminlte-card title="Order Status" icon="fas fa-chart-pie text-success"
            theme="success" theme-mode="outline" maximizable>
            <div style="height:200px">
                <canvas id="orderStatusChart"></canvas>
            </div>
            <div class="row mt-3 text-center" id="orderStatusCounts">
                @foreach($orderStatusCounts as $status => $count)
                    <div class="col-4">
                        <span class="badge badge-pill badge-{{ $status === 'completed' ? 'success' : ($status === 'pending' ? 'warning' : ($status === 'failed' ? 'danger' : 'secondary')) }} d-inline-block mb-1 text-capitalize">{{ $status }}</span>
                        <div class="font-weight-bold h5">{{ $count }}</div>
                    </div>
                @endforeach
            </div>
        </x-adminlte-card>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <x-adminlte-card title="Latest Bookings" icon="fas fa-receipt text-primary"
            theme="primary" theme-mode="outline" maximizable>
            <x-slot name="toolsSlot">
                <a href="{{ route('tickets.index') }}" class="btn btn-tool">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </x-slot>
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Attendee</th>
                            <th>Ticket Type</th>
                            <th>Order #</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="recentOrdersBody">
                        @forelse($recentOrders as $ticket)
                            <tr onclick="window.location='{{ route('tickets.show', $ticket->id) }}'" style="cursor:pointer">
                                <td>
                                    {{ $ticket->attendee_name }}<br>
                                    <small class="text-muted">{{ $ticket->attendee_email }}</small>
                                </td>
                                <td>
                                    <span class="font-weight-medium">{{ $ticket->ticketType->title ?? 'N/A' }}</span><br>
                                    <small class="text-muted">{{ Str::limit($ticket->ticketType->event->title ?? '', 30) }}</small>
                                </td>
                                <td class="font-weight-bold text-primary">{{ $ticket->order->order_number ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-pill badge-{{ $ticket->status === 'valid' ? 'success' : ($ticket->status === 'used' ? 'info' : ($ticket->status === 'cancelled' ? 'secondary' : 'danger')) }}">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td>
                                    <span title="{{ $ticket->created_at->format('M d, Y H:i') }}">
                                        {{ $ticket->created_at->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    No tickets yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
    </div>
</div>

{{-- Custom Date Range Modal --}}
@elseif(!empty($showStaffScanner))
<div class="row">
    <div class="col-lg-6">
        <x-adminlte-card title="Ticket Scanner" icon="fas fa-qrcode text-success"
            theme="success" theme-mode="outline">
            <div class="text-center py-4">
                <i class="fas fa-qrcode" style="font-size: 80px; color: #28a745; opacity: 0.6;"></i>
                <h4 class="mt-3">Scan Tickets</h4>
                <p class="text-muted">Validate tickets by scanning attendee QR codes.</p>
                <a href="{{ route('ticket-scanner.index') }}" class="btn btn-success btn-lg mt-2">
                    <i class="fas fa-camera mr-1"></i> Open Scanner
                </a>
            </div>
        </x-adminlte-card>
    </div>
</div>
@else
<div class="row">
    <div class="col-lg-6">
        <x-adminlte-card title="My Orders" icon="fas fa-shopping-cart text-info"
            theme="info" theme-mode="outline" maximizable>
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Order #</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myOrders as $order)
                            <tr>
                                <td class="font-weight-bold">{{ $order->order_number }}</td>
                                <td>{{ Number::currency($order->grand_total, config('app.currency')) }}</td>
                                <td>
                                    <span class="badge badge-pill badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : ($order->status === 'failed' ? 'danger' : 'secondary')) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td><span title="{{ $order->created_at->format('M d, Y H:i') }}">{{ $order->created_at->diffForHumans() }}</span></td>
                                <td><a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>No orders yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($myOrders->hasPages())
                <div class="mt-2">{{ $myOrders->links() }}</div>
            @endif
        </x-adminlte-card>
    </div>
    <div class="col-lg-6">
        <x-adminlte-card title="My Tickets" icon="fas fa-ticket-alt text-success"
            theme="success" theme-mode="outline" maximizable>
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Attendee</th>
                            <th>Event</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myTickets as $ticket)
                            <tr>
                                <td>{{ $ticket->attendee_name }}<br><small class="text-muted">{{ $ticket->attendee_email }}</small></td>
                                <td>{{ Str::limit($ticket->ticketType->event->title ?? '', 30) }}</td>
                                <td>
                                    <span class="badge badge-pill badge-{{ $ticket->status === 'valid' ? 'success' : ($ticket->status === 'used' ? 'danger' : ($ticket->status === 'cancelled' ? 'secondary' : 'warning')) }}">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td><span title="{{ $ticket->created_at->format('M d, Y H:i') }}">{{ $ticket->created_at->diffForHumans() }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>No tickets yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($myTickets->hasPages())
                <div class="mt-2">{{ $myTickets->links() }}</div>
            @endif
        </x-adminlte-card>
    </div>
</div>
@endif
@if($showAdminStats)
<x-adminlte-modal id="customRangeModal" title="Custom Date Range" icon="fas fa-clock" size="md" vcenter>
    <form id="customDateForm">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>From</label>
                    <input type="date" name="from" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>To</label>
                    <input type="date" name="to" class="form-control" required>
                </div>
            </div>
        </div>
    </form>
    <x-slot name="footerSlot">
        <x-adminlte-button theme="secondary" label="Cancel" data-dismiss="modal"/>
        <x-adminlte-button theme="primary" label="Apply" id="applyCustomRange"/>
    </x-slot>
</x-adminlte-modal>
@endif
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

@if($showAdminStats)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    'use strict';
    const CURRENCY = @json(config('app.currency'));

    let revenueChart, orderStatusChart;
    const DEFAULT_PERIOD = 'all';

    function initCharts(data) {
        if (revenueChart) revenueChart.destroy();
        if (orderStatusChart) orderStatusChart.destroy();

        var ctx = document.getElementById('revenueChart').getContext('2d');
        var gradient = ctx.createLinearGradient(0, 0, 0, 260);
        gradient.addColorStop(0, 'rgba(78, 115, 223, 0.25)');
        gradient.addColorStop(1, 'rgba(78, 115, 223, 0)');

        revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                datasets: [{
                    label: 'Revenue',
                    data: data.months,
                    backgroundColor: gradient,
                    borderColor: '#4e73df',
                    borderWidth: 2,
                    pointBackgroundColor: '#4e73df',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return '{{ config('app.currency', '$') }}' + value; }
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        var labels = Object.keys(data.orderStatusCounts);
        var values = Object.values(data.orderStatusCounts);
        var colors = {
            completed: '#1cc88a', pending: '#f6c23e', failed: '#e74a3b',
            cancelled: '#858796', refunded: '#36b9cc'
        };

        orderStatusChart = new Chart(document.getElementById('orderStatusChart'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: labels.map(function(l) { return colors[l] || '#858796'; }),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 12, usePointStyle: true, font: { size: 11 } }
                    }
                },
                cutout: '70%'
            }
        });
    }

    function updateSmallBox(id, title, text) {
        var el = $('#' + id);
        if (el.length) {
            el.find('.inner h3').html(title);
            el.find('.inner h5').html(text);
        }
    }

    function refreshDashboard(period, from, to) {
        var params = { period: period };
        if (period === 'custom' && from && to) {
            params.from = from;
            params.to = to;
        }

        $.ajax({
            url: '{{ route("dashboard") }}',
            data: params,
            dataType: 'json',
            beforeSend: function() {
                $('#statsRow').find('.small-box .overlay').removeClass('d-none');
            },
            success: function(data) {
                updateSmallBox('stat-events', data.totalEvents, 'Total Events');
                updateSmallBox('stat-categories', data.totalCategories, 'Categories');
                updateSmallBox('stat-orders', data.totalOrders, 'Total Orders');
                updateSmallBox('stat-revenue', new Intl.NumberFormat(undefined, { style: 'currency', currency: CURRENCY }).format(data.totalRevenue), 'Total Revenue');
                updateSmallBox('stat-tickets', data.totalTicketsSold, 'Tickets Sold');
                updateSmallBox('stat-upcoming', data.upcomingEventsCount, 'Upcoming Events');

                initCharts(data);

                if (data.html) {
                    $('#recentOrdersBody').html(data.html.recentOrders);
                    $('#orderStatusCounts').html(data.html.orderStatusCounts);
                }
            },
            complete: function() {
                $('#statsRow').find('.small-box .overlay').addClass('d-none');
            }
        });
    }

    // Period filter click handling
    $(document).on('click', '.period-btn', function() {
        var btn = $(this);
        var period = btn.data('period');
        btn.closest('#periodFilter').find('.btn').removeClass('active btn-primary text-white').addClass('btn-outline-secondary');
        btn.removeClass('btn-outline-secondary').addClass('active btn-primary text-white');
        refreshDashboard(period);
    });

    // Custom range apply
    $('#applyCustomRange').on('click', function() {
        var from = $('input[name="from"]').val();
        var to = $('input[name="to"]').val();
        if (from && to) {
            $('#customRangeModal').modal('hide');
            refreshDashboard('custom', from, to);
        }
    });

    // Set "All" as active by default
    $('.period-btn[data-period="' + DEFAULT_PERIOD + '"]')
        .addClass('active btn-primary text-white')
        .removeClass('btn-outline-secondary');

    // Init charts with server-rendered data
    initCharts({
        months: @json($months),
        orderStatusCounts: @json($orderStatusCounts)
    });
})();
</script>
@endif
@stop
