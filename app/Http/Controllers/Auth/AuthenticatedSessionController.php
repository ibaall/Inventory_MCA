<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\OtpCode;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * Untuk karyawan: verifikasi credentials dulu, kirim OTP, redirect ke halaman OTP.
     * Untuk owner/admin: login langsung.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Cek credentials manual terlebih dahulu
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        // Jika karyawan -> kirim OTP dulu, jangan login langsung
        if ($user->role === 'karyawan') {
            // Generate OTP 6 digit
            $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Hapus OTP lama yang belum dipakai
            OtpCode::where('user_id', $user->id)->where('is_used', false)->delete();

            // Simpan OTP baru (berlaku 5 menit)
            OtpCode::create([
                'user_id'    => $user->id,
                'code'       => $otpCode,
                'expires_at' => now()->addMinutes(5),
            ]);

            // Kirim OTP ke email dengan fallback try-catch jika SMTP bermasalah di production
            try {
                Mail::to($user->email)->send(new OtpMail($otpCode, $user->name));
                $otpMessage = 'Kode OTP telah dikirim ke email Anda.';
            } catch (\Exception $e) {
                // Log error
                logger()->error('Gagal mengirim email OTP: ' . $e->getMessage());
                // Tampilkan kode OTP langsung di layar sebagai fallback
                $otpMessage = 'Kode OTP Anda adalah: ' . $otpCode . ' (Pemberitahuan: Layanan email SMTP sedang mengalami gangguan/timeout).';
            }

            // Simpan user_id di session untuk verifikasi
            $request->session()->put('otp_user_id', $user->id);
            $request->session()->put('otp_email', $user->email);

            return redirect()->route('otp.show')
                ->with('otp_success', $otpMessage);
        }

        // Untuk owner/admin: login langsung
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
