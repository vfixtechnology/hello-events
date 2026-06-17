@extends('adminlte::page')

@section('title', 'Two-Factor Authentication')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>
            <i class="fas fa-shield-alt mr-2 text-primary"></i> Two-Factor Authentication
        </h1>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <x-adminlte-card title="Two-Factor Authentication Status" icon="fas fa-shield-alt text-primary"
                theme="primary" theme-mode="outline">
                @if($user->hasTwoFactorEnabled())
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <span class="badge badge-success badge-pill p-3" style="font-size: 1.1rem;">
                                <i class="fas fa-check-circle mr-2"></i> Two-Factor Authentication is ENABLED
                            </span>
                        </div>
                        <p class="text-muted mb-4">
                            Your account is protected with two-factor authentication.
                            Each time you sign in, you'll need your password and a verification code from your authenticator app.
                        </p>
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <a href="{{ route('two-factor.backup-codes') }}" class="btn btn-info btn-block mb-2">
                                    <i class="fas fa-key mr-2"></i> View Backup Codes
                                </a>
                            </div>
                        </div>
                        <hr class="my-4">
                        <h5 class="text-danger mb-3">Disable Two-Factor Authentication</h5>
                        <p class="text-muted mb-3">To disable, verify with a code from your authenticator app.</p>
                        <form method="POST" action="{{ route('two-factor.disable') }}" class="d-inline" id="disableForm">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="input-group mb-3">
                                        <input type="text" name="otp" class="form-control text-center"
                                            placeholder="Enter 6-digit code" required maxlength="6" inputmode="numeric"
                                            pattern="[0-9]*" autocomplete="off"
                                            oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-danger" id="disableBtn">
                                                <i class="fas fa-unlock mr-1"></i> Disable
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <span class="badge badge-secondary badge-pill p-3" style="font-size: 1.1rem;">
                                <i class="fas fa-times-circle mr-2"></i> Two-Factor Authentication is DISABLED
                            </span>
                        </div>
                        <p class="text-muted mb-4">
                            Add an extra layer of security to your account by enabling two-factor authentication.
                            You'll need to scan a QR code with your authenticator app (Google Authenticator, Authy, etc.).
                        </p>
                        <a href="{{ route('two-factor.setup') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-qrcode mr-2"></i> Setup Two-Factor Authentication
                        </a>
                    </div>
                @endif
            </x-adminlte-card>
        </div>

        <div class="col-lg-4">
            <x-adminlte-card title="What is 2FA?" icon="fas fa-question-circle text-info"
                theme="info" theme-mode="outline">
                <p class="text-muted">
                    Two-factor authentication adds an extra layer of security to your account
                    by requiring not only your password but also a verification code from your
                    authenticator app.
                </p>
                <hr>
                <h6><i class="fas fa-mobile-alt mr-2 text-primary"></i> Recommended Apps</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fab fa-google mr-2 text-success"></i> Google Authenticator
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-lock mr-2 text-info"></i> Authy
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-key mr-2 text-warning"></i> Microsoft Authenticator
                    </li>
                </ul>
            </x-adminlte-card>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            setTimeout(function() {
                Swal.fire({
                    icon: 'success',
                    text: '{{ session('success') }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000
                });
            }, 100);
        @endif

        @if(session('error'))
            setTimeout(function() {
                Swal.fire({
                    icon: 'error',
                    text: '{{ session('error') }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000
                });
            }, 100);
        @endif

        var disableBtn = document.getElementById('disableBtn');
        if (disableBtn) {
            disableBtn.addEventListener('click', function(e) {
                var otp = document.querySelector('input[name="otp"]').value;
                if (otp.length !== 6) {
                    Swal.fire({
                        icon: 'warning',
                        text: 'Please enter a valid 6-digit code.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }
                Swal.fire({
                    title: 'Disable Two-Factor Authentication?',
                    text: 'Your account will no longer be protected by 2FA.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, disable it',
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-danger btn-lg mr-3',
                        cancelButton: 'btn btn-secondary btn-lg'
                    }
                }).then(function(result) {
                    if (result.isConfirmed) {
                        document.getElementById('disableForm').submit();
                    }
                });
            });
        }
    });
</script>
@stop
