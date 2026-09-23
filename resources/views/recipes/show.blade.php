@extends('layouts.app')

@section('content')
<article class="recipe-show" style="border-left: 6px solid {{ $recipe->color ?? '#5f7f6d' }}; padding-left: 18px;">
    <div class="recipe-show-header">
        <div>
            <p>{{ $recipe->visibility === 'public' ? 'Publiska recepte' : 'Privāta recepte' }}</p>
            <h1>{{ $recipe->title }}</h1>
            <p class="recipe-author-inline">Autors: <a href="{{ route('user.profile', $recipe->user) }}">{{ $recipe->user->name }}</a></p>
            <p class="recipe-description">{{ $recipe->description }}</p>
        </div>
        @can('update', $recipe)
            <div class="recipe-show-actions">
                <a class="button secondary" href="{{ route('recipes.edit', $recipe) }}">Rediģēt</a>
            </div>
        @endcan
    </div>
    @if ($recipe->image)
        <img src="{{ $recipe->image }}" alt="{{ $recipe->title }}">
    @endif
    <div class="recipe-show-grid">
        <section class="recipe-show-section">
            <h2>Sastāvdaļas</h2>
            @php
                $ingredientLines = array_values(array_filter(array_map('trim', preg_split('/\R+/', trim((string) $recipe->ingredients))), fn ($ingredient) => $ingredient !== ''));
            @endphp
            @if ($ingredientLines)
                <ol>
                    @foreach ($ingredientLines as $ingredient)
                        <li>{{ $ingredient }}</li>
                    @endforeach
                </ol>
            @else
                <p>Nav sastāvdaļu.</p>
            @endif
        </section>
        <section class="recipe-show-section">
            <h2>Pagatavošana</h2>
            <div>{{ $recipe->instructions }}</div>
        </section>
    </div>
    @can('delete', $recipe)
        <form method="POST" action="{{ route('recipes.destroy', $recipe) }}" onsubmit="return confirm('Vai tiešām dzēst šo recepti?')">
            @csrf
            @method('DELETE')
            <button class="button" type="submit">Dzēst recepti</button>
        </form>
    @endcan
</article>
@endsection
