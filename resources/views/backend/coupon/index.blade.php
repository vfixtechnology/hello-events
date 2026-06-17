@extends('adminlte::page')

@section('title', 'Coupons')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>All Coupons</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                @can('coupon create')
                <li class="breadcrumb-item"><a href="{{ route('coupon.create') }}">+ Add New</a></li>
                @endcan
            </ol>
        </div>
    </div>
@stop

@section('content')

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card py-2 px-2">

                            <div class="card-body p-0">
                                <table id="myTable" class="table table-striped projects ">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%">
                                                #
                                            </th>
                                            <th style="width: 19%">
                                                Coupon
                                            </th>
                                            <th style="width: 15%">
                                                Type
                                            </th>
                                            <th style="width: 20%">
                                                Value
                                            </th>
                                            <th style="width: 15%">
                                                Usage
                                            </th>
                                            {{-- <th style="width: 10%">
                                                Comments
                                            </th> --}}
                                            <th style="width: 15%" class="text-center">
                                                Expires At
                                            </th>
                                            <th style="width: 18%">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($coupons as $coupon)
                                            <tr>
                                                <td>
                                                    {{ $loop->iteration }}
                                                </td>
                                                <td><strong>{{ $coupon->code }}</strong></td>
                                                <td>{{ ucfirst($coupon->type) }}</td>
                                                <td>
                                                    @if ($coupon->type == 'percent')
                                                        {{ $coupon->value }}%
                                                    @else
                                                        {{-- This one line handles everything! --}}
                                                        {{ Number::currency($coupon->value, config('settings.currency_code')) }}
                                                    @endif
                                                </td>
                                                <td>{{ $coupon->uses }} / {{ $coupon->max_uses ?? '∞' }}</td>
                                                <td>{{ $coupon->expires_at ? $coupon->expires_at->format('M d, Y') : 'Never' }}
                                                </td>

                                                <td class="project-actions text-right d-flex justify-content-between">
                                                    @can('coupon edit')
                                                    <div>
                                                        <a class="btn btn-info btn-sm"
                                                            href="{{ route('coupon.edit', $coupon->id) }}">
                                                            <i class="fas fa-pencil-alt">
                                                            </i>
                                                            Edit
                                                        </a>
                                                    </div>
                                                    @endcan
                                                    @can('coupon delete')
                                                    <div>
                                                        <form class="alert-form" action="{{ route('coupon.destroy', $coupon->id) }}"
                                                            method="post">
                                                            @csrf
                                                            @method('delete')
                                                            <button
                                                                type="submit" class="btn btn-danger btn-sm">
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
            $('#myTable').DataTable({
                responsive: true
            });

        });
    </script>

@endsection
