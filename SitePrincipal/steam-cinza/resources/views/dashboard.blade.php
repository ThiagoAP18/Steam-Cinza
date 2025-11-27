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

        <!-- ========================================== -->
        <!-- CONTEÚDO DA PUBLICADORA (VENDEDOR)         -->
        <!-- ========================================== -->
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
                    <div class="p-4 text-center">
                        <p class="text-muted">Você ainda não publicou nenhum jogo.</p>
                        <a href="/games/create">Começar agora</a>
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
                                    <th>Data da Compra</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($boughtLicenses as $license)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <!-- Asset helper é melhor pra garantir caminho -->
                                                <img src="{{ asset('img/games/' . $license->game->image) }}" alt="" class="table-thumb">
                                                <a href="/games/{{$license->game->id}}" class="fw-bold text-dark text-decoration-none">
                                                    {{ $license->game->name_game }}
                                                </a>
                                            </div>
                                        </td>
                                        <td><span class="key-code">{{ $license->license_key }}</span></td>
                                        <td>{{ $license->updated_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="#" class="btn-action btn-download">Baixar</a>
                                            <a href="#" class="btn-action btn-sell">Revender</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- 2. TABELA DE ALUGUÉIS (TEMPORÁRIOS) -->
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
                                        
                                        <!-- Mostra o nome do dono anterior (quem alugou pra vc) ou Loja -->
                                        <td>{{ optional($license->lastOwner)->name ?? 'Loja Oficial' }}</td>
                                        
                                        <td>
                                            <!-- Se rent_expires_at for nulo por erro, evita quebrar -->
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
            @if(count($rentedLicenses) == 0 && count($boughtLicenses) == 0)
                <div class="empty-state">
                    <h3>Sua biblioteca está vazia 😢</h3>
                    <p>Visite a loja para adquirir novos jogos.</p>
                    <a href="/" class="btn-create">Ir para a Loja</a>
                </div>
            @endif
        @endif
    </div>
</div>

@endsection