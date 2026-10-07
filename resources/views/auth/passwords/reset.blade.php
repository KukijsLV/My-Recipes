@extends('layouts.app')

@section('content')
<div class="auth-page">
    <p class="form-eyebrow">Atjaunot parole</p>
    <h1>Ievadiet jauno paroli</h1>
    <form class="auth-form" method="POST" action="{{ route('password.update', $token) }}">
        @csrf
        <div>
            <label for="password">Jauna parole</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required>
        </div>
        <div>
            <label for="password_confirmation">Atkārtota parole</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
        </div>
        <button type="submit">Atjaunot parole</button>
    </form>
</div>
@endsection
