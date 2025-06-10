<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Панель')</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" />
    <style>
        body, html {
            margin: 0; padding: 0; height: 100%;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }
        .wrapper {
            display: flex;
            height: 100vh;
            flex-direction: column;
        }
        header.topbar {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 0 1rem;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 1rem;
            font-size: 1rem;
        }
        header.topbar form {
            margin: 0;
        }
        .main-wrapper {
            flex: 1;
            display: flex;
            overflow: hidden;
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
        main.content > header {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        button.logout-btn {
            background: none;
            border: none;
            color: #ecf0f1;
            cursor: pointer;
            padding: 0;
            font: inherit;
        }

        .button-primary {
            background-color: #3490dc;
            color: white;
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .button-primary:hover {
            background-color: #2779bd;
        }

        .button-success {
            background-color: #38c172;
            color: white;
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .button-success:hover {
            background-color: #2fa360;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <header class="topbar">
        @if(Auth::check())
            <span>{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">Выйти</button>
            </form>
        @endif
    </header>

    <div class="main-wrapper">
        <nav class="sidebar">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Главная</a>
            <a href="{{ route('search-requests.index') }}" class="{{ request()->routeIs('search-requests') ? 'active' : '' }}">Поисковые запросы</a>
            <a href="{{ route('coefficients') }}" class="{{ request()->routeIs('coefficients') ? 'active' : '' }}">Коэффициенты</a>
        </nav>
        <main class="content">
            <header>@yield('title', 'Панель')</header>
            @yield('content')
        </main>
    </div>
</div>

<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
