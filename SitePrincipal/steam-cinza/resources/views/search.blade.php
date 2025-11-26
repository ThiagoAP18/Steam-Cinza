@extends('layouts.main')

@section('title', 'Pesquisando: '. $search)

@section('content')

<main class="container py-5">
    <section class="search-results">
        
        <div class="d-flex align-items-center mb-4">
            <h2 class="section-title">Resultados para: <span class="highlight">"{{$search}}"</span></h2>
        </div>

        <div class="cards-grid">
            @if(count($games) > 0)
                @foreach($games as $game)
                    <a href="/games/{{$game->id}}" class="game-card">
                        
                        <div class="card-image-wrapper">
                            <img src="{{ asset('img/games/' . $game->image) }}" alt="{{ $game->name_game }}">
                        </div>
                        
                        <div class="card-content">
                            <h3 class="card-title">{{$game->name_game}}</h3>

                            <p class="card-price">
                                <p class="card-price">
                                    @if($minBuyPrice)
                                        Compra: a partir de <strong>R$ {{ number_format($minBuyPrice, 2, ',', '.') }}</strong>
                                    @else
                                        <span class="unavailable">Indisponível para compra</span>
                                    @endif
                                    @if($minRentPrice)
                                        Aluguel: a partir de <strong>R$ {{ number_format($minRentPrice, 2, ',', '.') }}</strong>
                                    @else
                                        <span class="unavailable">Indisponível para aluguel</span>
                                    @endif
                                </p>
                            </p>
                        </div>
                    </a>
                @endforeach 
            @else
                <div class="no-results">
                    <h3>😕 Nenhum jogo encontrado</h3>
                    <p>Não encontramos nada com o termo "<strong>{{$search}}</strong>".</p>
                    <a href="/" class="btn-link">Voltar para a Loja</a>
                </div>
            @endif
        </div>
    </section>
</main>

@endsection