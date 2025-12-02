@extends('layouts.main')

@section('title', 'Início')

@section('content')
<main class="main container">
    {{-- <section class="boxes-section">
        <h2 class="section-title small">Você tem caixas disponíveis</h2>
        <div class="boxes-row">
             
            
            <a href="{{ route('search', ['box' => 1]) }}" class="box">
                <span class="box-label">CAIXA 1</span>
            </a>
            
            
        </div>
    </section>--}}

    <section class="related-section">
        <h2 class="section-title">Jogos em Destaque</h2>

        <div class="cards-grid">
            @foreach($games as $game)
                <a href="/games/{{$game->id}}" class="card pill">
                    <div class="card-image" aria-hidden="true">
                        <img src="/img/games/{{$game->image}}" alt="{{ $game->name_game }}">
                    </div>
                    
                    <h3 class="card-title">{{$game->name_game}}</h3>
                    
                    <div class="card-price">
                        @if($minBuyPrice) <span class="price-value">Compra: R$ {{ number_format($minBuyPrice, 2, ',', '.') }}</span>
                        @else
                            <span class="unavailable">Indisponível para compra</span>
                        @endif

                        @if($minRentPrice)
                            <span class="rent-value">Aluguel: R$ {{ number_format($minRentPrice, 2, ',', '.') }}</span>
                        @else
                            <span class="unavailable">Indisponível para aluguel</span>
                        @endif
                    </div>
                </a>
            @endforeach 
        </div>
    </section>
</main>
@endsection