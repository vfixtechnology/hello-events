@extends('adminlte::page')

@section('title', 'Edit Tax Rates')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Tax Rates</h1>
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
                            <h3 class="card-title">Edit Tax Rate</h3>
                        </div>
                        <div class="card-body">
                            <form class="alert-form" action="{{ route('tax.update',$tax->id) }}" method="post" >
                                @csrf
                                @method('PATCH')

                                <x-form.input label="Title" id="title" name="title" value="{{ $tax->title }}" placeholder="Add title" />

                                <x-form.input label="Rate" type="number" value="{{ $tax->rate }}" labelHelp="" id="" name="rate"
                                    placeholder="Tax rate" />

                                {{-- <x-form.checkbox name="is_active" label="Active"
                                    helpText="Only active tax rates will be shown event page" checked="{{ $tax->is_active }}" /> --}}


                                <x-button variant="danger" label="Submit" />
                                 <a href="{{ route('tax.index') }}" class="btn btn-secondary">Cancel</a>

                            </form>
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
                   text: "Items details will be updated. You can edit later!",
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
