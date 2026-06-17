@extends('adminlte::page')

@section('title', 'Manage Permissions - ' . ucfirst($role->name))

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Manage Permissions: <strong>{{ ucfirst($role->name) }}</strong></h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Back to Roles</a></li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">

            @if ($role->name === 'user')
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    The <strong>user</strong> role cannot have any permissions assigned. This role is reserved for frontend users only.
                </div>
            @endif

            <form id="perm-form" action="{{ route('roles.update', $role->id) }}" method="post">
                @csrf
                @method('put')

                <div class="row">
                    @foreach($permissions as $module => $modulePermissions)
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-primary py-2">
                                    <h3 class="card-title">
                                        <i class="fas fa-fw fa-shield-alt mr-1"></i>
                                        {{ ucfirst($module) }}
                                    </h3>
                                    <div class="card-tools">
                                        <label class="mb-0 text-white small">
                                            <input type="checkbox" class="module-checkall" data-module="{{ $module }}" {{ $role->name === 'user' ? 'disabled' : '' }}>
                                            Select All
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body py-2 px-3">
                                    @foreach($modulePermissions as $perm)
                                        <div class="form-check mb-1">
                                            <input class="form-check-input perm-checkbox"
                                                   type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $perm->name }}"
                                                   id="perm-{{ $perm->id }}"
                                                   {{ in_array($perm->name, $rolePermissions) ? 'checked' : '' }}
                                                   {{ $role->name === 'user' ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="perm-{{ $perm->id }}">
                                                {{ $perm->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="row mb-4">
                    <div class="col-12 text-center">
                        <button type="button" id="update-permissions" class="btn btn-success btn-lg px-5" {{ $role->name === 'user' ? 'disabled' : '' }}>
                            <i class="fas fa-save mr-1"></i> Update Permissions
                        </button>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-lg px-5 ml-2">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </section>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.module-checkall').on('change', function() {
                var module = $(this).data('module');
                var checked = $(this).prop('checked');
                $(this).closest('.card').find('.perm-checkbox').prop('checked', checked);
            });

            $('.perm-checkbox').on('change', function() {
                var card = $(this).closest('.card');
                var all = card.find('.perm-checkbox').length;
                var checked = card.find('.perm-checkbox:checked').length;
                card.find('.module-checkall').prop('checked', all === checked);
            });

            $('#update-permissions').on('click', function(e) {
                var checkedCount = $('.perm-checkbox:checked').length;

                Swal.fire({
                    title: 'Update Permissions?',
                    text: checkedCount + ' permission(s) will be assigned to "' + '{{ $role->name }}' + '" role.',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update!',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.value) {
                        $('#perm-form').submit();
                    }
                });
            });
        });
    </script>

    @if ($errors->any())
        <script>
            $(function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    type: 'error',
                    title: '{{ $errors->first() }}',
                    showConfirmButton: false,
                    timer: 5000
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
                    type: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        </script>
    @endif
@stop
