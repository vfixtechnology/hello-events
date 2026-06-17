@extends('adminlte::page')

@section('title', 'Trash Events')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Deleted listings/Events</h1>
            <small>All deleted listings/events - you can restore from delete permanently</small>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                @can('event create')
                <li class="breadcrumb-item"><a href="{{ route('event.create') }}">+ Add New</a> |</li>
                @endcan
                @can('event list')
                <li class=""> &nbsp; <a href="{{ route('event.index') }}">View All</a></li>
                @endcan
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

    {{-- @if (session()->has('success'))
        <div class="alert alert-dismissable alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>
                {!! session()->get('success') !!}
            </strong>
        </div>
    @endif --}}
    <div class="">

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="card py-2 px-2">
                            <div class="card-body p-0">
                                <div class="mb-2">
                                    @can('event trash-bulk-delete')
                                    <form id="bulk-delete-form" action="{{ route('event.trash.bulk-delete') }}"
                                        method="POST">
                                        @csrf
                                        <button type="button" class="btn btn-danger btn-sm d-none" id="delete-selected">
                                            <i class="fas fa-trash-alt"></i> Delete Selected
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                                <table id="table-1" class="table table-striped projects">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="select-all"></th>
                                            <th style="width: 1%">
                                                #
                                            </th>
                                            <th style="width: 25%">
                                                Title
                                            </th>
                                            <th style="width: 10%">
                                                Image
                                            </th>
                                            <th style="width: 10%">
                                                Date
                                            </th>
                                            <th>
                                                Venue
                                            </th>
                                            <th style="width: 20%" class="text-center">
                                                Status
                                            </th>
                                            <th style="width: 20%">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($events as $event)
                                            <tr>
                                                <td><input type="checkbox" class="row-checkbox"
                                                        data-id="{{ $event->id }}"></td>
                                                <td>
                                                    {{ $loop->iteration }}
                                                </td>
                                                <td>
                                                    <a>
                                                        {{ $event->title }}
                                                    </a>
                                                    <br>
                                                    <small>
                                                        Deleted: {{ $event->deleted_at->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td>
                                                    @if ($event->hasMedia('image'))
                                                        <img style="width:75px;" src="{{ $event->getFirstMediaUrl('image','thumb')  }}" alt="">
                                                    @else
                                                    <img style="width:75px;" src=" {{ asset('no-image.webp') }}" alt="">
                                                    @endif


                                                </td>
                                                <td>
                                                    {{ $event->start_datetime->format('d M y, g:i A') }}
                                                </td>
                                                <td>
                                                    {{ $event->venue }}
                                                </td>
                                                <td class="project-state">
                                                    @if ($event->published)
                                                        <span class="badge badge-success">Published</span>
                                                    @else
                                                        <span class="badge badge-danger">Draft</span>
                                                    @endif
                                                </td>
                                                <td class="project-actions text-right d-flex">
                                                    @can('event restore')
                                                    <div class="mr-2">
                                                        <form class="alert-restore"
                                                            action="{{ route('event.restore', $event->id) }}" method="get">
                                                            <button type="submit"
                                                                class="btn btn-primary btn-sm alert-restore">

                                                                <i class="fas fa-folder">
                                                                </i> Restore
                                                            </button>
                                                        </form>
                                                    </div>
                                                    @endcan
                                                    @can('event force-delete')
                                                    <div>
                                                        <form class="alert-form"
                                                            action="{{ route('event.force.delete', $event->id) }}"
                                                            method="post">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-trash">
                                                                </i>
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                    <!-- /.col -->

                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
    </div>

@stop

@section('css')

@stop

@section('js')
    {{-- hide notifcation --}}
    <script>
        $(document).ready(function() {
            $(".alert").delay(6000).slideUp(300);
        });
    </script>


    <script>
        $(document).ready(function() {
            $('#table-1').DataTable();
        });
    </script>

    {{-- Success and error notification --}}
    <script>
        $(document).ready(function() {
            $('.alert-form').on('submit', function(e) {
                e.preventDefault(); // prevent default form submit
                var form = this;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will be deleted permanently. Cannot be undone.",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update it!',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.value) {
                        form.submit(); // use native form submission
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.alert-restore').on('submit', function(e) {
                e.preventDefault(); // prevent default form submit
                var form = this;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This item will be restored.",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, restore it!',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.value) {
                        form.submit(); // use native form submission
                    }
                });
            });
        });
    </script>

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

    <script>
        $(document).ready(function() {
            $('#my-table').DataTable();

            function toggleDeleteButton() {
                const selected = $('.row-checkbox:checked').length;
                $('#delete-selected').toggleClass('d-none', selected === 0);
            }

            // Toggle visibility of delete button
            $(document).on('change', '.row-checkbox, #select-all', function() {
                toggleDeleteButton();
            });

            // Handle Select All checkbox
            $('#select-all').on('change', function() {
                $('.row-checkbox').prop('checked', this.checked).trigger('change');
            });

            // Handle Bulk Delete Button Click
            $('#delete-selected').on('click', function(e) {
                e.preventDefault();

                const selectedIds = $('.row-checkbox:checked').map(function() {
                    return $(this).data('id');
                }).get();

                if (selectedIds.length === 0) return;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "All items will be permanently deleted & cannot be recovered.",
                    type: 'warning', // SweetAlert2 v8 uses "type"
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete!',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.value) {
                        // Remove any previous hidden inputs
                        $('#bulk-delete-form').find('input[name="ids[]"]').remove();

                        // Add selected IDs to form
                        selectedIds.forEach(id => {
                            $('#bulk-delete-form').append(
                                `<input type="hidden" name="ids[]" value="${id}">`);
                        });

                        // Submit the form
                        $('#bulk-delete-form')[0].submit();
                    }
                });
            });
        });
    </script>
@stop
