<!DOCTYPE html>
<html lang="pt-BR">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'AgroTwin')</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Material Design Lite -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-lite/1.3.0/material.min.css">
        <script defer src="https://cdnjs.cloudflare.com/ajax/libs/material-design-lite/1.3.0/material.min.js"></script>

        <!-- Icons -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

        <!-- Bootstrap CSS and JS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


        <!-- CSS -->
        @vite(['resources/css/app.css'])
        @vite(['resources/css/uis-layouts.css'])
        @vite(['resources/css/forms-layouts.css'])
        @vite(['resources/js/app.js'])
        @stack('styles')

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Alpine.js para interatividade -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>

    <body x-data="{ sidebarOpen: false }" x-init="
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
        <nav class="navbar">
            <div class="navbar-content">
                <div class="navbar-left">
                    <button @click="sidebarOpen = !sidebarOpen" class="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="logo">
                        <i class="fas fa-seedling"></i>
                        <span>AgroTwin</span>
                    </div>
                </div>
                <div class="navbar-right">
                    <div class="user-menu">
                        <span class="user-name">{{ Auth::user()->name }}</span>
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="dropdown">
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-user-circle"></i>
                                Perfil
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-cog"></i>
                                Configurações
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('logout') }}" class="dropdown-item"
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
        <aside class="sidebar" :class="{ 'active': sidebarOpen }">
            <div class="sidebar-content">
                <ul class="sidebar-menu">
                    <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="menu-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('propriedade.*') ? 'active' : '' }}">
                        <a href="{{ route('propriedade.index') }}" class="menu-link">
                            <i class="fas fa-map-marked-alt"></i>
                            <span>Propriedades</span>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('lavouras.*') ? 'active' : '' }}">
                        <a href="{{ route('lavouras.index') }}" class="menu-link">
                            <i class="fas fa-seedling"></i>
                            <span>Lavouras</span>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('insumos.*') ? 'active' : '' }}">
                        <a href="{{ route('insumos.index') }}" class="menu-link">
                            <i class="fas fa-flask"></i>
                            <span>Insumos</span>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('sensores.*') ? 'active' : '' }}">
                        <a href="{{ route('sensores.index') }}" class="menu-link">
                            <i class="fas fa-satellite-dish"></i>
                            <span>Sensores</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <i class="fas fa-brain"></i>
                            <span>Recomendações IA</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <i class="fas fa-bell"></i>
                            <span>Alertas</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <i class="fas fa-chart-bar"></i>
                            <span>Relatórios</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>

        <!-- Overlay para mobile -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="mobile-overlay"
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
