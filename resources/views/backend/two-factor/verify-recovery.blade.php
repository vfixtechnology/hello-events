<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Backup Code</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center" style="min-height:100vh;">
            <div class="col-md-5 d-flex align-items-center">
                <div class="card shadow-sm w-100" style="border-radius:12px;">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <i class="fas fa-key text-warning" style="font-size:40px;"></i>
                        </div>
                        <h4 class="text-center mb-1">Use a Backup Code</h4>
                        <p class="text-muted text-center small mb-4">Each backup code can only be used once.</p>

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('two-factor.verify-recovery') }}" novalidate>
                            @csrf

                            <div class="form-group">
                                <input type="text" name="recovery_code" class="form-control form-control-lg text-center"
                                    placeholder="XXXX-XXXX-XXXX" autocomplete="off" autofocus
                                    value="{{ old('recovery_code') }}">
                            </div>

                            <button type="submit" class="btn btn-warning btn-lg btn-block">
                                <i class="fas fa-check mr-2"></i> Verify with Backup Code
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <a href="{{ route('two-factor.verify-login') }}" class="text-muted small">
                                <i class="fas fa-arrow-left mr-1"></i> Back to authenticator code
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
