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
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                @if (Auth::user()->hasMedia('image'))
                                    <img style="width:75px;" class="profile-user-img img-fluid img-circle"
                                        src="{{ Auth::user()->getFirstMediaUrl('image', 'webp') }}"
                                        alt="User profile picture">
                                @else
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                @endif
                            </div>
                            <h3 class="profile-username text-center">{{ Auth::user()->name }}</h3>
                            <p class="text-muted text-center">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <form class="alert-form" action="{{ route('profile.update') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="#personal"
                                            data-toggle="tab">Personal Information</a></li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="active tab-pane" id="personal">
                                        <div class="form-group row">
                                            <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                            <div class="col-sm-10">
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror" name="name"
                                                    id="inputName" placeholder="Name" value="{{ Auth::user()->name }}">
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                            <div class="col-sm-10">
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                                    id="inputEmail" placeholder="Email" value="{{ Auth::user()->email }}">
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputPhone" class="col-sm-2 col-form-label">Mobile no.</label>
                                            <div class="col-sm-10">
                                                <input type="number"
                                                    class="form-control @error('phone') is-invalid @enderror" name="phone"
                                                    id="inputPhone" placeholder="Mobile no."
                                                    value="{{ Auth::user()->phone }}">
                                                @error('phone')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputPhone" class="col-sm-2 col-form-label">Image</label>
                                            <div class="col-sm-10">
                                                <input type="file"
                                                    class="form-control @error('image') is-invalid @enderror" name="image"
                                                    id="image">
                                                @error('image')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="offset-sm-2 col-sm-10">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" name="remove_image"
                                                        id="remove_image" value="1">
                                                    <label class="form-check-label" for="remove_image">Remove current
                                                        image</label>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="offset-sm-2 col-sm-10">
                                        <button type="submit" class="btn btn-danger">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card">
                        <form class="alert-form" action="{{ route('user.password.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#password" data-toggle="tab">Change Password</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="current_password" class="col-sm-2 col-form-label">Old Password *</label>
                                    <div class="col-sm-10">
                                        <input type="password"
                                            class="form-control @error('current_password') is-invalid @enderror"
                                            id="current_password" placeholder="Old Password" name="current_password"
                                            required autocomplete="current-password">
                                        @error('current_password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password" class="col-sm-2 col-form-label">New Password *</label>
                                    <div class="col-sm-10">
                                        <input type="password"
                                            class="form-control @error('password') is-invalid @enderror" id="password"
                                            placeholder="New Password" name="password" required
                                            autocomplete="new-password">
                                        @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password_confirmation" class="col-sm-2 col-form-label">Confirm Password
                                        *</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" id="password_confirmation"
                                            placeholder="Confirm Password" name="password_confirmation" required
                                            autocomplete="new-password">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="offset-sm-2 col-sm-10">
                                    <button type="submit" class="btn btn-danger">Update Password</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#security" data-toggle="tab">Security</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Two-Factor Auth</label>
                                <div class="col-sm-10">
                                    @if(Auth::user()->hasTwoFactorEnabled())
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-success badge-pill p-2 mr-3">
                                                <i class="fas fa-check-circle mr-1"></i> Enabled
                                            </span>
                                            <a href="{{ route('two-factor.index') }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-cog mr-1"></i> Manage
                                            </a>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-secondary badge-pill p-2 mr-3">
                                                <i class="fas fa-times-circle mr-1"></i> Disabled
                                            </span>
                                            <a href="{{ route('two-factor.setup') }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-shield-alt mr-1"></i> Enable Now
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="offset-sm-2 col-sm-10">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Two-factor authentication adds an extra layer of security to your account.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
