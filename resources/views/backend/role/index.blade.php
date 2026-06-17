@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Roles & Permissions</h1>
        </div>
    </div>
@stop

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <table class="table table-striped projects">
                                <thead>
                                    <tr>
                                        <th style="width: 1%">#</th>
                                        <th>Role</th>
                                        <th>Permissions</th>
                                        <th>Users</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($roles as $role)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ ucfirst($role->name) }}</strong></td>
                                            <td>
                                                @foreach($role->permissions->take(5) as $perm)
                                                    <span class="badge badge-info">{{ $perm->name }}</span>
                                                @endforeach
                                                @if($role->permissions->count() > 5)
                                                    <span class="badge badge-secondary">+{{ $role->permissions->count() - 5 }} more</span>
                                                @endif
                                            </td>
                                            <td>{{ $role->users->count() }}</td>
                                            <td>
                                                @can('role edit')
                                                <a class="btn btn-info btn-sm" href="{{ route('roles.edit', $role->id) }}">
                                                    <i class="fas fa-shield-alt"></i> Manage Permissions
                                                </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No roles found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('js')
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
