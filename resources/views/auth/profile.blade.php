@extends('layouts.app')

@section('content')
<div class="recipe-form-page">
    <h1>Profila rediģēšana</h1>
    <p>Maini savu vārdu, e-pastu, profila attēlu un, ja vēlies, arī paroli.</p>

    <form method="POST" action="{{ route('profile.update') }}" class="recipe-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-field profile-image-field">
            <label for="profile_image">Profila bilde</label>

            <div class="profile-image-preview-wrap">
                @if ($user->profile_image)
                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" class="profile-image-preview">
                @else
                    <div class="profile-image-placeholder">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                @endif
            </div>

            <input id="profile_image" name="profile_image" type="file" accept="image/*">
        </div>

        <div class="form-field">
            <label for="name">Vārds</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required>
        </div>

        <div class="form-field">
            <label for="email">E-pasts</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
        </div>

        <div class="form-field">
            <label for="password">Jauna parole</label>
            <input id="password" name="password" type="password" autocomplete="new-password">
        </div>

        <div class="form-field">
            <label for="password_confirmation">Apstiprināt jauno paroli</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
        </div>

        <button type="submit" class="primary-action">Saglabāt</button>
    </form>
</div>
@endsection
