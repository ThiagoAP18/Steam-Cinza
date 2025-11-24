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
        <div class="logo">LOGO</div>
        </div>

        <div class="header-center">
        <label class="search-wrapper" for="search-input">
            <input id="search-input" class="search-input" type="search" placeholder="Busque pelos jogos de interesse" />
            <button class="search-btn" aria-label="Pesquisar">🔍</button>
        </label>
        </div>

        <div class="header-right">

        <button class="icon-btn notif-btn" aria-label="Notificações">🔔</button>

        <span class="notif-badge">0</span>

        <button class="icon-btn" aria-label="Perfil">👤</button>

        </div>
    </header>

    <div class="notif-panel">
        <h3>Notificações</h3>
        <div class="notif-list"></div>
    </div>
    @yield('content')

    <footer class="site-footer">
            <small>Prototipo — substituir logos e imagens</small>
    </footer>
</body>
</html>