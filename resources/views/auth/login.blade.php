<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Barbershop POS System</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-dark: #0a0a0a;
            --primary-black: #000000;
            --accent-gold: #d4af37;
            --accent-gold-dark: #b8960c;
            --text-light: #ffffff;
            --text-gray: #a0a0a0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary-black) 0%, var(--primary-dark) 100%);
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }
        
        /* Background Pattern */
        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d4af37' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.1;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }
        
        .login-card {
            background: rgba(10, 10, 10, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 24px;
            padding: 0;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeInUp 0.6s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            text-align: center;
            padding: 40px 40px 20px 40px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
        }
        
        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--accent-gold) 0%, var(--accent-gold-dark) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.4);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 15px rgba(212, 175, 55, 0);
            }
        }
        
        .logo-icon i {
            font-size: 40px;
            color: var(--primary-black);
        }
        
        .login-header h2 {
            font-weight: 800;
            margin-bottom: 8px;
            background: linear-gradient(135deg, var(--accent-gold) 0%, var(--text-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .login-header p {
            color: var(--text-gray);
            font-size: 0.9rem;
        }
        
        .login-body {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--accent-gold);
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .input-group {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 12px;
            transition: all 0.3s;
        }
        
        .input-group:focus-within {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }
        
        .input-group-text {
            background: transparent;
            border: none;
            color: var(--accent-gold);
            padding: 12px 15px;
        }
        
        .form-control {
            background: transparent;
            border: none;
            color: var(--text-light);
            padding: 12px 15px 12px 0;
        }
        
        .form-control:focus {
            background: transparent;
            box-shadow: none;
            color: var(--text-light);
        }
        
        .form-control::placeholder {
            color: rgba(160, 160, 160, 0.5);
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--accent-gold) 0%, var(--accent-gold-dark) 100%);
            border: none;
            color: var(--primary-black);
            font-weight: 700;
            padding: 14px;
            border-radius: 12px;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(212, 175, 55, 0.3);
            color: var(--primary-black);
        }
        
        .form-check-label {
            color: var(--text-gray);
            font-size: 0.85rem;
        }
        
        .form-check-input:checked {
            background-color: var(--accent-gold);
            border-color: var(--accent-gold);
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background: rgba(220, 38, 38, 0.1);
            border-left: 3px solid var(--danger-red);
            color: #fca5a5;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-left: 3px solid var(--success-green);
            color: #6ee7b7;
        }
        
        .demo-credentials {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 12px;
            padding: 15px;
            margin-top: 20px;
            border: 1px solid rgba(212, 175, 55, 0.1);
        }
        
        .demo-credentials p {
            margin-bottom: 5px;
            font-size: 0.8rem;
        }
        
        .demo-credentials code {
            background: rgba(212, 175, 55, 0.1);
            padding: 2px 6px;
            border-radius: 6px;
            color: var(--accent-gold);
            font-size: 0.75rem;
        }
        
        @media (max-width: 768px) {
            .login-card {
                margin: 20px;
                max-width: 100%;
            }
            
            .login-header {
                padding: 30px 30px 15px 30px;
            }
            
            .login-body {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-icon">
                    <i class="fas fa-cut"></i>
                </div>
                <h2>BARBERSHOP POS</h2>
                <p>Professional Point of Sale System</p>
            </div>
            
            <div class="login-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope me-2"></i>EMAIL ADDRESS
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control" 
                                   id="email" name="email" value="{{ old('email') }}" 
                                   required autofocus placeholder="admin@barbershop.com">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock me-2"></i>PASSWORD
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" 
                                   id="password" name="password" required placeholder="********">
                        </div>
                    </div>

                    <div class="form-group d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember Me</label>
                        </div>
                        <a href="#" class="text-decoration-none" style="color: var(--accent-gold); font-size: 0.85rem;">
                            Forgot Password?
                        </a>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i> Login
                    </button>
                </form>

                <div class="demo-credentials">
                    <p class="text-muted mb-2">
                        <i class="fas fa-info-circle me-1"></i> Demo Credentials:
                    </p>
                    <p class="mb-1">
                        <i class="fas fa-user-shield me-1"></i> Admin: 
                        <code>admin@barbershop.com</code> / <code>admin123</code>
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-user me-1"></i> Cashier: 
                        <code>kasir@barbershop.com</code> / <code>kasir123</code>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Add animation effect on input focus
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.closest('.input-group').style.transform = 'scale(1.02)';
            });
            input.addEventListener('blur', function() {
                this.closest('.input-group').style.transform = 'scale(1)';
            });
        });
        
        // Auto fill demo credentials (optional)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('demo') === 'admin') {
            document.getElementById('email').value = 'admin@barbershop.com';
            document.getElementById('password').value = 'admin123';
        }
    </script>
</body>
</html>