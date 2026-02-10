<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FreshHR</title>
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
        .register-left {
            flex: 1;
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px;
            color: white;
        }
        .register-left h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 16px;
            text-align: center;
        }
        .register-left p {
            font-size: 18px;
            opacity: 0.9;
            text-align: center;
        }
        .register-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px;
            overflow-y: auto;
        }
        .register-box {
            width: 100%;
            max-width: 400px;
        }
        .register-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 48px;
        }
        .register-logo-icon {
            width: 48px;
            height: 48px;
            background: #4ade80;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px -5px rgba(74, 222, 128, 0.4);
        }
        .register-logo-icon span {
            color: white;
            font-weight: 700;
            font-size: 24px;
        }
        .register-logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
        }
        .register-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }
        .register-subtitle {
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
        .form-input.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
        }
        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .btn-register {
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
        .btn-register:hover {
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
        .login-link {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #64748b;
        }
        .login-link a {
            color: #4ade80;
            font-weight: 600;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .register-left { display: none; }
            .register-right { padding: 24px; }
        }
    </style>
</head>
<body>
    <div class="register-left">
        <h1>FreshHR</h1>
        <p>Sistem Manajemen HR Modern</p>
    </div>
    <div class="register-right">
        <div class="register-box">
            <div class="register-logo">
                <div class="register-logo-icon">
                    <span>F</span>
                </div>
                <span class="register-logo-text">FreshHR</span>
            </div>
            <h2 class="register-title">Buat Akun Baru</h2>
            <p class="register-subtitle">Daftar untuk mulai menggunakan FreshHR</p>

            @if($errors->any())
                <div class="alert-error">
                    <i data-lucide="alert-circle" style="width: 16px; height: 16px;"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-input @error('name') error @enderror" placeholder="John Doe" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div class="error-message">
                            <i data-lucide="alert-triangle" style="width: 12px; height: 12px;"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input @error('email') error @enderror" placeholder="nama@perusahaan.com" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="error-message">
                            <i data-lucide="alert-triangle" style="width: 12px; height: 12px;"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input @error('password') error @enderror" placeholder="Minimal 8 karakter" required>
                    @error('password')
                        <div class="error-message">
                            <i data-lucide="alert-triangle" style="width: 12px; height: 12px;"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-input" placeholder="Ulangi password" required>
                </div>
                <button type="submit" class="btn-register">Daftar</button>
            </form>

            <div class="login-link">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
