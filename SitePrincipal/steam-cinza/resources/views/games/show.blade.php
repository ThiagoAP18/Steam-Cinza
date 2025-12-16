@extends('layouts.main')

@section('title', $game->name_game)

@section('content')

<div class="game-container container">
    <div class="row">
        <!-- Coluna da Imagem -->
        <div class="col-md-5">
            <img src="/img/games/{{ $game->image }}" class="img-fluid rounded shadow" alt="{{ $game->name_game }}">
        </div>

        <!-- Coluna dos Detalhes -->
        <div class="col-md-7" id="game-details">
            <h1>{{ $game->name_game }}</h1>

            @if($game->tags->count() > 0)
                <div class="tags-display">
                    @foreach($game->tags as $tag)
                        <span class="game-tag">{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif
            
            <p class="game-publisher">
                Publicado por: <strong>{{ $publisher }}</strong> 
            </p>

            <h3>Descrição:</h3>
            <p class="game-desc">{{ $game->description }}</p>
            
            <hr>
                @if($hasGame)
                    <div class="alert alert-success mt-4">
                        ✅ <strong>Você já possui este jogo!</strong><br>
                        Acesse sua biblioteca para visualizar a chave.
                    </div>
                @endif
                @if(count($commonLicenses) > 0 || count($publisherLicenses) > 0)
                    <h3 class="mt-4 mb-3">Ofertas Disponíveis:</h3>

                    <div class="table-responsive">
                        <table class="licenses-table">
                            <thead>
                                <tr>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Modalidade</th>
                                    <th scope="col">Tempo</th>
                                    <th scope="col">Vendedor</th>
                                    <th scope="col">Preço</th>
                                    <th scope="col">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- 1. OFERTAS OFICIAIS (PUBLICADORA) -->
                                @if(count($publisherLicenses) > 0)
                                    <tr>
                                        <td><span class="badge badge-official">Oficial</span></td>
                                        <td><span class="text-success fw-bold">Compra</span></td>
                                        <td><span class="text-muted">-</span></td>
                                        <td>
                                            <div class="user_name">
                                                {{ $publisher }}
                                                @if(Auth::id() == $game->publisher_id) 
                                                    <br><small class="text-muted fst-italic">(Você)</small> 
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="price-value">R$ {{ number_format($game->initial_price ?? $publisherLicenses->first()->price, 2, ',', '.') }}</span>
                                                <small class="text-muted">{{ $game->actual_quantity }} em estoque</small>
                                            </div>
                                        </td>
                                        <td>
                                            @auth
                                                @if(Auth::user()->type == 'publisher')
                                                    <span class="text-muted small">Indisponível p/ CNPJ</span>
                                                @elseif($hasGame)
                                                    <button disabled class="btn btn-success btn-sm w-100 opacity-50">Adquirido</button>
                                                @else
                                                    <form action="/games/buy/{{$publisherLicenses->first()->id}}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary btn-sm w-100">Comprar</button>
                                                    </form>
                                                @endif
                                            @else
                                                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Login</a>
                                            @endauth
                                        </td>
                                    </tr>
                                @endif

                                <!-- 2. OFERTAS DE REVENDA (JOGADORES) - ESTAVA FALTANDO ISSO AQUI -->
                                @if(count($commonLicenses) > 0)
                                    @foreach ($commonLicenses as $license)
                                        <tr>
                                            <td><span class="badge badge-resale">Revenda</span></td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    @if($license->buy) <span class="text-success fw-bold">Compra</span> @endif
                                                    @if($license->rent) <span class="text-info fw-bold">Aluguel</span> @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($license->rent)
                                                    {{$license->rent_time}} Dias
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="user_name">
                                                    {{ $license->user->name }}
                                                    @if(Auth::id() == $license->user_id) 
                                                        <br><small class="text-muted fst-italic">(Você)</small> 
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    @if($license->buy)
                                                        <span class="price-value">R$ {{ number_format($license->price, 2, ',', '.') }}</span>
                                                    @endif
                                                    @if($license->rent)
                                                        <span class="rent-value">R$ {{ number_format($license->rent_price, 2, ',', '.') }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @auth
                                                    @if(Auth::id() == $license->user_id)
                                                        <button disabled class="btn btn-secondary btn-sm w-100">Seu Item</button>
                                                    @elseif($hasGame)
                                                        <button disabled class="btn btn-success btn-sm w-100 opacity-50">Adquirido</button>
                                                    @elseif(Auth::user()->type == 'publisher')
                                                        <span class="text-muted small">Indisponível</span>
                                                    @else
                                                        <div class="d-flex flex-column gap-2">
                                                            @if($license->buy)
                                                                <form action="/games/buy/{{$license->id}}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-primary btn-sm w-100">Comprar</button>
                                                                </form>
                                                            @endif
                                                            @if($license->rent)
                                                                <form action="/games/rent/{{$license->id}}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-info btn-sm w-100 text-white">Alugar</button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @else
                                                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Login</a>
                                                @endauth
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning mt-4">
                        Nenhuma oferta disponível para este jogo no momento.
                    </div>
                @endif
        </div>
    </div>
</div>

@endsection