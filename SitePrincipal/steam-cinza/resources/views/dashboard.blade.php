@extends('layouts.main')

@section('title', $pageTitle)

@section('content')

<div class="dashboard-container">
    <div class="container">
        <div class="dashboard-header">
            <div>
                <h1>{{ $pageTitle }}</h1>
                <p class="text-muted mb-0">
                    @if($user->type == 'publisher')
                        Gerencie seus lançamentos e estoque.
                    @else
                        Gerencie seus jogos comprados e alugados.
                    @endif
                </p>
            </div>
            
            @if($user->type == 'publisher')
                <a href="/games/create" class="btn-create">+ Criar Novo Jogo</a>
            @endif
        </div>
        @if($user->type == 'publisher')
            <div class="custom-table-card">
                <h3 class="card-header-title">Jogos Publicados</h3>
                
                @if(count($games) > 0)
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Capa</th>
                                    <th>Nome</th>
                                    <th>Lançamento</th>
                                    <th>Estoque</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($games as $game)
                                    <tr>
                                        <td>
                                            <img src="/img/games/{{ $game->image }}" alt="" class="table-thumb">
                                        </td>
                                        <td>
                                            <a href="/games/{{$game->id}}" class="fw-bold text-dark text-decoration-none">
                                                {{$game->name_game}}
                                            </a>
                                        </td>
                                        <td>{{ date('d/m/Y', strtotime($game->dt_launch)) }}</td>
                                        <td>{{ $game->initial_quantity }} un.</td>
                                        <td>
                                            <a href="/games/edit/{{$game->id}}" class="btn-action btn-edit">Editar</a>
                                            <form action="/games/{{$game->id}}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action btn-delete" onclick="return confirm('Tem certeza?')">Deletar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <p>Você ainda não publicou nenhum jogo.</p>
                        <a href="/games/create" class="btn-link">Lançar Jogo</a>
                    </div>
                @endif
            </div>
        @else
            @if(count($boughtLicenses) > 0)
                <div class="custom-table-card">
                    <h3 class="card-header-title">🎮 Jogos Comprados (Meus)</h3>
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Jogo</th>
                                    <th>Chave de Ativação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($boughtLicenses as $license)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('img/games/' . $license->game->image) }}" alt="" class="table-thumb">
                                                <a href="/games/{{$license->game->id}}" class="fw-bold text-dark text-decoration-none">
                                                    {{ $license->game->name_game }}
                                                </a>
                                            </div>
                                        </td>
                                        <td><span class="key-code">{{ $license->license_key }}</span></td>
                                        <td>
                                            <a href="#" class="btn-action btn-download">Baixar</a>
                                            <button type="button" 
                                                    class="btn-action btn-sell" 
                                                    onclick="openAnnounceModal({{ $license->id }}, '{{ $license->game->name_game }}')">
                                                Anunciar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            @if(count($rentedLicenses) > 0)
                <div class="custom-table-card">
                    <h3 class="card-header-title">⏳ Jogos Alugados</h3>
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Jogo</th>
                                    <th>Chave Temporária</th>
                                    <th>Proprietário Real</th>
                                    <th>Vencimento</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rentedLicenses as $license)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('img/games/' . $license->game->image) }}" alt="" class="table-thumb">
                                                <a href="/games/{{$license->game->id}}" class="fw-bold text-dark text-decoration-none">
                                                    {{ $license->game->name_game }}
                                                </a>
                                            </div>
                                        </td>
                                        <td><span class="key-code">{{ $license->license_key }}</span></td>
                                        
                                        <td>
                                            @if($license->lastOwner)
                                                {{ $license->lastOwner->name }}
                                            @else
                                                <span class="badge badge-official">Loja Oficial</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            {{ optional($license->rent_expires_at)->format('d/m/Y H:i') ?? '--' }}
                                        </td>
                                        
                                        <td>
                                            @if($license->rent_expires_at && $license->rent_expires_at->isPast())
                                                <span class="badge-status expired">Vencido</span>
                                            @else
                                                <span class="badge-status active">
                                                    Restam {{ optional($license->rent_expires_at)->diffForHumans(null, true) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            @if(count($boughtLicenses) == 0 && count($rentedLicenses) == 0)
                <div class="empty-state">
                    <h3>Sua biblioteca está vazia 😢</h3>
                    <p>Visite a loja para adquirir novos jogos.</p>
                    <a href="/" class="btn-create">Ir para a Loja</a>
                </div>
            @endif
        @endif
    </div>
</div>
<div id="announceModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalGameTitle">Anunciar Jogo</h2>
            <button class="close-modal" onclick="closeAnnounceModal()">×</button>
        </div>
        <form action="{{ route('games.announce') }}" method="POST" id="announceForm">
            @csrf
            <input type="hidden" name="license_id" id="modalLicenseId">
            <div class="modal-body">
                <p class="text-muted">Selecione as modalidades (você pode marcar ambas):</p>
                <div class="option-container mb-3">
                    <label class="type-option-checkbox">
                        <input type="checkbox" name="enable_sale" id="checkSale" onchange="toggleOptions()">
                        <span class="type-box-row">
                            <span class="icon">💰</span>
                            <span class="info">
                                <span class="label">Vender</span>
                                <span class="desc">Definir preço de venda definitiva.</span>
                            </span>
                        </span>
                    </label>
                    <div id="saleInputs" class="inputs-wrapper" style="display:none;">
                        <div class="form-group">
                            <label>Valor da Venda (R$):</label>
                            <input type="number" name="sale_price" id="salePrice" class="form-control" step="0.01" min="1" placeholder="0.00" oninput="calcSale()">
                            <small class="earnings-feedback text-success" id="saleEarnings">Você recebe: R$ 0,00</small>
                        </div>
                    </div>
                </div>
                <div class="option-container">
                    <label class="type-option-checkbox">
                        <input type="checkbox" name="enable_rent" id="checkRent" onchange="toggleOptions()">
                        <span class="type-box-row">
                            <span class="icon">⏳</span>
                            <span class="info">
                                <span class="label">Alugar</span>
                                <span class="desc">Definir preço e tempo de empréstimo.</span>
                            </span>
                        </span>
                    </label>
                    <div id="rentInputs" class="inputs-wrapper" style="display:none;">
                        <div class="form-row">
                            <div class="form-group half">
                                <label>Valor (R$):</label>
                                <input type="number" name="rent_price" id="rentPrice" class="form-control" step="0.01" min="1" placeholder="0.00" oninput="calcRent()">
                            </div>
                            <div class="form-group half">
                                <label>Dias:</label>
                                <input type="number" name="rental_days" id="rentDays" class="form-control" min="1" max="30" placeholder="Ex: 7">
                            </div>
                        </div>
                        <small class="earnings-feedback text-info" id="rentEarnings">Você recebe: R$ 0,00</small>
                    </div>
                </div>

                <div class="alert alert-warning mt-3" id="errorMsg" style="display:none; font-size: 0.85rem;">
                    Selecione pelo menos uma opção para anunciar.
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAnnounceModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnConfirm" disabled>Confirmar Anúncio</button>
            </div>
        </form>
    </div>
</div>
@endsection