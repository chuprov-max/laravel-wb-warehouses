<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Панель')</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <style>
        body, html {
            margin: 0; padding: 0; height: 100%;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }
        .wrapper {
            display: flex;
            height: 100vh;
        }
        nav.sidebar {
            width: 220px;
            background: #2c3e50;
            color: #ecf0f1;
            display: flex;
            flex-direction: column;
            padding: 1rem;
        }
        nav.sidebar a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 0.7rem 1rem;
            margin-bottom: 0.3rem;
            border-radius: 4px;
            display: block;
        }
        nav.sidebar a:hover, nav.sidebar a.active {
            background: #34495e;
        }
        main.content {
            flex: 1;
            padding: 2rem;
            background: white;
            overflow-y: auto;
        }
        header {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <nav class="sidebar">
        <h2>Меню</h2>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Главная</a>
        <a href="{{ route('coefficients') }}" class="{{ request()->routeIs('coefficients') ? 'active' : '' }}">Коэффициенты</a>
        <form method="POST" action="{{ route('logout') }}" style="margin-top:auto;">
            @csrf
            <button type="submit" style="background:none;border:none;color:#ecf0f1;cursor:pointer;padding:0;">Выйти</button>
        </form>
    </nav>
    <main class="content">
        <header>@yield('title', 'Панель')</header>
        @yield('content')
    </main>
</div>
<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
