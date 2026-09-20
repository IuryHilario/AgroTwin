<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'AgroTwin')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    {{-- Tema (aplica .dark antes do primeiro paint, evitando flash) --}}
    <script>
        (function () {
            const stored = localStorage.getItem('theme');
            const isDark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', isDark);
        })();
    </script>

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-gray-50 font-sans dark:bg-gray-950">
    <div class="min-h-screen lg:grid lg:grid-cols-[1.05fr_1fr]">

        {{-- ====== Painel da estação: identidade do produto ====== --}}
        <aside class="painel-estacao flex flex-col justify-between rounded-none p-8 lg:p-12">
            <div>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-400/15 ring-1 ring-emerald-400/30">
                        <i class="fas fa-seedling text-xl text-emerald-400"></i>
                    </span>
                    <span class="text-xl font-semibold tracking-tight text-white">AgroTwin</span>
                </a>

                <div class="entrada mt-10 hidden lg:block" style="animation-delay: 80ms">
                    <p class="font-readout text-xs uppercase tracking-[0.22em] text-emerald-400/80">
                        Monitoramento de solo · IoT
                    </p>
                    <h2 class="mt-3 max-w-md text-3xl font-semibold leading-tight text-white xl:text-4xl">
                        O solo da sua lavoura, medido de hora em hora.
                    </h2>
                    <p class="mt-4 max-w-md leading-relaxed text-white/55">
                        Sensores no campo enviam as leituras para o sistema, que avalia cada parâmetro,
                        aciona a irrigação e avisa quando algo sai da faixa ideal.
                    </p>
                </div>
            </div>

            {{-- Parâmetros medidos: reforça o que o sistema faz, sem fingir dado ao vivo --}}
            <div class="entrada mt-10 hidden lg:block" style="animation-delay: 200ms">
                <p class="font-readout mb-3 text-[11px] uppercase tracking-[0.18em] text-white/35">
                    Parâmetros monitorados
                </p>
                <div class="flex flex-wrap gap-2">
                    @foreach (['Umidade', 'pH', 'Temperatura', 'Condutividade', 'Nitrogênio', 'Fósforo', 'Potássio'] as $parametro)
                        <span class="font-readout rounded-md border border-white/10 bg-white/5 px-2.5 py-1 text-xs text-white/70">
                            {{ $parametro }}
                        </span>
                    @endforeach
                </div>

                <div class="font-readout mt-8 flex items-center gap-2 text-[11px] uppercase tracking-wider text-white/30">
                    <i class="fas fa-microchip"></i>
                    <span>Sensor 7-em-1</span>
                    <i class="fas fa-arrow-right-long text-[9px]"></i>
                    <span>ESP32</span>
                    <i class="fas fa-arrow-right-long text-[9px]"></i>
                    <span>AgroTwin</span>
                </div>
            </div>
        </aside>

        {{-- ====== Formulário ====== --}}
        <main class="flex items-center justify-center px-5 py-10 sm:px-10">
            <div class="w-full max-w-[420px]">
                <header class="entrada mb-8">
                    <h1 class="text-2xl font-semibold text-heading">@yield('cabecalho')</h1>
                    <p class="mt-1.5 text-sm text-muted">@yield('subtitulo')</p>
                </header>

                @if (session('success'))
                    <div class="entrada mb-6 flex items-start gap-2.5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-300">
                        <i class="fas fa-circle-check mt-0.5"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @yield('formulario')

                <p class="entrada mt-8 text-center text-sm text-muted" style="animation-delay: 240ms">
                    @yield('rodape')
                </p>
            </div>
        </main>
    </div>

    <script>
        // Mostrar/ocultar senha — delegado, serve para qualquer quantidade de
        // campos na tela (login tem 1, cadastro e redefinição têm 2).
        document.addEventListener('click', function (evento) {
            const botao = evento.target.closest('[data-alternar-senha]');
            if (!botao) return;

            const campo = document.getElementById(botao.dataset.alternarSenha);
            const icone = botao.querySelector('i');
            const oculto = campo.type === 'password';

            campo.type = oculto ? 'text' : 'password';
            icone.classList.toggle('fa-eye', !oculto);
            icone.classList.toggle('fa-eye-slash', oculto);
            botao.setAttribute('aria-label', oculto ? 'Ocultar senha' : 'Mostrar senha');
        });
    </script>
</body>

</html>
