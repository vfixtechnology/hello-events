@extends('adminlte::page')

@section('title', 'Ticket Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-0">
                Ticket Details
                <span class="badge badge-{{ $ticket->status === 'valid' ? 'success' : ($ticket->status === 'used' ? 'danger' : ($ticket->status === 'cancelled' ? 'secondary' : 'warning')) }} ml-2" style="font-size: 0.7rem; vertical-align: middle;">
                    {{ ucfirst($ticket->status) }}
                </span>
            </h1>
            <small class="text-muted">UUID: {{ $ticket->uuid }}</small>
        </div>
        <div>
            @can('ticket edit')
            <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-primary">
                <i class="fas fa-pencil-alt"></i> Edit
            </a>
            @endcan
            <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Tickets
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-qrcode mr-2"></i>QR Code</h3>
                </div>
                <div class="card-body text-center">
                    <div style="display: inline-block; padding: 15px; background: white; border: 1px solid #dee2e6; border-radius: 8px;">
                        <img src="data:image/svg+xml;base64,{{ \App\Helpers\QrCodeHelper::generateBase64($ticket->uuid, 200) }}" alt="QR Code">
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Ticket Info</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Ticket Type</td>
                            <td class="font-weight-bold">{{ $ticket->ticketType->title ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Max Entries</td>
                            <td>{{ $ticket->max_entries }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Check-ins</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="mr-2">{{ $ticket->check_in_count }} / {{ $ticket->max_entries }}</span>
                                    <div class="progress flex-grow-1" style="height:6px">
                                        <div class="progress-bar bg-{{ $ticket->check_in_count >= $ticket->max_entries ? 'danger' : ($ticket->check_in_count > 0 ? 'warning' : 'success') }}"
                                            style="width: {{ $ticket->max_entries > 0 ? ($ticket->check_in_count / $ticket->max_entries * 100) : 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">First Check-in</td>
                            <td>{{ $ticket->first_check_in_at ? $ticket->first_check_in_at->format('d M Y, g:i A') : 'Not checked in' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Last Check-in</td>
                            <td>{{ $ticket->last_check_in_at ? $ticket->last_check_in_at->format('d M Y, g:i A') : 'Not checked in' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-bolt mr-2"></i>Quick Actions</h3>
                </div>
                <div class="card-body">
                    @can('ticket check-in')
                    @if($ticket->status !== 'cancelled' && $ticket->status !== 'refunded')
                        @if($ticket->check_in_count < $ticket->max_entries)
                            <form class="checkin-form" method="POST" action="{{ route('tickets.check-in', $ticket->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block mb-2">
                                    <i class="fas fa-check"></i> Check In
                                </button>
                            </form>
                        @else
                            <button class="btn btn-secondary btn-block mb-2" disabled>
                                <i class="fas fa-times"></i> Max Entries Reached
                            </button>
                        @endif
                    @endif
                    @endcan
                    @can('ticket download-pdf')
                    <a href="{{ route('tickets.download-pdf', $ticket->id) }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                    @endcan
                    @can('ticket resend-email')
                    <form class="resend-form" method="POST" action="{{ route('tickets.resend-email', $ticket->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-block mb-2">
                            <i class="fas fa-envelope"></i> Resend Email
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user mr-2"></i>Attendee Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted" style="width:100px">Name</td>
                                    <td class="font-weight-bold">{{ $ticket->attendee_name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Email</td>
                                    <td><a href="mailto:{{ $ticket->attendee_email }}">{{ $ticket->attendee_email }}</a></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Phone</td>
                                    <td>{{ $ticket->attendee_phone ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted" style="width:100px">Org</td>
                                    <td>{{ $ticket->organization ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Designation</td>
                                    <td>{{ $ticket->designation ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Event Details</h3>
                </div>
                <div class="card-body">
                    @php $event = $ticket->ticketType->event ?? null; @endphp
                    @if($event)
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted" style="width:100px">Event</td>
                                        <td class="font-weight-bold">{{ $event->title }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Date</td>
                                        <td>{{ $event->start_datetime->format('d M Y, g:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Venue</td>
                                        <td>{{ $event->venue }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Location</td>
                                        <td>{{ $event->location }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    @if($event->host_name)
                                    <tr>
                                        <td class="text-muted" style="width:80px">Host</td>
                                        <td>{{ $event->host_name }}</td>
                                    </tr>
                                    @endif
                                    @if($event->host_email)
                                    <tr>
                                        <td class="text-muted">Email</td>
                                        <td><a href="mailto:{{ $event->host_email }}">{{ $event->host_email }}</a></td>
                                    </tr>
                                    @endif
                                    @if($event->host_phone)
                                    <tr>
                                        <td class="text-muted">Phone</td>
                                        <td>{{ $event->host_phone }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">Event information not available.</p>
                    @endif
                </div>
            </div>

            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-receipt mr-2"></i>Order Details</h3>
                </div>
                <div class="card-body">
                    @if($ticket->order)
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted" style="width:100px">Order #</td>
                                        <td class="font-weight-bold">{{ $ticket->order->order_number }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Payment</td>
                                        <td><span class="badge badge-info">{{ str_replace('_', ' ', ucfirst($ticket->order->payment_method)) }}</span></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted" style="width:100px">Status</td>
                                        <td>
                                            <span class="badge badge-{{ $ticket->order->status == 'completed' ? 'success' : 'warning' }}">
                                                {{ $ticket->order->status == 'completed' ? 'Paid' : ucfirst($ticket->order->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Total</td>
                                        <td class="font-weight-bold">{{ Number::currency($ticket->order->grand_total, config('app.currency')) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">Order information not available.</p>
                    @endif
                </div>
            </div>

            @can('ticket update-status')
            <div class="card card-outline card-dark">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-edit mr-2"></i>Update Ticket Status</h3>
                </div>
                <div class="card-body">
                    <form class="update-status-form" method="POST" action="{{ route('tickets.update-status', $ticket->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="valid" {{ $ticket->status == 'valid' ? 'selected' : '' }}>Valid</option>
                                        <option value="used" {{ $ticket->status == 'used' ? 'selected' : '' }}>Used</option>
                                        <option value="cancelled" {{ $ticket->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        <option value="refunded" {{ $ticket->status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Max Entries</label>
                                    <input type="number" name="max_entries" class="form-control" value="{{ $ticket->max_entries }}" min="1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="padding-top: 32px;">
                                    <button type="submit" class="btn btn-danger btn-block">
                                        <i class="fas fa-save"></i> Update Status
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="cancellation-reason-group" style="display: none;">
                            <label>Cancellation Reason</label>
                            <textarea name="cancellation_reason" class="form-control" rows="2" placeholder="Enter reason for cancellation...">{{ $ticket->cancellation_reason }}</textarea>
                        </div>
                    </form>
                </div>
            </div>
            @endcan
        </div>
    </div>
@stop

@section('css')
<style>
.card-outline { border-top: 3px solid; }
.progress { background-color: #e9ecef; border-radius: 3px; }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#cancellation-reason-group').toggle($('select[name="status"]').val() === 'cancelled');

        $('select[name="status"]').change(function() {
            $('#cancellation-reason-group').toggle($(this).val() === 'cancelled');
        });

        $('.checkin-form').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Confirm Check-In',
                text: 'Mark this ticket as checked in?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, check in!',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.value) {
                    form.submit();
                }
            });
        });

        $('.resend-form').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Resend Email?',
                text: 'Resend ticket notification to the attendee?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, resend!',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.value) {
                    form.submit();
                }
            });
        });

        $('.update-status-form').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Update Ticket Status?',
                text: 'Are you sure you want to change the status?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update!',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.value) {
                    form.submit();
                }
            });
        });
    });
</script>

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
                timer: 3000
            });
        });
    </script>
@endif
@stop
