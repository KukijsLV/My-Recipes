@extends('layouts.app')

@section('content')
<div class="recipes-page">
    <div class="recipes-heading">
        <p>Brīvpieejamās receptes</p>
        <h1>Visas receptes</h1>
    </div>
    @auth
        <a class="button" href="{{ route('recipes.create') }}">+ Pievienot</a>
    @endauth
</div>

<form class="recipe-search" method="GET" action="{{ route('recipes.index') }}">
    <label class="sr-only" for="recipe-search">Meklēt receptes</label>
    <input id="recipe-search" name="search" type="search" value="{{ old('search', $search ?? '') }}" placeholder="Meklēt recepti, sastāvdaļas vai aprakstu...">

    <label class="sr-only" for="recipe-sort">Kārtot receptes</label>
    <select id="recipe-sort" name="sort">
        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Jaunākās pirmās</option>
        <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Vecākās pirmās</option>
    </select>

    <button class="button secondary" type="submit">Meklēt</button>
    @if ($search !== '' || $sort !== 'newest')
        <a class="search-clear" href="{{ route('recipes.index') }}">Notīrīt</a>
    @endif
</form>

@if ($recipes->isEmpty())
    <div class="empty-state">
        <h2>{{ $search !== '' ? 'Nav atrasta neviena recepte' : 'Vēl nav nevienas receptes' }}</h2>
        <p>{{ $search !== '' ? 'Mēģiniet meklēt citu nosaukumu vai sastāvdaļas.' : 'Pirmā recepte sākas ar vienu labu ideju.' }}</p>
        @auth
            <a class="button" href="{{ route('recipes.create') }}">{{ $search !== '' ? 'Izveidot jaunu recepti' : 'Izveidot recepti' }}</a>
        @endauth
    </div>
@else
    <div class="recipe-list">
        @foreach ($recipes as $recipe)
            <article class="recipe-card" style="border-left: 6px solid {{ $recipe->color ?? '#5f7f6d' }};">
                @if ($recipe->image)
                    <a href="{{ route('recipes.show', $recipe) }}" class="recipe-card-image-link">
                        <img src="{{ $recipe->image }}" alt="{{ $recipe->title }}" class="recipe-card-image">
                    </a>
                @endif
                <div class="recipe-card-heading">
                    <h2><a href="{{ route('recipes.show', $recipe) }}">{{ $recipe->title }}</a></h2>
                    <span class="visibility" style="background: {{ $recipe->color ?? '#5f7f6d' }}22; color: {{ $recipe->color ?? '#2a5144' }};">{{ $recipe->visibility === 'public' ? 'Publiska' : 'Privāta' }}</span>
                </div>
                <p class="recipe-author">Autors: <a href="{{ route('user.profile', $recipe->user) }}">{{ $recipe->user->name }}</a></p>
                <p>{{ $recipe->description ?: $recipe->ingredients }}</p>
                <div class="recipe-actions">
                    <a href="{{ route('recipes.show', $recipe) }}">Apskatīt</a>
                    @auth
                        @if (auth()->id() === $recipe->user_id)
                            <a href="{{ route('recipes.edit', $recipe) }}">Rediģēt</a>
                        @endif
                    @endauth
                </div>
            </article>
        @endforeach
    </div>
@endif
@endsection
