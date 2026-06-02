<x-guest-layout>
    <div class="mb-4 text-sm" style="color: #94a3b8; text-align: center;">
        Kode OTP telah dikirim ke email <strong style="color: #a5b4fc;">{{ session('otp_email') }}</strong>. 
        Masukkan kode 6 digit untuk melanjutkan login.
    </div>

    @if(session('otp_error'))
        <div style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #f87171; padding: 0.75rem 1rem; border-radius: 0.75rem; font-size: 0.85rem; margin-bottom: 1rem; text-align: center;">
            {{ session('otp_error') }}
        </div>
    @endif

    @if(session('otp_success'))
        <div style="background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.3); color: #4ade80; padding: 0.75rem 1rem; border-radius: 0.75rem; font-size: 0.85rem; margin-bottom: 1rem; text-align: center;">
            {{ session('otp_success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf

        <div>
            <label for="otp_code" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.5rem;">Kode OTP</label>
            <input type="text" id="otp_code" name="otp_code" 
                   maxlength="6" 
                   inputmode="numeric" 
                   pattern="[0-9]*"
                   autocomplete="one-time-code"
                   placeholder="______"
                   required autofocus
                   style="width: 100%; text-align: center; font-size: 2rem; font-weight: 700; letter-spacing: 0.8rem; 
                          background: rgba(15,23,42,0.6); border: 1px solid rgba(255,255,255,0.1); 
                          color: #f8fafc; border-radius: 0.75rem; padding: 0.75rem 1rem; 
                          box-sizing: border-box;">
            @error('otp_code')
                <p style="color: #f87171; font-size: 0.8rem; margin-top: 0.35rem;">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" style="display: flex; justify-content: center; align-items: center; width: 100%; 
                background: linear-gradient(135deg, #6366f1, #4f46e5); border: none; border-radius: 0.75rem; 
                color: white; font-weight: 600; font-size: 0.95rem; padding: 0.75rem 1.5rem; cursor: pointer; 
                margin-top: 1.25rem; box-shadow: 0 4px 12px rgba(79,70,229,0.3); letter-spacing: 0.05em;">
            🔓 Verifikasi & Login
        </button>
    </form>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.25rem;">
        <form method="POST" action="{{ route('otp.resend') }}" style="display: inline;">
            @csrf
            <button type="submit" style="background: none; border: none; color: #94a3b8; font-size: 0.85rem; cursor: pointer; font-weight: 500; padding: 0;">
                🔄 Kirim Ulang OTP
            </button>
        </form>
        <a href="{{ route('login') }}" style="color: #94a3b8; font-size: 0.85rem; text-decoration: none; font-weight: 500;">
            ← Kembali ke Login
        </a>
    </div>
</x-guest-layout>
