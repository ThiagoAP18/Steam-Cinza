@extends('layouts.main')

@section('title', $pageTitle)

@section('content')

<div class="dashboard-container">
    <div class="container">
        
        <!-- CABEÇALHO (Mantido Igual) -->
        <div class="dashboard-header">
            <div>
                <h1>{{ $pageTitle }}</h1>
                <!-- ... -->
            </div>
            @if($user->type == 'publisher')
                <a href="/games/create" class="btn-create">+ Criar Novo Jogo</a>
            @endif
        </div>

        @if($user->type == 'publisher')
            <!-- ... (Código da Publicadora Mantido Igual) ... -->
            <!-- ... Copie o código da sua publicadora aqui ... -->
             <div class="custom-table-card">
                <!-- ... Tabela da publicadora ... -->
             </div>

        @else
            <!-- VISÃO DO JOGADOR -->
            
            <!-- Tabela de Compras (Onde está o botão Anunciar) -->
            @if(count($boughtLicenses) > 0)
                <div class="custom-table-card">
                    <h3 class="card-header-title">🎮 Jogos Comprados (Meus)</h3>
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Jogo</th>
                                    <th>Chave</th>
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
        @endif
    </div>
</div>

<div id="announceModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalGameTitle">Anunciar Jogo</h2>
            <button class="close-modal" onclick="closeAnnounceModal()">×</button>
        </div>
        
        <form action="/games/announce/{{$license->id}}" method="POST" id="announceForm">
            @csrf
            <input type="hidden" name="license_id" id="modalLicenseId">

            <div class="modal-body">
                <p class="text-muted">Selecione as modalidades (você pode marcar ambas):</p>
                
                <!-- OPÇÃO 1: VENDA -->
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

                    <!-- Inputs de Venda (Escondidos por padrão) -->
                    <div id="saleInputs" class="inputs-wrapper" style="display:none;">
                        <div class="form-group">
                            <label>Valor da Venda (R$):</label>
                            <input type="number" name="sale_price" id="salePrice" class="form-control" step="0.01" min="1" placeholder="0.00" oninput="calcSale()">
                            <small class="earnings-feedback text-success" id="saleEarnings">Você recebe: R$ 0,00</small>
                        </div>
                    </div>
                </div>

                <!-- OPÇÃO 2: ALUGUEL -->
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

                    <!-- Inputs de Aluguel (Escondidos por padrão) -->
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

                <div class="alert alert-warning mt-3" id="errorMsg">
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