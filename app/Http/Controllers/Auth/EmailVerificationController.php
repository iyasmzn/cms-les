<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    /**
     * Tampilkan pemberitahuan agar pengguna memverifikasi emailnya.
     */
    public function notice(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('home'));
        }

        return view('auth.verify-email');
    }

    /**
     * Proses link verifikasi yang diklik dari email (URL bertanda tangan).
     */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('home'))
                ->with('success', 'Email Anda sudah terverifikasi.');
        }

        $request->fulfill();

        return redirect()->intended(route('home'))
            ->with('success', 'Email berhasil diverifikasi. Terima kasih!');
    }

    /**
     * Kirim ulang email verifikasi.
     */
    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('home'));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Link verifikasi baru telah dikirim ke email Anda.');
    }
}
