@extends('layouts.app')

@section('content')
<div class="auth-page">
    <p class="form-eyebrow">Prieks tevi atkal redzēt</p>
    <h1>Ienākt</h1>
    <p class="auth-intro">Turpini papildināt savu recepšu kolekciju.</p>
    <form class="auth-form" method="POST" action="{{ route('login.store') }}">
        @csrf
        <div>
            <label for="email">E-pasts</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
        </div>
        <div>
            <label for="password">Parole</label>
            <input id="password" name="password" type="password" required>
        </div>
        <label><input type="checkbox" name="remember" value="1"> Atcerēties mani</label>
        <button type="submit">Ienākt</button>
    </form>
</div>
@endsection
