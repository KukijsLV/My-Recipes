@extends('layouts.app')

@section('content')
<div class="home-page">
    <section class="home-hero">
        <div class="home-copy">
            <p class="form-eyebrow">Mana virtuve</p>
            <h1>Labas receptes pelnījušas savu vietu.</h1>
            <p class="home-subtitle">Saglabā ģimenes klasiku, atrodi ko jaunu un dalies ar ēdieniem, pie kuriem gribas atgriezties.</p>

            <div class="home-actions">
                @guest
                    <a class="button" href="{{ route('recipes.index') }}">Apskatīt receptes</a>
                    <a class="button secondary" href="{{ route('register') }}">Izveidot kontu</a>
                @else
                    <a class="button" href="{{ route('recipes.index') }}">Manas receptes</a>
                    <a class="button secondary" href="{{ route('recipes.create') }}">Pievienot recepti</a>
                @endguest
            </div>
        </div>

        <figure class="home-visual">
            <img src="https://images.unsplash.com/photo-1498837167922-ddd27525d352?auto=format&amp;fit=crop&amp;w=1400&amp;q=85" alt="Svaigi dārzeņi un maltītes sastāvdaļas uz galda">
            <figcaption>Gatavo, saglabā, nodod tālāk.</figcaption>
        </figure>
    </section>
</div>
@endsection