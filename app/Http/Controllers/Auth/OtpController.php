<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    /**
     * Tampilkan halaman verifikasi OTP.
     */
    public function show(Request $request)
    {
        // Pastikan ada session otp_user_id
        if (!$request->session()->has('otp_user_id')) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Silakan login terlebih dahulu.']);
        }

        return view('auth.verify-otp');
    }

    /**
     * Verifikasi kode OTP.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        $userId = $request->session()->get('otp_user_id');

        if (!$userId) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Session kedaluwarsa. Silakan login ulang.']);
        }

        // Cari OTP yang valid
        $otp = OtpCode::where('user_id', $userId)
            ->where('code', $request->otp_code)
            ->where('is_used', false)
            ->first();

        if (!$otp) {
            return back()->with('otp_error', 'Kode OTP salah. Silakan coba lagi.');
        }

        if ($otp->isExpired()) {
            return back()->with('otp_error', 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang.');
        }

        // Tandai OTP sudah dipakai
        $otp->update(['is_used' => true]);

        // Login user
        $user = User::find($userId);
        Auth::login($user);

        // Bersihkan session OTP
        $request->session()->forget(['otp_user_id', 'otp_email']);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Kirim ulang OTP.
     */
    public function resend(Request $request)
    {
        $userId = $request->session()->get('otp_user_id');

        if (!$userId) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Session kedaluwarsa. Silakan login ulang.']);
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')
                ->withErrors(['email' => 'User tidak ditemukan.']);
        }

        // Generate OTP baru
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Hapus OTP lama
        OtpCode::where('user_id', $user->id)->where('is_used', false)->delete();

        // Simpan OTP baru
        OtpCode::create([
            'user_id'    => $user->id,
            'code'       => $otpCode,
            'expires_at' => now()->addMinutes(5),
        ]);

        // Kirim email dengan fallback try-catch jika SMTP bermasalah di production
        try {
            Mail::to($user->email)->send(new OtpMail($otpCode, $user->name));
            $otpMessage = 'Kode OTP baru telah dikirim ke email Anda.';
        } catch (\Throwable $e) {
            // Log error
            logger()->error('Gagal mengirim email OTP (Resend): ' . $e->getMessage());
            // Tampilkan kode OTP langsung di layar sebagai fallback
            $otpMessage = 'Kode OTP baru Anda adalah: ' . $otpCode . ' (Pemberitahuan: Layanan email SMTP sedang mengalami gangguan/timeout).';
        }

        return back()->with('otp_success', $otpMessage);
    }
}
