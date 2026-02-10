<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - FreshHR</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f1f5f9;
        }
        .verify-left {
            flex: 1;
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px;
            color: white;
        }
        .verify-left h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 16px;
            text-align: center;
        }
        .verify-left p {
            font-size: 18px;
            opacity: 0.9;
            text-align: center;
        }
        .verify-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px;
        }
        .verify-box {
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .verify-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 48px;
        }
        .verify-logo-icon {
            width: 48px;
            height: 48px;
            background: #4ade80;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px -5px rgba(74, 222, 128, 0.4);
        }
        .verify-logo-icon span {
            color: white;
            font-weight: 700;
            font-size: 24px;
        }
        .verify-logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
        }
        .verify-icon {
            width: 80px;
            height: 80px;
            background: #dcfce7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 32px;
            color: #22c55e;
        }
        .verify-icon svg {
            width: 40px;
            height: 40px;
        }
        .verify-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 16px;
        }
        .verify-text {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 32px;
            line-height: 1.6;
        }
        .verify-email {
            font-weight: 600;
            color: #1e293b;
        }
        .btn-resend {
            width: 100%;
            background: #4ade80;
            color: #1e293b;
            border: none;
            border-radius: 12px;
            padding: 14px 24px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 10px 25px -5px rgba(74, 222, 128, 0.3);
            margin-bottom: 16px;
        }
        .btn-resend:hover {
            background: #22c55e;
            transform: translateY(-2px);
        }
        .btn-logout {
            width: 100%;
            background: white;
            color: #64748b;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 24px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-logout:hover {
            background: #f1f5f9;
            color: #1e293b;
        }
        .alert-success {
            background: #dcfce7;
            color: #15803d;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        @media (max-width: 768px) {
            .verify-left { display: none; }
            .verify-right { padding: 24px; }
        }
    </style>
</head>
<body>
    <div class="verify-left">
        <h1>FreshHR</h1>
        <p>Sistem Manajemen HR Modern</p>
    </div>
    <div class="verify-right">
        <div class="verify-box">
            <div class="verify-logo">
                <div class="verify-logo-icon">
                    <span>F</span>
                </div>
                <span class="verify-logo-text">FreshHR</span>
            </div>

            <div class="verify-icon">
                <i data-lucide="mail-check"></i>
            </div>

            <h2 class="verify-title">Verifikasi Email Anda</h2>
            <p class="verify-text">
                Terima kasih telah mendaftar! Silakan verifikasi email Anda dengan mengklik link yang kami kirimkan ke <span class="verify-email">{{ auth()->user()->email }}</span>.
            </p>

            @if(session('status') === 'verification-link-sent')
                <div class="alert-success">
                    <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                    Link verifikasi baru telah dikirim ke email Anda.
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn-resend">
                    Kirim Ulang Link Verifikasi
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    Keluar
                </button>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
