<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 20px; }
        .container { max-width: 480px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1e1b4b, #312e81); padding: 32px 24px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; margin: 0; }
        .header p { color: #a5b4fc; font-size: 13px; margin-top: 6px; }
        .body { padding: 32px 24px; text-align: center; }
        .body p { color: #475569; font-size: 14px; line-height: 1.6; }
        .otp-box { display: inline-block; background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; font-size: 36px; font-weight: 800; letter-spacing: 12px; padding: 16px 32px; border-radius: 12px; margin: 24px 0; }
        .note { color: #94a3b8; font-size: 12px; margin-top: 16px; }
        .footer { background: #f8fafc; padding: 16px 24px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #94a3b8; font-size: 11px; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 PT MCA</h1>
            <p>Sistem Manajemen Transaksi & Inventaris</p>
        </div>
        <div class="body">
            <p>Halo <strong>{{ $userName }}</strong>,</p>
            <p>Berikut adalah kode OTP untuk login ke akun Anda:</p>
            <div class="otp-box">{{ $otpCode }}</div>
            <p>Masukkan kode ini di halaman verifikasi OTP.</p>
            <p class="note">⏱ Kode berlaku selama <strong>5 menit</strong>. Jangan bagikan kode ini kepada siapapun.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} PT MCA. Jika Anda tidak merasa login, abaikan email ini.</p>
        </div>
    </div>
</body>
</html>
