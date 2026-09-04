@extends('layouts.app')

@section('content')
<h1>My Recipes</h1>
<p>Create and manage your recipes.</p>

<p>
    <a href="{{ route('login') }}">Login</a>
    <a href="{{ route('register') }}">Register</a>
</p>

@auth
    <p><a href="{{ route('recipes.index') }}">My recipes</a></p>
@endauth
@endsection