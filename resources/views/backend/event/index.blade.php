@extends('adminlte::page')

@section('title', 'Catepories')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>All Events</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                @can('event create')
                <li class="breadcrumb-item"><a href="{{ route('event.create') }}">+ Add New  </a></li>
                @endcan
                @can('event list')
                <li class="breadcrumb-item"><a href="{{ route('event.trash') }}"> View Trash </a></li>
                @endcan
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Events</h3>
            @can('event create')
            <div class="card-tools">
                <a href="{{ route('event.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Event
                </a>
            </div>
            @endcan
        </div>
        <div class="card-body">
            <table id="myTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>

                        <th>Image</th>
                        <th>Venue</th>
                        <th>Starts At</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($events as $event)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $event->title }}
                                <br><small>Created: {{ $event->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <img class="img-fluid" style="width: 100px;" src="{{ $event->getFirstMediaUrl('image','thumb') }}" alt="">
                            </td>
                            <td>{{ $event->venue ?? 'N/A' }}</td>
                            <td>{{ $event->start_datetime->format('M d, Y H:i A') }}</td>
                            <td>
                                @if ($event->published)
                                    <span class="badge badge-success">Published</span>
                                @else
                                    <span class="badge badge-secondary">Draft</span>
                                @endif
                            </td>
                            <td>
                                @can('event edit')
                                <a href="{{ route('event.edit', $event) }}" class="btn btn-sm btn-info">Edit</a>
                                @endcan
                                @can('event delete')
                                <form  action="{{ route('event.destroy', $event) }}" method="POST" class=" alert-form d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Trash</button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@stop

@section('css')

@stop

@section('js')
    {{-- create live slug --}}
    <script>
        $('#title').on("change keyup paste click", function() {
            var Text = $(this).val().trim();
            Text = Text.toLowerCase();

            // Step 1: Remove the specific characters (), [], {} completely.
            Text = Text.replace(/[\[\]\(\)\{\}]/g, '');

            // Step 2: Replace spaces and any other non-alphanumeric characters with a single hyphen.
            Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');

            // Step 3 (Optional but recommended): Remove any leading or trailing hyphens.
            Text = Text.replace(/^-+|-+$/g, '');

            $('#slug').val(Text);
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                responsive: true
            });

        });
    </script>

    <script>
        $(document).ready(function() {
            $('.alert-form').on('submit', function(e) {
                e.preventDefault(); // prevent default form submit
                var form = this;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Not permanent — this post goes to Trash!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.value) {
                        form.submit(); // use native form submission
                    }
                });
            });
        });
    </script>

    @if (session('warning'))
        <script>
            $(function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    type: 'warning',
                    title: '{{ session('warning') }}',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                });
            });
        </script>
    @endif

    @if (session('errors'))
        <script>
            $(function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    type: 'error', // SweetAlert2 v8 uses "type" instead of "icon"
                    title: '<strong>Whoops!</strong> There were some problems with your input.',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            $(function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    type: 'success', // for SweetAlert2 v8
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif



@endsection
