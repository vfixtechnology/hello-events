@extends('adminlte::page')

@section('title', 'Tickets')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1><i class="fas fa-ticket-alt mr-2 text-primary"></i>All Tickets</h1>
    </div>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <form method="GET" action="{{ route('tickets.index') }}" class="filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search attendee name, email, UUID..." value="{{ $search }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="event_id" class="form-control" data-placeholder="Events" onchange="this.form.submit()">
                            <option value="">All Events</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ $eventId == $event->id ? 'selected' : '' }}>{{ $event->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="ticket_type_id" class="form-control" data-placeholder="Ticket Types" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            @foreach($ticketTypes as $type)
                                <option value="{{ $type->id }}" {{ $ticketTypeId == $type->id ? 'selected' : '' }}>{{ $type->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="valid" {{ $status == 'valid' ? 'selected' : '' }}>Valid</option>
                            <option value="used" {{ $status == 'used' ? 'selected' : '' }}>Used</option>
                            <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="refunded" {{ $status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <select name="per_page" class="form-control" onchange="this.form.submit()">
                            <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                    <div class="col-md-auto d-flex">
                        @if($search || $status || $eventId || $ticketTypeId)
                            <a href="{{ route('tickets.index') }}" class="btn btn-secondary mr-2">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        @endif
                        @can('ticket export')
                        <div class="btn-group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('tickets.export', array_merge(request()->only(['search', 'status', 'event_id', 'ticket_type_id']), ['format' => 'xlsx'])) }}">
                                    <i class="fas fa-file-excel"></i> Excel (XLSX)
                                </a>
                                <a class="dropdown-item" href="{{ route('tickets.export', array_merge(request()->only(['search', 'status', 'event_id', 'ticket_type_id']), ['format' => 'csv'])) }}">
                                    <i class="fas fa-file-csv"></i> CSV
                                </a>
                            </div>
                        </div>
                        @endcan
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 1%">#</th>
                        <th>Event</th>
                        <th>Attendee</th>
                        <th>Ticket Type</th>
                        <th>Order #</th>
                        <th>Status</th>
                        <th class="text-center">Check-ins</th>
                        <th class="text-center" style="width: 18%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            <td>{{ $tickets->firstItem() + $loop->index }}</td>
                            <td><a href="{{ route('tickets.show', $ticket->id) }}">{{ $ticket->ticketType->event->title ?? 'N/A' }}</a></td>
                            <td>
                                <strong>{{ $ticket->attendee_name }}</strong><br>
                                <small class="text-muted">{{ $ticket->attendee_email }}</small>
                            </td>
                            <td><span class="badge badge-secondary">{{ $ticket->ticketType->title ?? 'N/A' }}</span></td>
                            <td><span class="font-weight-bold">{{ $ticket->order->order_number ?? 'N/A' }}</span></td>
                            <td>
                                <span class="badge badge-{{ $ticket->status === 'valid' ? 'success' : ($ticket->status === 'used' ? 'danger' : ($ticket->status === 'cancelled' ? 'secondary' : 'warning')) }}">
                                    {{ ucfirst($ticket->status) }}
                                </span>
                            </td>
                            <td class="text-center">{{ $ticket->check_in_count }} / {{ $ticket->max_entries }}</td>
                            <td class="text-center">
                                @can('ticket show')
                                <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                @can('ticket edit')
                                <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                @endcan
                                @can('ticket download-pdf')
                                <a href="{{ route('tickets.download-pdf', $ticket->id) }}" class="btn btn-sm btn-outline-secondary" title="Download PDF">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endcan
                                @can('ticket resend-email')
                                <form class="resend-form" action="{{ route('tickets.resend-email', $ticket->id) }}" method="post" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Resend Email">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                </form>
                                @endcan
                                @can('ticket delete')
                                <form class="alert-form" action="{{ route('tickets.destroy', $ticket->id) }}" method="post" style="display: inline;">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                No tickets found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
            <div class="card-footer">
                {{ $tickets->links() }}
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
<script>
    $(document).ready(function() {
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

        $('.alert-form').on('submit', function(e) {
            e.preventDefault();
            var form = this;

            Swal.fire({
                title: 'Delete Ticket?',
                text: 'Type DELETE to confirm permanent deletion.',
                icon: 'warning',
                input: 'text',
                inputPlaceholder: 'Type DELETE to confirm',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger mr-3',
                    cancelButton: 'btn btn-secondary'
                },
                inputValidator: function(value) {
                    if (value !== 'DELETE') {
                        return 'Please type DELETE to confirm.';
                    }
                }
            }).then(function(result) {
                if (result.isConfirmed) {
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
