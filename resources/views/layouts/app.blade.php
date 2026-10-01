<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'My Recipes' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <nav class="side-nav">
                <a class="brand" href="{{ route('recipes.index') }}">My Recipes</a>

                <div class="nav-actions">
                    @auth
                        <a href="{{ route('profile') }}" class="user-name">{{ auth()->user()->name }}</a>
                        <a href="{{ route('recipes.index') }}">Receptes</a>
                        <a href="{{ route('recipes.create') }}">Jauna recepte</a>
                        <a href="{{ route('contact') }}">Kontakti</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Iziet</button>
                        </form>
                    @else
                        <a href="{{ route('recipes.index') }}">Receptes</a>
                        <a href="{{ route('login') }}">Ienākt</a>
                        <a href="{{ route('register') }}">Reģistrēties</a>
                        <a href="{{ route('contact') }}">Kontakti</a>
                    @endauth
                </div>
            </nav>
        </aside>

        <main class="page-shell">
        @if (session('status'))
            <div class="flash-message" role="status" aria-live="polite">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="validation-errors" role="alert">
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
