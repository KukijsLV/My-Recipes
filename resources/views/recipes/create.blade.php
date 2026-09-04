@extends('layouts.app')

@section('content')
<div class="recipe-form-page">
    <p class="form-eyebrow">Jauns ieraksts</p>
    <h1>Izveidot recepti</h1>
    <form class="recipe-form" method="POST" action="{{ route('recipes.store') }}">
        @include('recipes._form', ['submitLabel' => 'Saglabāt recepti'])
    </form>
</div>
@endsection
