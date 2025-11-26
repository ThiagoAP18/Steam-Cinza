<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>@yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/main.js" defer></script>
</head>
<body>
    <header class="site-header">
        <div class="header-left">
            <a href="/" class="logo">Logo</a>
        </div>

        <div class="header-center">
            <form action="/search" method="GET">
                <label class="search-wrapper" for="search-input">
                    <input name ="search" id="search-input" class="search-input" type="search" placeholder="Busque pelos jogos de interesse" />
                    <button class="search-btn" aria-label="Pesquisar">🔍</button>
                </label>
            </form>
        </div>

        <div class="header-right">
            @auth
                @if(Auth::user()->type == "common")
                    <button class="icon-btn notif-btn" aria-label="Notificações">🔔</button>
                    <span class="notif-badge">0</span>
                @endif

                <div class="user-menu-container">
                    <button class="user-menu-trigger" onclick="toggleUserMenu()">
                        <span>👤</span>
                        <span class="arrow">▼</span>
                    </button>

                    <div class="user-dropdown" id="userDropdown">
                        <div class="user-name">Olá, {{ Auth::user()->name }}</div>
                        
                        @if(Auth::user()->type == "publisher")
                            <a href="{{ route('dashboard') }}" class="dropdown-item">Meus Jogos Lançados</a>
                        @else
                            <a href="{{ route('dashboard') }}" class="dropdown-item">Meus Jogos</a>
                        @endif
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item" style="color: red;">Sair</button>
                        </form>
                    </div>
                </div>
            @endauth

            @guest
                <a href="{{ route('login') }}" class="btn-login">Entrar</a>
                <a href="{{ route('register') }}" class="btn-register">Cadastrar</a>
            @endguest
        </div>
    </header>

    @auth
    <div class="notif-panel">
        <h3>Notificações</h3>
        <div class="notif-list"></div>
    </div>
    @endauth
    @yield('content')

    <footer class="site-footer">
            <small>Prototipo — substituir logos e imagens</small>
    </footer>
</body>
</html>