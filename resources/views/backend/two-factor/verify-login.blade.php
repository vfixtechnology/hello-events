<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Two-Factor Verification</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center" style="min-height:100vh;">
            <div class="col-md-5 d-flex align-items-center">
                <div class="card shadow-sm w-100" style="border-radius:12px;">
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <i class="fas fa-shield-alt text-primary" style="font-size:40px;"></i>
                        </div>
                        <h4 class="mb-1">Two-Factor Verification</h4>
                        <p class="text-muted mb-4">Enter the code from your authenticator app</p>

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('two-factor.verify-login') }}" novalidate>
                            @csrf

                            <div class="form-group">
                                <input type="text" name="otp" class="form-control form-control-lg text-center"
                                    placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]*"
                                    autocomplete="off" autofocus
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                    style="font-size:28px;letter-spacing:10px;font-weight:700;">
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-check-circle mr-2"></i> Verify & Sign In
                            </button>
                        </form>

                        <div class="mt-4">
                            <a href="{{ route('two-factor.verify-recovery') }}" class="text-muted small">
                                <i class="fas fa-key mr-1"></i> Use a backup code
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
