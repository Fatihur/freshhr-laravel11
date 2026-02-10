<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - FreshHR</title>
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
        .forgot-left {
            flex: 1;
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px;
            color: white;
        }
        .forgot-left h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 16px;
            text-align: center;
        }
        .forgot-left p {
            font-size: 18px;
            opacity: 0.9;
            text-align: center;
        }
        .forgot-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px;
        }
        .forgot-box {
            width: 100%;
            max-width: 400px;
        }
        .forgot-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 48px;
        }
        .forgot-logo-icon {
            width: 48px;
            height: 48px;
            background: #4ade80;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px -5px rgba(74, 222, 128, 0.4);
        }
        .forgot-logo-icon span {
            color: white;
            font-weight: 700;
            font-size: 24px;
        }
        .forgot-logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
        }
        .forgot-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }
        .forgot-subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 32px;
            line-height: 1.6;
        }
        .form-group {
            margin-bottom: 24px;
        }
        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .form-input {
            width: 100%;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
        }
        .form-input:focus {
            border-color: #4ade80;
            box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.2);
        }
        .btn-send {
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
        }
        .btn-send:hover {
            background: #22c55e;
            transform: translateY(-2px);
        }
        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
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
            gap: 8px;
        }
        .back-link {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #64748b;
        }
        .back-link a {
            color: #4ade80;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .forgot-left { display: none; }
            .forgot-right { padding: 24px; }
        }
    </style>
</head>
<body>
    <div class="forgot-left">
        <h1>FreshHR</h1>
        <p>Sistem Manajemen HR Modern</p>
    </div>
    <div class="forgot-right">
        <div class="forgot-box">
            <div class="forgot-logo">
                <div class="forgot-logo-icon">
                    <span>F</span>
                </div>
                <span class="forgot-logo-text">FreshHR</span>
            </div>
            <h2 class="forgot-title">Lupa Password?</h2>
            <p class="forgot-subtitle">Masukkan email Anda dan kami akan mengirimkan link untuk reset password.</p>

            @if(session('status'))
                <div class="alert-success">
                    <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert-error">
                    <i data-lucide="alert-circle" style="width: 16px; height: 16px;"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" placeholder="nama@perusahaan.com" value="{{ old('email') }}" required autofocus>
                </div>
                <button type="submit" class="btn-send">Kirim Link Reset Password</button>
            </form>

            <div class="back-link">
                <a href="{{ route('login') }}">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                    Kembali ke Login
                </a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
