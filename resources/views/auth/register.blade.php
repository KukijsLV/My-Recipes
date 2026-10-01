@extends('layouts.app')

@section('content')
<div class="auth-page">
    <p class="form-eyebrow">Tava virtuve sākas šeit</p>
    <h1>Izveidot kontu</h1>
    <p class="auth-intro">Saglabā iecienītās receptes vienuviet.</p>
    <form class="auth-form" method="POST" action="{{ route('register.store') }}">
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
