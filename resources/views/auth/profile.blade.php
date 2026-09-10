@extends('layouts.app')

@section('content')
<div class="recipe-form-page">
    <h1>Profila rediģēšana</h1>
    <p>Maini savu vārdu un e-pastu.</p>

    <form method="POST" action="{{ route('profile.update') }}" class="recipe-form">
        @csrf
        @method('PUT')

        <div class="form-field">
            <label for="name">Vārds</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-field">
            <label for="email">E-pasts</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
        </div>

        <button type="submit">Saglabāt</button>
    </form>
</div>
@endsection
