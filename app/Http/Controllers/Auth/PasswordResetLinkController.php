<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            // Coba kirim email reset password secara normal
            $status = Password::sendResetLink(
                $request->only('email')
            );

            return $status == Password::RESET_LINK_SENT
                        ? back()->with('status', __($status))
                        : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
        } catch (\Throwable $e) {
            // Log error
            logger()->error('Gagal mengirim email reset password: ' . $e->getMessage());

            // Cari user
            $user = \App\Models\User::where('email', $request->email)->first();
            if ($user) {
                // Generate token reset password secara manual
                $token = Password::createToken($user);
                $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);
                
                // Kembalikan ke halaman sebelumnya dengan pesan sukses berisi link reset password langsung
                return back()->with('status', 'Gagal mengirim email karena SMTP server timeout. Namun, Anda dapat mereset password melalui link berikut: ' . $resetUrl);
            }

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Gagal mengirim email reset password: ' . $e->getMessage()]);
        }
    }
}
