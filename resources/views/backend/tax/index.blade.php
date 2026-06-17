@extends('adminlte::page')

@section('title', 'Tax Rates')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>All Tax Rates</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"> Home</a></li>
                 <li class="breadcrumb-item"><a href="{{ route('tax.index') }}"> Tax Rate</a></li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header bg-primary py-2">
                             <h3 class="card-title">Add Tax Rate</h3>
                        </div>
                        @can('tax create')
                        <div class="card-body">
                            <form action="{{ route('tax.store') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                <x-form.input label="Title" id="title" name="title" placeholder="Add title" />

                                <x-form.input label="Rate" type="number" labelHelp="" id="" name="rate"
                                    placeholder="Tax rate" />

                               {{-- <x-form.checkbox name="is_active" label="Active"
                                helpText="Only active tax rates will be shown event page" checked="true" /> --}}


                               <x-button label="Submit" />

                            </form>
                        </div>
                        @endcan
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card py-2 px-2">

                        <div class="card-body p-0">
                            <table id="myTable" class="table table-striped projects ">
                                <thead>
                                    <tr>
                                        <th style="width: 1%">
                                            #
                                        </th>
                                        <th style="width: 19%">
                                            Title
                                        </th>
                                        <th style="width: 15%">
                                            Rate
                                        </th>
                                        <th style="width: 19%">
                                            Status
                                        </th>

                                        <th style="width: 19%">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($taxes as $taxRate)
                                        <tr>
                                            <td>
                                                {{ $loop->iteration }}
                                            </td>

                                            <td>{{ $taxRate->title }}</td>
                                            <td>
                                                {{ $taxRate->rate }}
                                            </td>
                                            <td>
                                                @if($taxRate->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>

                                            <td class="project-actions text-right d-flex justify-content-between">
                                                @can('tax edit')
                                                <div>
                                                    <a class="btn btn-info btn-sm"
                                                        href="{{ route('tax.edit', $taxRate->id) }}">
                                                        <i class="fas fa-pencil-alt">
                                                        </i>
                                                        Edit
                                                    </a>
                                                </div>
                                                @endcan
                                                @can('tax delete')
                                                <div>
                                                    <form class="alert-form"
                                                        action="{{ route('tax.destroy', $taxRate->id) }}"
                                                        method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash">
                                                            </i>
                                                            Trash
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

                    </div>
                </div>


            </div>

        </div>
    </section>

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
                    text: "⚠️ Are you sure? This will permanently delete the item!",
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

    @if (session('error'))
        <script>
            $(function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    type: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 4000
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
