@extends('layouts.app')

@section('content')
<div>
    <p>Jauns ieraksts</p>
    <h1>Izveidot recepti</h1>
    <form method="POST" action="{{ route('recipes.store') }}">
        @include('recipes._form', ['submitLabel' => 'Saglabāt recepti'])
    </form>
</div>
@endsection
