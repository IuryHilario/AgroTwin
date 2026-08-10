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

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
        x-data="{ sidebarOpen: false }"
        x-init="
        // Fechar sidebar ao redimensionar tela para desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                sidebarOpen = false;
            }
        });

        // Fechar sidebar ao clicar em links no mobile
        document.addEventListener('click', (e) => {
            if (e.target.closest('.sidebar .menu-link') && window.innerWidth < 1024) {
                setTimeout(() => { sidebarOpen = false; }, 100);
            }
        });
    ">
        <!-- Navbar -->
        <nav class="fixed inset-x-0 top-0 z-[1000] h-[70px] border-b border-gray-200 bg-gradient-to-br from-white to-gray-50 shadow-[0_2px_10px_rgba(0,0,0,0.08)] backdrop-blur-sm max-md:h-[60px] dark:border-gray-800 dark:from-gray-900 dark:to-gray-900">
            <div class="flex h-full w-full max-w-full items-center justify-between px-8 max-md:px-4">
                <div class="flex items-center gap-5">
                    <button @click="sidebarOpen = !sidebarOpen" onclick="toggleSidebar()" title="Mostrar/ocultar menu" class="rounded-xl bg-green-500/10 p-3 text-lg text-green-500 shadow-[0_2px_4px_rgba(34,197,94,0.1)] transition-all duration-300 hover:-translate-y-px hover:bg-green-500/15 hover:shadow-[0_4px_8px_rgba(34,197,94,0.2)]">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="flex items-center gap-3 text-[22px] font-bold text-green-500 max-md:text-lg">
                        <i class="fas fa-seedling text-[28px] drop-shadow-[0_2px_4px_rgba(34,197,94,0.3)] max-md:text-2xl"></i>
                        <span>AgroTwin</span>
                    </div>
                </div>
                <div class="ml-auto flex items-center gap-6">
                    <button onclick="toggleTheme()" title="Alternar tema" class="rounded-xl bg-gray-500/10 p-3 text-lg text-gray-600 transition-all duration-300 hover:-translate-y-px hover:bg-gray-500/15 dark:text-gray-300">
                        <i class="fas fa-moon theme-icon-moon"></i>
                        <i class="fas fa-sun theme-icon-sun"></i>
                    </button>
                    <div class="group relative flex cursor-pointer items-center gap-3 rounded-xl px-4 py-2 transition-all duration-300 hover:bg-gray-500/5">
                        <span class="text-[15px] font-semibold text-gray-700 dark:text-gray-200">{{ Auth::user()->name }}</span>
                        <div class="flex h-[42px] w-[42px] items-center justify-center rounded-full border-2 border-white/20 bg-gradient-to-br from-green-500 to-green-600 text-white shadow-[0_4px_12px_rgba(34,197,94,0.3)]">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="invisible absolute right-0 top-[60px] z-[1001] min-w-[200px] -translate-y-2 overflow-hidden rounded-2xl border border-gray-200 bg-white opacity-0 shadow-2xl transition-all duration-300 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 dark:border-gray-700 dark:bg-gray-800">
                            <a href="{{ route('perfil.edit') }}" class="flex items-center gap-3 px-[18px] py-3.5 font-medium text-gray-700 no-underline transition-all duration-200 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:text-green-500 dark:text-gray-200 dark:hover:from-gray-700 dark:hover:to-gray-700">
                                <i class="fas fa-user-circle"></i>
                                Perfil
                            </a>
                            <a href="{{ route('configuracoes.edit') }}" class="flex items-center gap-3 px-[18px] py-3.5 font-medium text-gray-700 no-underline transition-all duration-200 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:text-green-500 dark:text-gray-200 dark:hover:from-gray-700 dark:hover:to-gray-700">
                                <i class="fas fa-cog"></i>
                                Configurações
                            </a>
                            <div class="my-2 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent dark:via-gray-700"></div>
                            <a href="{{ route('logout') }}" class="flex items-center gap-3 px-[18px] py-3.5 font-medium text-gray-700 no-underline transition-all duration-200 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:text-green-500 dark:text-gray-200 dark:hover:from-gray-700 dark:hover:to-gray-700"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i>
                                Sair
                            </a>
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
            <div class="py-6">
                <ul class="list-none px-4">
                    <li class="mb-1.5">
                        <a href="{{ route('dashboard') }}" title="Dashboard" class="menu-link flex items-center gap-3.5 rounded-xl px-5 py-3.5 font-medium no-underline transition-all duration-300 hover:translate-x-1 {{ request()->routeIs('dashboard') ? 'border-l-4 border-green-600 bg-green-50 text-green-600 shadow-[0_4px_12px_rgba(34,197,94,0.2)] dark:border-green-500 dark:bg-green-900/30 dark:text-green-400' : 'text-gray-500 hover:bg-green-50 hover:text-green-600 hover:shadow-[0_4px_12px_rgba(34,197,94,0.15)] dark:text-gray-400 dark:hover:bg-green-900/20 dark:hover:text-green-400' }}">
                            <i class="fas fa-tachometer-alt w-5 text-center"></i>
                            <span class="menu-label">Dashboard</span>
                        </a>
                    </li>
                    <li class="mb-1.5">
                        <a href="{{ route('propriedade.index') }}" title="Propriedades" class="menu-link flex items-center gap-3.5 rounded-xl px-5 py-3.5 font-medium no-underline transition-all duration-300 hover:translate-x-1 {{ request()->routeIs('propriedade.*') ? 'border-l-4 border-green-600 bg-green-50 text-green-600 shadow-[0_4px_12px_rgba(34,197,94,0.2)] dark:border-green-500 dark:bg-green-900/30 dark:text-green-400' : 'text-gray-500 hover:bg-green-50 hover:text-green-600 hover:shadow-[0_4px_12px_rgba(34,197,94,0.15)] dark:text-gray-400 dark:hover:bg-green-900/20 dark:hover:text-green-400' }}">
                            <i class="fas fa-map-marked-alt w-5 text-center"></i>
                            <span class="menu-label">Propriedades</span>
                        </a>
                    </li>
                    <li class="mb-1.5">
                        <a href="{{ route('lavouras.index') }}" title="Lavouras" class="menu-link flex items-center gap-3.5 rounded-xl px-5 py-3.5 font-medium no-underline transition-all duration-300 hover:translate-x-1 {{ request()->routeIs('lavouras.*') ? 'border-l-4 border-green-600 bg-green-50 text-green-600 shadow-[0_4px_12px_rgba(34,197,94,0.2)] dark:border-green-500 dark:bg-green-900/30 dark:text-green-400' : 'text-gray-500 hover:bg-green-50 hover:text-green-600 hover:shadow-[0_4px_12px_rgba(34,197,94,0.15)] dark:text-gray-400 dark:hover:bg-green-900/20 dark:hover:text-green-400' }}">
                            <i class="fas fa-seedling w-5 text-center"></i>
                            <span class="menu-label">Lavouras</span>
                        </a>
                    </li>
                    <li class="mb-1.5">
                        <a href="{{ route('insumos.index') }}" title="Insumos" class="menu-link flex items-center gap-3.5 rounded-xl px-5 py-3.5 font-medium no-underline transition-all duration-300 hover:translate-x-1 {{ request()->routeIs('insumos.*') ? 'border-l-4 border-green-600 bg-green-50 text-green-600 shadow-[0_4px_12px_rgba(34,197,94,0.2)] dark:border-green-500 dark:bg-green-900/30 dark:text-green-400' : 'text-gray-500 hover:bg-green-50 hover:text-green-600 hover:shadow-[0_4px_12px_rgba(34,197,94,0.15)] dark:text-gray-400 dark:hover:bg-green-900/20 dark:hover:text-green-400' }}">
                            <i class="fas fa-flask w-5 text-center"></i>
                            <span class="menu-label">Insumos</span>
                        </a>
                    </li>
                    <li class="mb-1.5">
                        <a href="{{ route('sensores.index') }}" title="Sensores" class="menu-link flex items-center gap-3.5 rounded-xl px-5 py-3.5 font-medium no-underline transition-all duration-300 hover:translate-x-1 {{ request()->routeIs('sensores.*') ? 'border-l-4 border-green-600 bg-green-50 text-green-600 shadow-[0_4px_12px_rgba(34,197,94,0.2)] dark:border-green-500 dark:bg-green-900/30 dark:text-green-400' : 'text-gray-500 hover:bg-green-50 hover:text-green-600 hover:shadow-[0_4px_12px_rgba(34,197,94,0.15)] dark:text-gray-400 dark:hover:bg-green-900/20 dark:hover:text-green-400' }}">
                            <i class="fas fa-satellite-dish w-5 text-center"></i>
                            <span class="menu-label">Sensores</span>
                        </a>
                    </li>
                    <li class="mb-1.5">
                        <a href="{{ route('dashboard') }}#recomendacoes" title="Recomendações" class="menu-link flex items-center gap-3.5 rounded-xl px-5 py-3.5 font-medium text-gray-500 no-underline transition-all duration-300 hover:translate-x-1 hover:bg-green-50 hover:text-green-600 hover:shadow-[0_4px_12px_rgba(34,197,94,0.15)] dark:text-gray-400 dark:hover:bg-green-900/20 dark:hover:text-green-400">
                            <i class="fas fa-brain w-5 text-center"></i>
                            <span class="menu-label">Recomendações</span>
                        </a>
                    </li>
                    <li class="mb-1.5">
                        <a href="{{ route('alertas.index') }}" title="Alertas" class="menu-link flex items-center gap-3.5 rounded-xl px-5 py-3.5 font-medium no-underline transition-all duration-300 hover:translate-x-1 {{ request()->routeIs('alertas.*') ? 'border-l-4 border-green-600 bg-green-50 text-green-600 shadow-[0_4px_12px_rgba(34,197,94,0.2)] dark:border-green-500 dark:bg-green-900/30 dark:text-green-400' : 'text-gray-500 hover:bg-green-50 hover:text-green-600 hover:shadow-[0_4px_12px_rgba(34,197,94,0.15)] dark:text-gray-400 dark:hover:bg-green-900/20 dark:hover:text-green-400' }}">
                            <i class="fas fa-bell w-5 text-center"></i>
                            <span class="menu-label">Alertas</span>
                        </a>
                    </li>
                    <li class="mb-1.5">
                        <a href="#" title="Relatórios" class="menu-link flex items-center gap-3.5 rounded-xl px-5 py-3.5 font-medium text-gray-500 no-underline transition-all duration-300 hover:translate-x-1 hover:bg-green-50 hover:text-green-600 hover:shadow-[0_4px_12px_rgba(34,197,94,0.15)] dark:text-gray-400 dark:hover:bg-green-900/20 dark:hover:text-green-400">
                            <i class="fas fa-chart-bar w-5 text-center"></i>
                            <span class="menu-label">Relatórios</span>
                        </a>
                    </li>
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

        <!-- Form de logout (hidden) -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>

        <!-- Scripts -->
        @vite(['resources/js/modal.js'])
        @stack('scripts')
    </body>

</html>
