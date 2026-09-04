@extends('layouts.app')

@section('content')
<div class="recipe-form-page">
    <p class="form-eyebrow">Rediģēšana</p>
    <h1>{{ $recipe->title }}</h1>
    <form class="recipe-form" method="POST" action="{{ route('recipes.update', $recipe) }}">
        @method('PUT')
        @include('recipes._form', ['submitLabel' => 'Saglabāt izmaiņas'])
    </form>
</div>
@endsection
