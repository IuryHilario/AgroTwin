<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Redefinir senha - AgroTwin</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

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

        <div class="relative z-10 w-full max-w-[450px]">
            <div class="animate-slide-up rounded-[20px] border border-white/20 bg-white/95 p-10 shadow-[0_25px_45px_rgba(0,0,0,0.1)] backdrop-blur-md max-[480px]:p-6 dark:border-gray-700/50 dark:bg-gray-900/95">
                <div class="mb-9 text-center">
                    <div class="mb-4 flex items-center justify-center gap-4 max-[480px]:flex-col max-[480px]:gap-2.5">
                        <i class="fas fa-lock animate-logo-pulse text-5xl text-green-500 max-[480px]:text-4xl"></i>
                        <h1 class="bg-gradient-to-br from-green-500 to-green-800 bg-clip-text text-3xl font-bold text-transparent max-[480px]:text-2xl dark:from-green-400 dark:to-green-600">Redefinir senha</h1>
                    </div>
                    <p class="text-sm text-muted">Escolha uma nova senha para sua conta.</p>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-5" autocomplete="off">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="flex flex-col gap-2">
                        <label for="email" class="flex items-center gap-2 text-sm font-medium text-gray-800 dark:text-gray-200">
                            <i class="fas fa-envelope w-4 text-green-500"></i> Email
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required autofocus class="form-control">
                        @error('email')
                            <span class="flex items-center gap-1.5 text-sm font-medium text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="password" class="flex items-center gap-2 text-sm font-medium text-gray-800 dark:text-gray-200">
                            <i class="fas fa-lock w-4 text-green-500"></i> Nova senha
                        </label>
                        <input type="password" id="password" name="password" required class="form-control">
                        @error('password')
                            <span class="flex items-center gap-1.5 text-sm font-medium text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="password_confirmation" class="flex items-center gap-2 text-sm font-medium text-gray-800 dark:text-gray-200">
                            <i class="fas fa-lock w-4 text-green-500"></i> Confirmar nova senha
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary mt-2 w-full normal-case">
                        <i class="fas fa-check"></i>
                        Redefinir senha
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
