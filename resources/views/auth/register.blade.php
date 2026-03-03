<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Cadastro - AgroTwin</title>
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
                <div class="leaf leaf-5"><i class="fas fa-leaf"></i></div>
                <div class="leaf leaf-6"><i class="fas fa-seedling"></i></div>
            </div>
        </div>

        <div class="auth-container">
            <div class="auth-card register-card">
                <div class="auth-header">
                    <div class="logo">
                        <i class="fas fa-seedling"></i>
                        <h1>AgroTwin</h1>
                    </div>
                    <p class="subtitle">Cadastre-se para começar a monitorar seu solo</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="auth-form register-form">
                    @csrf

                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> Nome</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Senha</label>
                        <div class="password-input">
                            <input type="password" id="password" name="password" required>
                            <button type="button" class="toggle-password"
                                onclick="togglePassword('password', 'toggleIcon1')">
                                <i class="fas fa-eye" id="toggleIcon1"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation"><i class="fas fa-lock"></i> Confirmar Senha</label>
                        <div class="password-input">
                            <input type="password" id="password_confirmation" name="password_confirmation" required>
                            <button type="button" class="toggle-password"
                                onclick="togglePassword('password_confirmation', 'toggleIcon2')">
                                <i class="fas fa-eye" id="toggleIcon2"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-user-plus"></i>
                        Cadastrar
                    </button>

                    <div class="auth-footer">
                        <p>Já tem uma conta? <a href="{{ route('login') }}">Faça login aqui</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);

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
