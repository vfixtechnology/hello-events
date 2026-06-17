@extends('adminlte::page')

@section('title', 'Setup Two-Factor Authentication')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>
            <i class="fas fa-qrcode mr-2 text-primary"></i> Setup Two-Factor Authentication
        </h1>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <x-adminlte-card title="Step 1: Scan QR Code" icon="fas fa-qrcode text-primary"
                theme="primary" theme-mode="outline">
                <div class="text-center mb-4">
                    <p class="text-muted">Scan this QR code with your authenticator app (Google Authenticator, Authy, etc.)</p>
                    <div class="d-inline-block p-3 bg-white border rounded">
                        {!! $qrCode !!}
                    </div>
                </div>

                <div class="text-center mb-4">
                    <p class="text-muted mb-1">Or manually enter this secret key:</p>
                    <div class="d-inline-block">
                        <code class="p-2 bg-light border rounded" id="secretKey" style="font-size: 1.1rem; letter-spacing: 2px;">
                            {{ $secret }}
                        </code>
                        <button class="btn btn-sm btn-outline-secondary ml-2" onclick="copySecret()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </x-adminlte-card>

            <x-adminlte-card title="Step 2: Verify Code" icon="fas fa-check-circle text-success"
                theme="success" theme-mode="outline">
                <p class="text-muted">Enter the 6-digit code from your authenticator app to verify and enable 2FA.</p>

                <form method="POST" action="{{ route('two-factor.enable') }}">
                    @csrf
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" name="otp" class="form-control form-control-lg text-center"
                                    placeholder="Enter 6-digit code" required maxlength="6"
                                    autocomplete="off" inputmode="numeric" pattern="[0-9]*"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                            </div>
                            <button type="submit" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-shield-alt mr-2"></i> Enable Two-Factor Authentication
                            </button>
                        </div>
                    </div>
                </form>
            </x-adminlte-card>

            <div class="text-center mb-4">
                <a href="{{ route('two-factor.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Back to 2FA Settings
                </a>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    function copySecret() {
        var secret = document.getElementById('secretKey');
        var range = document.createRange();
        range.selectNode(secret);
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        document.execCommand('copy');
        window.getSelection().removeAllRanges();

        var btn = event.currentTarget;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(function() {
            btn.innerHTML = '<i class="fas fa-copy"></i>';
        }, 2000);
    }
</script>
@stop
