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
                <div class="user-menu-container">
                    <button class="user-menu-trigger" onclick="toggleUserMenu()">
                        <span>👤</span>
                        <span class="arrow">▼</span>
                    </button>

                    <div class="user-dropdown" id="userDropdown">
                        <div class="user-name">Olá, {{ Auth::user()->name }}
                            <div class="cash">Saldo: R${{ Auth::user()->cash }}</div>
                        </div>
                        
                        @if(Auth::user()->type == "publisher")
                            <a href="/dashboard" class="dropdown-item">Meus Jogos Lançados</a>
                            <a href="/games/create" class="dropdown-item">Criar Jogo</a>
                        @else
                            <a href="/dashboard" class="dropdown-item">Meus Jogos</a>
                            <a href="/addfunds" class="dropdown-item">Adicionar Fundos</a>
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

    @if(session('msg'))
        @php
            $msgType = session('type', 'success');
            $alertClass = ($msgType === 'danger') ? 'flash-danger' : 'flash-success';
        @endphp

        <div id="flash-message" class="flash-message {{ $alertClass }}">
            <div class="flash-content">
                {!! session('msg') !!}
            </div>
            <button class="flash-close" onclick="closeFlash()">×</button>
            
            <div class="flash-progress"></div>
        </div>
    @endif

    <div class="container-fluid">
        <div class="row">
            @yield('content')
        </div>
    </div>

    <footer class="site-footer">
            <small>Prototipo — substituir logos e imagens</small>
    </footer>
</body>
</html>