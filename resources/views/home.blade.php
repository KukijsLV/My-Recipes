@extends('layouts.app')

@section('content')
<div class="home-page">
    <section class="home-hero">
        <div class="home-copy">
            <p class="form-eyebrow">Mana virtuve</p>
            <h1>Glabā, sakārto un dalies ar savām receptēm.</h1>
            <p class="home-subtitle">Izveido individuālu receptu kolekciju, atrodi gatavotus ēdienus ātri un turpini ēst ar prieku.</p>

            <div class="home-actions">
                @guest
                    <a class="button" href="{{ route('register') }}">Sākt tūlīt</a>
                    <a class="button secondary" href="{{ route('login') }}">Ienākt</a>
                @else
                    <a class="button" href="{{ route('recipes.index') }}">Manas receptes</a>
                    <a class="button secondary" href="{{ route('recipes.create') }}">Pievienot recepti</a>
                @endguest
            </div>
        </div>

        <div class="home-panel">
            <div class="mini-card">
                <span>Populārākās</span>
                <strong>24 receptes</strong>
            </div>
            <div class="mini-card accent">
                <span>Šodienas plāns</span>
                <strong>3 receptes</strong>
            </div>
            <div class="mini-card soft">
                <span>Vietējie favorīti</span>
                <strong>8 izlasīti</strong>
            </div>
        </div>
    </section>
</div>
@endsection