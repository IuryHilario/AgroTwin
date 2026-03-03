<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Login - AgroTwin</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/auth/auth.css'])
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-background">
            <div class="floating-elements">
                <div class="leaf leaf-1"><i class="fas fa-leaf"></i></div>
                <div class="leaf leaf-2"><i class="fas fa-seedling"></i></div>
                <div class="leaf leaf-3"><i class="fas fa-leaf"></i></div>
                <div class="leaf leaf-4"><i class="fas fa-seedling"></i></div>
            </div>
        </div>

        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="logo">
                        <i class="fas fa-seedling"></i>
                        <h1>AgroSolo Inteligente</h1>
                    </div>
                    <p class="subtitle">Sistema de Monitoramento de Solo com IoT e IA</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="auth-form" autocomplete="off">
                    @csrf

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Senha</label>
                        <div class="password-input">
                            <input type="password" id="password" name="password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-options">
                        <label class="checkbox-container">
                            <input type="checkbox" name="remember">
                            <span class="checkmark"></span>
                            Lembrar-me
                        </label>
                        <a href="#" class="forgot-password">Esqueceu a senha?</a>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-sign-in-alt"></i>
                        Entrar
                    </button>

                    <div class="auth-footer">
                        <p>Não tem uma conta? <a href="{{ route('register') }}">Cadastre-se aqui</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>
