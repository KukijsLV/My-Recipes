<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'My Recipes' }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <nav class="side-nav">
                <a class="brand" href="{{ auth()->check() ? route('recipes.index') : url('/') }}">My Recipes</a>

                <div class="nav-actions">
                    @auth
                        <span class="user-name">{{ auth()->user()->name }}</span>
                        <a href="{{ route('recipes.index') }}">Receptes</a>
                        <a href="{{ route('recipes.create') }}">Jauna recepte</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Iziet</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}">Ienākt</a>
                        <a href="{{ route('register') }}">Reģistrēties</a>
                    @endauth
                </div>
            </nav>
        </aside>

        <main class="page-shell">
        @if (session('status'))
            <div>{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
