@extends('layouts.app')

@section('content')
<div class="recipe-form-page">
    <p class="form-eyebrow">Sazinies ar mums</p>
    <h1>Kontakti</h1>
    <p>Nosūti jautājumu vai ieteikumu administratoram.</p>

    <form class="recipe-form" method="POST" action="{{ route('contact.send') }}">
        @csrf
        <div class="form-field">
            <label for="name">Vārds</label>
            <input id="name" name="name" type="text" value="{{ old('name', auth()->user()?->name) }}" maxlength="255" autocomplete="name" required>
        </div>

        <div class="form-field">
            <label for="email">E-pasts</label>
            <input id="email" name="email" type="email" value="{{ old('email', auth()->user()?->email) }}" maxlength="255" autocomplete="email" required>
        </div>

        <div class="form-field">
            <label for="subject">Temats</label>
            <input id="subject" name="subject" type="text" value="{{ old('subject') }}" maxlength="150" required>
        </div>

        <div class="form-field">
            <label for="message">Ziņa</label>
            <textarea id="message" name="message" maxlength="5000" required>{{ old('message') }}</textarea>
        </div>

        <button class="primary-action" type="submit">Nosūtīt ziņu</button>
    </form>
</div>
@endsection