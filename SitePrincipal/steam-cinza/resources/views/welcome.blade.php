@extends('layouts.main') {{-- Assumindo que você criou um layout --}}

@section('title', 'Início - GameMarket')

@section('content')
<main class="main container">
    <section class="boxes-section">
        <h2 class="section-title small">Você tem caixas disponíveis</h2>
        <div class="boxes-row">
            {{-- 
            
            <a href="{{ route('search', ['box' => 1]) }}" class="box">
                <span class="box-label">CAIXA 1</span>
            </a>
            
            --}}
        </div>
    </section>

    {{-- Seção de Produtos --}}
    <section class="related-section">
        <h2 class="section-title">Relacionados a produtos de interesse</h2>

        <div class="cards-grid">
            {{-- @foreach($games as $game) --}}
                <a href="{{-- route('product.show', $game->id) --}}" class="card pill">
                    <div class="card-image" aria-hidden="true">
                        {{-- <img src="{{ $game->image_url }}" alt="{{ $game->name }}"> --}}
                    </div>
                    <h3 class="card-title">{{-- $game->name --}} Nome do Jogo (Exemplo)</h3>
                    <p class="card-price">R$ 99,90</p>
                </a> 
                {{-- Card Estático de Exemplo (pode apagar quando colocar o PHP) --}}
                <article class="card pill">
                    <div class="card-image"></div>
                    <h3 class="card-title">Exemplo Estático</h3>
                </article>
            {{-- @endforeach --}}
        </div>
    </section>
</main>
@endsection