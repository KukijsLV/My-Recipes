@extends('layouts.app')

@section('content')
<div>
    <p>Rediģēšana</p>
    <h1>{{ $recipe->title }}</h1>
    <form method="POST" action="{{ route('recipes.update', $recipe) }}">
        @method('PUT')
        @include('recipes._form', ['submitLabel' => 'Saglabāt izmaiņas'])
    </form>
</div>
@endsection
