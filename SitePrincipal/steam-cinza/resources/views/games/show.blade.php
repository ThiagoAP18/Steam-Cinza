@extends('layouts.main')

@section('title', $game->name_game)

@section('content')

<div class="game-container container">
    <div class="row">
        <div class="col-md-5">
            <img src="/img/games/{{ $game->image }}" class="img-fluid rounded shadow" alt="{{ $game->name_game }}">
        </div>

        <div class="col-md-7" id="game-details">
            <h1>{{ $game->name_game }}</h1>
            
            <p class="game-publisher">
                Publicado por: <strong>{{ $publisher }}</strong> 
            </p>

            <h3>Descrição:</h3>
            <p class="game-desc">{{ $game->description }}</p>
            
            <hr>

            @if(!$hasGame)
                <h3 class="mt-4 mb-3">Ofertas Disponíveis:</h3>

                @if(count($commonLicenses) > 0 || count($publisherLicenses) > 0)
                    <div class="table-responsive">
                        <table class="licenses-table">
                            <thead>
                                <tr>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Modalidade</th>
                                    <th scope="col">Vendedor</th>
                                    <th scope="col">Preços</th>
                                    @if(count($publisherLicenses) > 0)
                                        <th scope="col">Quantidade em Estoque</th>
                                    @endif
                                    <th scope="col">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge badge-official">Oficial</span></td>
                                    <td>
                                        <div class="d-flex flex-column"><span class="text-success fw-bold">Compra</span></div>
                                    </td>
                                    <td>
                                        <div class="user_name">
                                            {{ $publisher }}
                                            @if(Auth::id() == $game->publisher_id) 
                                                <br><small class="text-muted fst-italic">(Você)</small> 
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="price-value">
                                                R$ {{ number_format($publisherLicenses[0]->price, 2, ',', '.') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="price-value">
                                                {{count($publisherLicenses)}} Cópias
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @auth
                                            @if(Auth::user()->type == 'publisher')
                                                <span class="text-muted small" title="Publicadoras não compram jogos">Indisponível p/ CNPJ</span>
                                            
                                            @else
                                                <div class="d-flex flex-column gap-2">
                                                    <a href="#" class="btn btn-primary btn-sm">Comprar</a>
                                                </div>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Login para Adquirir</a>
                                        @endauth
                                    </td>
                                </tr>
                            </tbody>
                            <tbody>
                                @foreach ($commonLicenses as $license)
                                    <tr>
                                        <td>
                                            <span class="badge badge-resale">Revenda</span>
                                        </td>

                                        <td>
                                            <div class="d-flex flex-column">
                                                @if($license->price)
                                                    <span class="text-success fw-bold">Compra</span>
                                                @endif
                                                @if($license->rent_price)
                                                    <span class="text-info fw-bold">Aluguel</span>
                                                @endif
                                            </div>
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
                                                @if($license->price)
                                                    <span class="price-value">
                                                        R$ {{ number_format($license->price, 2, ',', '.') }}
                                                    </span>
                                                @endif
                                                
                                                @if($license->rent_price)
                                                    <span class="rent-value">
                                                        R$ {{ number_format($license->rent_price, 2, ',', '.') }} <small>/mês</small>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            @auth
                                                @if(Auth::id() == $license->user_id)
                                                    <button disabled class="btn btn-secondary btn-sm w-100">Seu Item</button>
                                                
                                                @elseif(Auth::user()->type == 'publisher')
                                                    <span class="text-muted small" title="Publicadoras não compram jogos">Indisponível p/ CNPJ</span>
                                                
                                                @else
                                                    <div class="d-flex flex-column gap-2">
                                                        @if($license->price)
                                                            <a href="#" class="btn btn-primary btn-sm">
                                                                Comprar
                                                            </a>
                                                        @endif

                                                        @if($license->rent_price)
                                                            <a href="#" class="btn btn-info btn-sm text-white">
                                                                Alugar
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            @else
                                                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Login para Adquirir</a>
                                            @endauth
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning">
                        Opa! Nenhuma licença disponível para compra no momento.
                    </div>
                @endif
            @else
                <div class="alert alert-success mt-4" role="alert">
                    ✅ <strong>Você já possui este jogo!</strong><br>
                    Acesse sua biblioteca para visualizar a chave de ativação.
                </div>
            @endif

        </div>
    </div>
</div>

@endsection