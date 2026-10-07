@extends('layouts.app')

@section('content')
<div class="auth-page">
    <p class="form-eyebrow">Atjaunot parole</p>
    <h1>Norāda parole atjaunarēšanai</h1>
    <p class="auth-intro">Ievadiet savu e-pastu, lai saņemotu drošu atjaunašanas norādi.</p>
    <form class="auth-form" method="POST" action="{{ route('password.email') }}">
        @csrf
        <div>
            <label for="email">E-pasts</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
        </div>
        <button type="submit">Nosūtīt norādi</button>
    </form>
</div>
@endsection
