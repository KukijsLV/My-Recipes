@extends('layouts.app')

@section('content')
<div>
    <h1>Izveidot kontu</h1>
    <p>Sāc veidot savu recepšu kolekciju.</p>
    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <div>
            <label for="name">Vārds</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus>
        </div>
        <div>
            <label for="email">E-pasts</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required>
        </div>
        <div>
            <label for="password">Parole</label>
            <input id="password" name="password" type="password" required>
        </div>
        <div>
            <label for="password_confirmation">Atkārtota parole</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required>
        </div>
        <button type="submit">Reģistrēties</button>
    </form>
</div>
@endsection
