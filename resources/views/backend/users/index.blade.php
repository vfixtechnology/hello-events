@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Users</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                @can('user create')
                <li class="breadcrumb-item"><a href="{{ route('users.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New</a></li>
                @endcan
            </ol>
        </div>
    </div>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('users.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search by name, email or phone..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="role" class="form-control">
                                    <option value="">All Roles</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="disabled" {{ request('status') === 'disabled' ? 'selected' : '' }}>Disabled</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="per_page" class="form-control">
                                    <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 per page</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info btn-block"><i class="fas fa-filter"></i> Filter</button>
                            </div>
                        </div>
                    </form>

                    <table class="table table-striped projects">
                        <thead>
                            <tr>
                                <th style="width: 1%">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr class="{{ !$user->status ? 'bg-light' : '' }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span class="badge badge-info">You</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge badge-primary">{{ ucfirst($role->name) }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($user->status)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Disabled</span>
                                        @endif
                                    </td>
                                    <td class="d-flex">
                                        @can('user edit')
                                        @if($user->id !== 1 && $user->id !== auth()->id())
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-info btn-sm mr-1">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </a>
                                        @endif
                                        @endcan
                                        @can('user delete')
                                        @if($user->id !== 1 && $user->id !== auth()->id())
                                            <form class="delete-form" action="{{ route('users.destroy', $user->id) }}" method="post">
                                                @csrf
                                                @method('delete')
                                                <button type="button" class="btn btn-danger btn-sm delete-btn">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        @endif
                                        @endcan
                                        @if(!auth()->user()->can('user edit') && !auth()->user()->can('user delete'))
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.delete-btn').on('click', function(e) {
                var form = $(this).closest('.delete-form');

                Swal.fire({
                    title: 'Delete User?',
                    text: 'This action cannot be undone. Type DELETE to confirm.',
                    type: 'warning',
                    input: 'text',
                    inputPlaceholder: 'Type DELETE to confirm',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                    preConfirm: function(input) {
                        if (input !== 'DELETE') {
                            Swal.showValidationMessage('Please type DELETE to confirm.');
                        }
                    }
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
                    timer: 5000
                });
            });
        </script>
    @endif
@stop
