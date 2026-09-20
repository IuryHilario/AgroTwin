<!DOCTYPE html>
<html lang="pt-BR">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'AgroTwin')</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Tema (aplica .dark antes do primeiro paint, evitando flash) -->
        <script>
            (function () {
                const stored = localStorage.getItem('theme');
                const isDark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('dark', isDark);
            })();

            window.toggleTheme = function () {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            };
        </script>

        <!-- Sidebar recolhida (desktop): aplica antes do primeiro paint, evitando salto de layout -->
        <script>
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                document.documentElement.classList.add('sidebar-collapsed');
            }

            window.toggleSidebar = function () {
                // No mobile o botão controla o drawer (via Alpine), não o modo recolhido
                if (window.innerWidth < 1024) {
                    return;
                }

                const collapsed = document.documentElement.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', collapsed);
            };
        </script>

        <!-- Fonts — Inter para a interface, IBM Plex Mono para leituras de sensor -->
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Icons -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

        <!-- CSS -->
        @vite(['resources/css/app.css'])
        @vite(['resources/js/app.js'])
        @stack('styles')

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Alpine.js para interatividade -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>

    <body
        class="overflow-x-hidden bg-gradient-to-br from-gray-50 to-gray-100 font-sans text-gray-800 dark:from-gray-900 dark:to-gray-950 dark:text-gray-100"
        x-data="sidebarNav()">
        <!-- Navbar -->
        <nav class="fixed inset-x-0 top-0 z-[1000] h-[70px] border-b border-gray-200 bg-gradient-to-br from-white to-gray-50 shadow-[0_2px_10px_rgba(0,0,0,0.08)] backdrop-blur-sm max-md:h-[60px] dark:border-gray-800 dark:from-gray-900 dark:to-gray-900">
            <div class="flex h-full w-full max-w-full items-center justify-between px-8 max-md:px-4">
                <div class="flex items-center gap-5">
                    <button @click="sidebarOpen = !sidebarOpen" onclick="toggleSidebar()" title="Mostrar/ocultar menu" class="rounded-xl bg-green-500/10 p-3 text-lg text-green-500 shadow-[0_2px_4px_rgba(34,197,94,0.1)] transition-all duration-300 hover:-translate-y-px hover:bg-green-500/15 hover:shadow-[0_4px_8px_rgba(34,197,94,0.2)]">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="flex items-center gap-3 text-[22px] font-bold text-green-500 max-md:text-lg">
                        <i class="fas fa-seedling text-[28px] drop-shadow-[0_2px_4px_rgba(34,197,94,0.3)] max-md:text-2xl"></i>
                        <span><a href="/dashboard">AgroTwin</a></span>
                    </div>
                </div>
                <div class="ml-auto flex items-center gap-3 max-md:gap-2 md:gap-6">
                    <button onclick="toggleTheme()" title="Alternar tema" class="flex-shrink-0 rounded-xl bg-gray-500/10 p-3 text-lg text-gray-600 transition-all duration-300 hover:-translate-y-px hover:bg-gray-500/15 dark:text-gray-300">
                        <i class="fas fa-moon theme-icon-moon"></i>
                        <i class="fas fa-sun theme-icon-sun"></i>
                    </button>
                    <div class="group relative flex cursor-pointer items-center gap-3 rounded-xl px-2 py-2 transition-all duration-300 hover:bg-gray-500/5 md:px-4" title="{{ Auth::user()->name }}">
                        <div class="flex h-[42px] w-[42px] flex-shrink-0 items-center justify-center rounded-full border-2 border-white/20 bg-gradient-to-br from-green-500 to-green-600 text-white shadow-[0_4px_12px_rgba(34,197,94,0.3)]">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="invisible absolute right-0 top-[60px] z-[1001] min-w-[200px] -translate-y-2 overflow-hidden rounded-2xl border border-gray-200 bg-white opacity-0 shadow-2xl transition-all duration-300 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 dark:border-gray-700 dark:bg-gray-800">
                            <x-ui.dropdown-item :route="route('perfil.edit')" icon="fas fa-user-circle" label="Perfil" />
                            <x-ui.dropdown-item :route="route('configuracoes.edit')" icon="fas fa-cog" label="Configurações" />
                            <x-ui.dropdown-item :route="route('logout')" icon="fas fa-sign-out-alt" label="Sair" :divider="true"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();" />
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        <aside
            class="sidebar fixed left-0 top-[70px] z-[999] h-[calc(100vh-70px)] w-[280px] -translate-x-full overflow-y-auto overflow-x-hidden border-r border-gray-200 bg-gradient-to-b from-white to-gray-50 shadow-[2px_0_20px_rgba(0,0,0,0.08)] transition-all duration-300 ease-out lg:translate-x-0 max-md:top-[60px] max-md:h-[calc(100vh-60px)] dark:border-gray-800 dark:from-gray-900 dark:to-gray-900"
            :class="{ 'translate-x-0': sidebarOpen }"
        >
            @php
                $menuItems = [
                    ['route' => route('dashboard'), 'active' => 'dashboard', 'icon' => 'fas fa-tachometer-alt', 'label' => 'Dashboard'],
                    ['route' => route('propriedade.index'), 'active' => 'propriedade.*', 'icon' => 'fas fa-map-marked-alt', 'label' => 'Propriedades'],
                    ['route' => route('lavouras.index'), 'active' => 'lavouras.*', 'icon' => 'fas fa-seedling', 'label' => 'Lavouras'],
                    ['route' => route('insumos.index'), 'active' => 'insumos.*', 'icon' => 'fas fa-flask', 'label' => 'Insumos'],
                    ['route' => route('sensores.index'), 'active' => 'sensores.*', 'icon' => 'fas fa-satellite-dish', 'label' => 'Sensores'],
                    ['route' => route('recomendacoes.index'), 'active' => 'recomendacoes.*', 'icon' => 'fas fa-brain', 'label' => 'Recomendações'],
                    ['route' => route('alertas.index'), 'active' => 'alertas.*', 'icon' => 'fas fa-bell', 'label' => 'Alertas'],
                    ['route' => route('relatorios.index'), 'active' => 'relatorios.*', 'icon' => 'fas fa-chart-bar', 'label' => 'Relatórios'],
                ];
            @endphp

            <div class="py-6">
                <ul class="list-none px-4">
                    @foreach ($menuItems as $item)
                        <x-ui.menu-item :route="$item['route']" :active="$item['active']" :icon="$item['icon']" :label="$item['label']" />
                    @endforeach
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content ml-0 mt-[60px] min-h-[calc(100vh-70px)] bg-gradient-to-br from-gray-50 to-gray-100 p-5 transition-all duration-300 ease-out md:mt-8 md:p-6 lg:ml-[250px] lg:p-8 dark:from-gray-900 dark:to-gray-900">
            @yield('content')
        </main>

        <!-- Overlay para mobile -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-gradient-to-br from-slate-900/40 to-slate-800/30 backdrop-blur-[2px] lg:hidden"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        </div>

        {{--
            Mensagens de retorno (session flash) das ações por link — ligar/parar
            irrigação, marcar alerta, acesso negado. Formulários AJAX não caem
            aqui: o fetch segue o redirect e consome a mensagem, e o modal.js
            mostra o próprio resultado.
        --}}
        @php
            $mensagens = collect([
                'success' => ['tom' => 'ok', 'icone' => 'fa-circle-check'],
                'info' => ['tom' => 'info', 'icone' => 'fa-circle-info'],
                'error' => ['tom' => 'erro', 'icone' => 'fa-circle-exclamation'],
            ])->filter(fn ($_, $tipo) => session()->has($tipo));
        @endphp
        @if ($mensagens->isNotEmpty())
            <div class="pointer-events-none fixed inset-x-4 top-20 z-[1200] flex flex-col items-end gap-2 sm:left-auto sm:right-6">
                @foreach ($mensagens as $tipo => $estilo)
                    <div x-data="{ aberto: true }" x-init="setTimeout(() => aberto = false, 5000)" x-show="aberto"
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-y-[-8px] opacity-0"
                         x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0"
                         role="status"
                         class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl border border-subtle bg-white p-4 shadow-lg dark:bg-gray-800">
                        <i class="fas {{ $estilo['icone'] }} mt-0.5 {{ ['ok' => 'text-emerald-500', 'info' => 'text-sky-500', 'erro' => 'text-rose-500'][$estilo['tom']] }}"></i>
                        <p class="flex-1 text-sm text-heading">{{ session($tipo) }}</p>
                        <button type="button" @click="aberto = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" aria-label="Fechar">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Form de logout (hidden) -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>

        <!-- Scripts -->
        @vite(['resources/js/modal.js'])
        @stack('scripts')
    </body>

</html>
