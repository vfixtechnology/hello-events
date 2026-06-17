<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FALaravel\Google2FA;

class TwoFactorController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('backend.two-factor.index', compact('user'));
    }

    public function setup(Google2FA $google2fa)
    {
        $user = auth()->user();

        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.index')
                ->with('error', 'Two-factor authentication is already enabled.');
        }

        $secret = $google2fa->generateSecretKey();
        $user->google2fa_secret = $secret;
        $user->save();

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $qrCode = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret,
            250
        );

        return view('backend.two-factor.setup', compact(
            'secret', 'qrCodeUrl', 'qrCode', 'user'
        ));
    }

    public function enable(Request $request, Google2FA $google2fa)
    {
        $request->validate([
            'otp' => 'required|string',
        ]);

        $user = auth()->user();

        $valid = $google2fa->verifyKey(
            $user->google2fa_secret,
            $request->otp
        );

        if (!$valid) {
            return back()->with('error', 'Invalid verification code. Please try again.');
        }

        $user->google2fa_enabled = true;
        $codes = $user->generateRecoveryCodes();
        $user->save();

        return redirect()->route('two-factor.backup-codes')
            ->with('success', 'Two-factor authentication has been enabled successfully. Save your backup codes!');
    }

    public function verifyLogin(Request $request, Google2FA $google2fa)
    {
        if (!session()->has('two-factor:user:id')) {
            return redirect('login');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'otp' => 'required_without:recovery_code|string',
                'recovery_code' => 'required_without:otp|string',
            ]);

            $user = \App\Models\User::find(session('two-factor:user:id'));

            if (!$user || !$user->hasTwoFactorEnabled()) {
                session()->forget('two-factor:user:id');
                return redirect('login')->with('error', 'Invalid session. Please login again.');
            }

            if ($request->filled('otp')) {
                $valid = $google2fa->verifyKey(
                    $user->google2fa_secret,
                    $request->otp
                );
            } else {
                $valid = $user->useRecoveryCode($request->recovery_code);
            }

            if ($valid) {
                session()->forget('two-factor:user:id');
                auth()->login($user);
                return redirect()->intended('/admin/dashboard');
            }

            return back()->withInput()->with('error', 'Invalid code. Please try again.');
        }

        return view('backend.two-factor.verify-login');
    }

    public function verifyLoginRecovery(Request $request)
    {
        if (!session()->has('two-factor:user:id')) {
            return redirect('login');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'recovery_code' => 'required|string',
            ]);

            $user = \App\Models\User::find(session('two-factor:user:id'));

            if (!$user || !$user->hasTwoFactorEnabled()) {
                session()->forget('two-factor:user:id');
                return redirect('login')->with('error', 'Invalid session. Please login again.');
            }

            if ($user->useRecoveryCode($request->recovery_code)) {
                session()->forget('two-factor:user:id');
                auth()->login($user);
                return redirect()->intended('/admin/dashboard');
            }

            return back()->withInput()->with('error', 'Invalid backup code. Please try again.');
        }

        return view('backend.two-factor.verify-recovery');
    }

    public function disable(Request $request, Google2FA $google2fa)
    {
        $request->validate([
            'otp' => 'required|string',
        ]);

        $user = auth()->user();

        $valid = $google2fa->verifyKey(
            $user->google2fa_secret,
            $request->otp
        );

        if (!$valid) {
            return back()->with('error', 'Invalid verification code. Please try again.');
        }

        $user->google2fa_secret = null;
        $user->google2fa_enabled = false;
        $user->two_factor_recovery_codes = null;
        $user->save();

        return redirect()->route('two-factor.index')
            ->with('success', 'Two-factor authentication has been disabled successfully.');
    }

    public function backupCodes()
    {
        $user = auth()->user();
        $codes = $user->getRecoveryCodes();

        if (empty($codes) || !$user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.index');
        }

        return view('backend.two-factor.backup-codes', compact('codes'));
    }

    public function regenerateBackupCodes()
    {
        $user = auth()->user();

        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.index');
        }

        $codes = $user->generateRecoveryCodes();

        return redirect()->route('two-factor.backup-codes')
            ->with('success', 'New backup codes have been generated.');
    }
}
