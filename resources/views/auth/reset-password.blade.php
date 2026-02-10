<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - FreshHR</title>
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
        .reset-left {
            flex: 1;
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px;
            color: white;
        }
        .reset-left h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 16px;
            text-align: center;
        }
        .reset-left p {
            font-size: 18px;
            opacity: 0.9;
            text-align: center;
        }
        .reset-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px;
        }
        .reset-box {
            width: 100%;
            max-width: 400px;
        }
        .reset-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 48px;
        }
        .reset-logo-icon {
            width: 48px;
            height: 48px;
            background: #4ade80;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px -5px rgba(74, 222, 128, 0.4);
        }
        .reset-logo-icon span {
            color: white;
            font-weight: 700;
            font-size: 24px;
        }
        .reset-logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
        }
        .reset-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }
        .reset-subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 32px;
        }
        .form-group {
            margin-bottom: 20px;
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
        .btn-reset {
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
            margin-top: 8px;
        }
        .btn-reset:hover {
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
        @media (max-width: 768px) {
            .reset-left { display: none; }
            .reset-right { padding: 24px; }
        }
    </style>
</head>
<body>
    <div class="reset-left">
        <h1>FreshHR</h1>
        <p>Sistem Manajemen HR Modern</p>
    </div>
    <div class="reset-right">
        <div class="reset-box">
            <div class="reset-logo">
                <div class="reset-logo-icon">
                    <span>F</span>
                </div>
                <span class="reset-logo-text">FreshHR</span>
            </div>
            <h2 class="reset-title">Reset Password</h2>
            <p class="reset-subtitle">Buat password baru untuk akun Anda</p>

            @if($errors->any())
                <div class="alert-error">
                    <i data-lucide="alert-circle" style="width: 16px; height: 16px;"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $request->email) }}" required readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-input" placeholder="Minimal 8 karakter" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-input" placeholder="Ulangi password baru" required>
                </div>

                <button type="submit" class="btn-reset">Reset Password</button>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
