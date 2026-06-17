@extends('adminlte::page')

@section('title', 'Edit Ticket')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Ticket</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('tickets.index') }}">Tickets</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tickets.show', $ticket->id) }}">#{{ $ticket->uuid }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-dismissable alert-danger mt-3">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>Whoops!</strong> There were some problems with your input.<br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Attendee Information</h3>
                </div>
                <form class="alert-form" action="{{ route('tickets.update', $ticket->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input name="attendee_name" label="Full Name" :value="$ticket->attendee_name ?? ''" required="true"
                                    placeholder="Enter attendee name" />
                            </div>
                            <div class="col-md-6">
                                <x-form.input name="attendee_email" label="Email Address" :value="$ticket->attendee_email ?? ''" required="true"
                                    type="email" placeholder="Enter email address" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input name="attendee_phone" label="Phone Number" :value="$ticket->attendee_phone ?? ''"
                                    placeholder="Enter phone number" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input name="organization" label="Organization" :value="$ticket->organization ?? ''"
                                    placeholder="Enter organization name" />
                            </div>
                            <div class="col-md-6">
                                <x-form.input name="designation" label="Designation" :value="$ticket->designation ?? ''"
                                    placeholder="Enter designation" />
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-danger">Update Ticket</button>
                        <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ticket Summary</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 120px;">Ticket ID</th>
                            <td><span class="badge bg-info">{{ $ticket->uuid }}</span></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @switch($ticket->status)
                                    @case('valid')
                                        <span class="badge bg-success">Valid</span>
                                        @break
                                    @case('used')
                                        <span class="badge bg-primary">Used</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                        @break
                                    @case('refunded')
                                        <span class="badge bg-warning">Refunded</span>
                                        @break
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <th>Event</th>
                            <td>{{ $ticket->ticketType->event->title ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Ticket Type</th>
                            <td>{{ $ticket->ticketType->title ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Order #</th>
                            <td>{{ $ticket->order->order_number ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    @if (session('errors'))
        <script>
            $(function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    type: 'error',
                    title: '<strong>Whoops!</strong> There were some problems with your input.',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif

    <script>
        $(document).ready(function() {
            $('.alert-form').on('submit', function(e) {
                e.preventDefault();
                var form = this;

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Ticket details will be updated.',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update it!',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.value) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@stop
