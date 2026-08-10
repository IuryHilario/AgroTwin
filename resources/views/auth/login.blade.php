<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Login - AgroTwin</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Tema (aplica .dark antes do primeiro paint, evitando flash) -->
    <script>
        (function () {
            const stored = localStorage.getItem('theme');
            const isDark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', isDark);
        })();
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body class="font-sans dark:bg-gray-950">
    <div class="relative flex min-h-screen items-center justify-center p-5">
        <div class="fixed inset-0 -z-20 bg-[length:400%_400%] bg-[linear-gradient(135deg,#4CAF50_0%,#8BC34A_25%,#CDDC39_50%,#FF9800_75%,#FF5722_100%)] animate-gradient-shift"></div>

        <div class="pointer-events-none absolute inset-0 -z-10 h-full w-full overflow-hidden">
            <div class="absolute left-[10%] top-[10%] animate-float text-3xl text-white/10"><i class="fas fa-leaf"></i></div>
            <div class="absolute right-[15%] top-[20%] animate-float text-3xl text-white/10 [animation-delay:1s]"><i class="fas fa-seedling"></i></div>
            <div class="absolute left-[5%] top-[60%] animate-float text-3xl text-white/10 [animation-delay:2s]"><i class="fas fa-leaf"></i></div>
            <div class="absolute bottom-[20%] right-[10%] animate-float text-3xl text-white/10 [animation-delay:3s]"><i class="fas fa-seedling"></i></div>
        </div>

        <div class="relative z-10 w-full max-w-[450px]">
            <div class="animate-slide-up rounded-[20px] border border-white/20 bg-white/95 p-10 shadow-[0_25px_45px_rgba(0,0,0,0.1)] backdrop-blur-md max-[480px]:p-6 dark:border-gray-700/50 dark:bg-gray-900/95">
                <div class="mb-9 text-center">
                    <div class="mb-4 flex items-center justify-center gap-4 max-[480px]:flex-col max-[480px]:gap-2.5">
                        <i class="fas fa-seedling animate-logo-pulse text-5xl text-green-500 max-[480px]:text-4xl"></i>
                        <h1 class="bg-gradient-to-br from-green-500 to-green-800 bg-clip-text text-3xl font-bold text-transparent max-[480px]:text-2xl dark:from-green-400 dark:to-green-600">AgroSolo Inteligente</h1>
                    </div>
                    <p class="text-sm text-muted">Sistema de Monitoramento de Solo com IoT e IA</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5" autocomplete="off">
                    @csrf

                    <div class="flex flex-col gap-2">
                        <label for="email" class="flex items-center gap-2 text-sm font-medium text-gray-800 dark:text-gray-200">
                            <i class="fas fa-envelope w-4 text-green-500"></i> Email
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus class="form-control">
                        @error('email')
                            <span class="flex items-center gap-1.5 text-sm font-medium text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="password" class="flex items-center gap-2 text-sm font-medium text-gray-800 dark:text-gray-200">
                            <i class="fas fa-lock w-4 text-green-500"></i> Senha
                        </label>
                        <div class="relative w-full">
                            <input type="password" id="password" name="password" required class="form-control pr-12">
                            <button type="button" class="absolute right-3 top-1/2 flex -translate-y-1/2 items-center justify-center text-gray-500 transition-colors hover:text-green-500 dark:text-gray-400" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="flex items-center gap-1.5 text-sm font-medium text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between gap-4 max-[768px]:flex-col max-[768px]:items-start">
                        <label class="flex cursor-pointer items-center gap-2 text-sm text-muted">
                            <input type="checkbox" name="remember" class="h-[18px] w-[18px] rounded border-gray-300 text-green-600 focus:ring-green-500">
                            Lembrar-me
                        </label>
                        <a href="#" class="text-sm font-medium text-green-600 hover:text-green-800 hover:underline dark:text-green-400 dark:hover:text-green-300">Esqueceu a senha?</a>
                    </div>

                    <button type="submit" class="btn btn-primary mt-2 w-full normal-case">
                        <i class="fas fa-sign-in-alt"></i>
                        Entrar
                    </button>

                    <div class="mt-2 border-t border-gray-100 pt-5 text-center dark:border-gray-700">
                        <p class="text-sm text-muted">Não tem uma conta? <a href="{{ route('register') }}" class="font-medium text-green-600 hover:text-green-800 hover:underline dark:text-green-400 dark:hover:text-green-300">Cadastre-se aqui</a></p>
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
