@extends('adminlte::page')

@section('title', 'Profile')

@section('content_header')
    <h1>Profile</h1>
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

    <section>
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Create New Coupon</h3>
            </div>
            <form action="{{ route('coupon.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <x-form.input name="code" label="Coupon Code" :value="$coupon->code ?? ''" required="true"
                                placeholder="e.g., EARLYBIRD10" />
                        </div>
                        <div class="col-md-6">
                            <x-form.select name="type" label="Coupon Type" :options="['percent' => 'Percentage (%)', 'fixed' => 'Fixed Amount']" :selected="$coupon->type ?? null"
                                required="true" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <x-form.input name="value" label="Value" :value="$coupon->value ?? ''" type="number" step="0.01"
                                required="true" placeholder="e.g., 10 or 500" />
                        </div>
                        <div class="col-md-6">
                            <x-form.input name="max_uses" label="Maximum Uses (Optional)" :value="$coupon->max_uses ?? ''" type="number"
                                placeholder="e.g., 100" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <x-form.input name="expires_at" label="Expires At (Optional)" :value="isset($coupon->expires_at) ? $coupon->expires_at->format('Y-m-d') : ''"
                                type="date" />
                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save Coupon</button>
                    <a href="{{ route('coupon.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </section>
@stop


@section('js')


    {{-- Succes and error notification alert --}}
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

    @if (session('password_error'))
        <script>
            $(function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    type: 'error', // SweetAlert2 v8 uses "type" instead of "icon"
                    title: 'Current password does not match!',
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
            $('.alert-form').on('submit', function(e) {
                e.preventDefault(); // prevent default form submit
                var form = this;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "User details will be updated. You can edit later!",
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

@stop
