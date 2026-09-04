@extends('layouts.app')

@section('content')
<div class="recipes-page">
    <div class="recipes-heading">
        <p>Mana virtuve</p>
        <h1>Manas receptes</h1>
    </div>
    <a class="button" href="{{ route('recipes.create') }}">+ Pievienot</a>
</div>
@if ($recipes->isEmpty())
    <div class="empty-state">
        <h2>Vēl nav nevienas receptes</h2>
        <p>Pirmā recepte sākas ar vienu labu ideju.</p>
        <a class="button" href="{{ route('recipes.create') }}">Izveidot recepti</a>
    </div>
@else
    <div class="recipe-list">
        @foreach ($recipes as $recipe)
            <article class="recipe-card">
                <div class="recipe-card-heading">
                    <h2><a href="{{ route('recipes.show', $recipe) }}">{{ $recipe->title }}</a></h2>
                    <span class="visibility">{{ $recipe->visibility === 'public' ? 'Publiska' : 'Privāta' }}</span>
                </div>
                <p>{{ $recipe->description ?: $recipe->ingredients }}</p>
                <div class="recipe-actions">
                    <a href="{{ route('recipes.show', $recipe) }}">Apskatīt</a>
                    <a href="{{ route('recipes.edit', $recipe) }}">Rediģēt</a>
                </div>
            </article>
        @endforeach
    </div>
@endif
@endsection
