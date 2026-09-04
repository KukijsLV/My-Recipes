<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'My Recipes' }}</title>
</head>
<body>
    <header>
        <nav>
            <a href="{{ auth()->check() ? route('recipes.index') : url('/') }}">My Recipes</a>
            <div>
                @auth
                    <span>{{ auth()->user()->name }}</span>
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
    </header>
    <main>
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
</body>
</html>
