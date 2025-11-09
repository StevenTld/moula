<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Moula') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/layout.css'])
    @stack('styles')

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
</head>
<body>
    <div id="app">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="container">
                <div class="navbar-brand">
                    <a href="{{ route('home') }}" class="brand-logo">
                        <span class="brand-name">Moula</span>
                    </a>
                </div>

                <div class="navbar-menu">
                    <a href="{{ route('home') }}" class="navbar-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span>Tableau de bord</span>
                    </a>
                    @if(Route::has('wallets.index'))
                    <a href="{{ route('wallets.index') }}" class="navbar-link {{ request()->routeIs('wallets.*') ? 'active' : '' }}">
                        <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Mes Wallets</span>
                    </a>
                    @endif
                    <button id="wallet-connect-btn" class="navbar-link navbar-link-special">
                        <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        <span>WalletConnect</span>
                    </button>
                    <div id="wallet-address-display" class="navbar-link hidden" style="cursor: default; font-size: 0.9em;"></div>
                    <div id="wallet-network-display" class="navbar-link hidden" style="cursor: default; font-size: 0.85em; color: #3b82f6;"></div>
                    <button id="wallet-disconnect-btn" class="navbar-link hidden" style="color: #ef4444;">
                        <svg class="navbar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Disconnect</span>
                    </button>
                </div>

                <div class="navbar-user">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-logout">Déconnexion</button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <p>&copy; {{ date('Y') }} Moula. Tous droits réservés.</p>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
