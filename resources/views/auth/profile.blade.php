@extends('layouts.app')

@section('content')
<div class="profile-page">
    <div class="profile-header">
        <div class="profile-image-preview-wrap profile-header-image">
            @if ($user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" class="profile-image-preview">
            @else
                <div class="profile-image-placeholder">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            @endif
        </div>

        <div>
            <p class="form-eyebrow">{{ $isOwnProfile ? 'Mans profils' : 'Profils' }}</p>
            <h1>{{ $user->name }}</h1>
            <p class="profile-summary">{{ $recipes->count() }} receptes</p>
            @if (auth()->check() && auth()->user()->is_admin && ! $isOwnProfile)
                <form method="POST" action="{{ route('admin.users.toggle-block', $user) }}" style="margin-top: 1rem;">
                    @csrf
                    <button type="submit" class="button secondary">
                        {{ $user->is_blocked ? 'Atbloķēt lietotāju' : 'Bloķēt lietotāju' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if ($isOwnProfile)
        <div class="recipe-form-page">
            <h2>Profila rediģēšana</h2>
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
    @endif

    <div class="profile-recipe-section">
        <div class="recipes-heading profile-heading">
            <p>{{ $isOwnProfile ? 'Mana virtuve' : 'Receptes' }}</p>
            <h2>{{ $isOwnProfile ? 'Pievienotās receptes' : $user->name . ' receptes' }}</h2>
        </div>

        @if ($recipes->isEmpty())
            <div class="empty-state">
                <h2>Vēl nav pievienotu receptju.</h2>
                <p>{{ $isOwnProfile ? 'Pievieno savu pirmo recepti un dalies ar to ar citiem.' : 'Šis lietotājs vēl nav pievienojis receptes.' }}</p>
            </div>
        @else
            <div class="recipe-list">
                @foreach ($recipes as $recipe)
                    <article class="recipe-card">
                        <div class="recipe-card-heading">
                            <h2><a href="{{ route('recipes.show', $recipe) }}">{{ $recipe->title }}</a></h2>
                            <span class="visibility">{{ $recipe->visibility === 'public' ? 'Publiska' : 'Privāta' }}</span>
                        </div>
                        <p class="recipe-author">Autors: <a href="{{ route('user.profile', $recipe->user) }}">{{ $recipe->user->name }}</a></p>
                        <p>{{ $recipe->description ?: $recipe->ingredients }}</p>
                        <div class="recipe-actions">
                            <a href="{{ route('recipes.show', $recipe) }}">Apskatīt</a>
                            @if ($isOwnProfile)
                                <a href="{{ route('recipes.edit', $recipe) }}">Rediģēt</a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
