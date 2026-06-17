@extends('adminlte::page')

@section('title', 'Backup Codes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>
            <i class="fas fa-key mr-2 text-primary"></i> Backup Codes
        </h1>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

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
            <x-adminlte-card title="Your Backup Codes" icon="fas fa-key text-warning"
                theme="warning" theme-mode="outline">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Important:</strong> These backup codes can each be used once to access your account
                    if you lose access to your authenticator app. Store them in a safe place.
                    <strong class="d-block mt-2">Do not share these codes with anyone!</strong>
                </div>

                <div class="row justify-content-center my-4" id="backupCodes">
                    <div class="col-md-8">
                        <div class="bg-dark text-light p-4 rounded font-monospace" style="font-family: 'Courier New', monospace;">
                            @foreach($codes as $code)
                                <div class="mb-2" style="font-size: 1.2rem; letter-spacing: 3px;">
                                    <i class="fas fa-key mr-2 text-warning"></i> {{ $code }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="text-center mb-4">
                    <button class="btn btn-info mr-2" onclick="copyCodes()">
                        <i class="fas fa-copy mr-1"></i> Copy Codes
                    </button>
                    <button class="btn btn-success mr-2" onclick="downloadCodes()">
                        <i class="fas fa-download mr-1"></i> Download Codes
                    </button>
                    <button class="btn btn-outline-secondary" onclick="printCodes()">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                </div>

                <hr>

                <div class="text-center">
                    <form method="POST" action="{{ route('two-factor.regenerate-backup-codes') }}" class="d-inline"
                        onsubmit="return confirm('This will invalidate your existing backup codes. Are you sure?')">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-sync mr-1"></i> Regenerate Backup Codes
                        </button>
                    </form>
                    <a href="{{ route('two-factor.index') }}" class="btn btn-outline-secondary ml-2">
                        <i class="fas fa-arrow-left mr-1"></i> Back to 2FA Settings
                    </a>
                </div>
            </x-adminlte-card>
        </div>
    </div>
@stop

@section('js')
<script>
    function getCodesText() {
        var codes = [];
        document.querySelectorAll('#backupCodes .font-monospace > div').forEach(function(el) {
            var text = el.textContent.trim();
            codes.push(text);
        });
        return codes.join('\n');
    }

    function copyCodes() {
        var text = getCodesText();
        navigator.clipboard.writeText(text).then(function() {
            var btn = event.currentTarget;
            btn.innerHTML = '<i class="fas fa-check mr-1"></i> Copied!';
            setTimeout(function() { btn.innerHTML = '<i class="fas fa-copy mr-1"></i> Copy Codes'; }, 2000);
        });
    }

    function downloadCodes() {
        var text = getCodesText();
        var blob = new Blob([text], { type: 'text/plain' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'backup-codes-' + new Date().toISOString().split('T')[0] + '.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    function printCodes() {
        var codes = document.querySelectorAll('#backupCodes .font-monospace > div');
        var win = window.open('', '_blank');
        win.document.write('<html><head><title>Backup Codes</title>');
        win.document.write('<style>body{font-family:"Courier New",monospace;padding:40px;}h2{text-align:center}.code{font-size:18px;letter-spacing:3px;margin-bottom:12px;text-align:center}</style>');
        win.document.write('</head><body>');
        win.document.write('<h2>Backup Codes</h2>');
        win.document.write('<p style="text-align:center;color:#666;">Store these codes safely. Each can be used once.</p>');
        win.document.write('<hr style="max-width:400px;margin:20px auto">');
        codes.forEach(function(el) {
            win.document.write('<div class="code">' + el.textContent.trim() + '</div>');
        });
        win.document.write('</body></html>');
        win.document.close();
        win.print();
    }
</script>
@stop
