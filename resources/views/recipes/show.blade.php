@extends('layouts.app')

@section('content')
<article>
    <div>
        <div>
            <p>{{ $recipe->visibility === 'public' ? 'Publiska recepte' : 'Privāta recepte' }}</p>
            <h1>{{ $recipe->title }}</h1>
            <p>{{ $recipe->description }}</p>
        </div>
        @can('update', $recipe)
            <a href="{{ route('recipes.edit', $recipe) }}">Rediģēt</a>
        @endcan
    </div>
    @if ($recipe->image)
        <img src="{{ $recipe->image }}" alt="{{ $recipe->title }}">
    @endif
    <div>
        <section>
            <h2>Sastāvdaļas</h2>
            <div>{{ $recipe->ingredients }}</div>
        </section>
        <section>
            <h2>Pagatavošana</h2>
            <div>{{ $recipe->instructions }}</div>
        </section>
    </div>
    @can('delete', $recipe)
        <form method="POST" action="{{ route('recipes.destroy', $recipe) }}" onsubmit="return confirm('Vai tiešām dzēst šo recepti?')">
            @csrf
            @method('DELETE')
            <button type="submit">Dzēst recepti</button>
        </form>
    @endcan
</article>
@endsection
