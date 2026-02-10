<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FreshHR</title>
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
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px;
            color: white;
        }
        .login-left h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 16px;
            text-align: center;
        }
        .login-left p {
            font-size: 18px;
            opacity: 0.9;
            text-align: center;
        }
        .login-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px;
        }
        .login-box {
            width: 100%;
            max-width: 400px;
        }
        .login-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 48px;
        }
        .login-logo-icon {
            width: 48px;
            height: 48px;
            background: #4ade80;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px -5px rgba(74, 222, 128, 0.4);
        }
        .login-logo-icon span {
            color: white;
            font-weight: 700;
            font-size: 24px;
        }
        .login-logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
        }
        .login-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }
        .login-subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 32px;
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
        .btn-login {
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
        .btn-login:hover {
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
        .demo-section {
            margin-top: 32px;
            padding-top: 32px;
            border-top: 1px solid #e2e8f0;
        }
        .demo-title {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }
        .demo-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
        .demo-btn {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            cursor: pointer;
            text-align: left;
            transition: all 0.2s;
        }
        .demo-btn:hover {
            border-color: #4ade80;
            background: #f0fdf4;
        }
        .demo-btn-role {
            font-size: 12px;
            font-weight: 600;
            color: #1e293b;
        }
        .demo-btn-email {
            font-size: 11px;
            color: #64748b;
        }
        @media (max-width: 768px) {
            .login-left { display: none; }
            .login-right { padding: 24px; }
        }
    </style>
</head>
<body>
    <div class="login-left">
        <h1>FreshHR</h1>
        <p>Sistem Manajemen HR Modern</p>
    </div>
    <div class="login-right">
        <div class="login-box">
            <div class="login-logo">
                <div class="login-logo-icon">
                    <span>F</span>
                </div>
                <span class="login-logo-text">FreshHR</span>
            </div>
            <h2 class="login-title">Selamat Datang</h2>
            <p class="login-subtitle">Masuk ke akun Anda untuk melanjutkan</p>

            @if($errors->any())
                <div class="alert-error">
                    <i data-lucide="alert-circle" style="width: 16px; height: 16px;"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" placeholder="nama@perusahaan.com" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-login">Masuk</button>
            </form>

            <div class="demo-section">
                <p class="demo-title">Demo Login (Satu Klik)</p>
                <div class="demo-grid">
                    <button type="button" class="demo-btn" onclick="loginDemo('admin@freshhr.com')">
                        <div class="demo-btn-role">Super Admin</div>
                        <div class="demo-btn-email">Full Access</div>
                    </button>
                    <button type="button" class="demo-btn" onclick="loginDemo('hr@freshhr.com')">
                        <div class="demo-btn-role">HR Admin</div>
                        <div class="demo-btn-email">HR Manager</div>
                    </button>
                    <button type="button" class="demo-btn" onclick="loginDemo('depthead@freshhr.com')">
                        <div class="demo-btn-role">Dept Head</div>
                        <div class="demo-btn-email">Approval</div>
                    </button>
                    <button type="button" class="demo-btn" onclick="loginDemo('employee@freshhr.com')">
                        <div class="demo-btn-role">Employee</div>
                        <div class="demo-btn-email">Karyawan</div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function loginDemo(email) {
            const form = document.querySelector('form');
            const emailInput = document.querySelector('input[name="email"]');
            const passwordInput = document.querySelector('input[name="password"]');

            emailInput.value = email;
            passwordInput.value = 'password';
            form.submit();
        }
    </script>
</body>
</html>
