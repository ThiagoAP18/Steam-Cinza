@extends ('layouts.main')

@if(Auth::user()->type == 'publisher')
    @section('title', 'Meus Jogos Lançados')
@else
    @section('title', 'Meus Jogos')
@endif

@section('content')

<div class="col-md-10 offset-md-1 dashboard-title-container">
    @if(Auth::user()->type == 'publisher')
        <h1>Meus Jogos Publicados</h1>
    @else
        <h1>Minha Licenças</h1>
    @endif
</div>

<div class="col-md-10 offset-md-1 dashboard-games-container">
    @if(Auth::user()->type == 'publisher')
        @if(count($games) > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nome</th>
                        <th scope="col">Data de Lançamento</th>
                        <th scope="col">Quantidade em Estoque</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($games as $game)
                        <tr>
                            <th scope="row">{{$loop->index + 1}}</th>
                            <td>
                                <a href="/games/{{$game->id}}">{{$game->name_game}}</a>
                            </td>
                            <td>
                                <a href="/games/edit/{{$game->id}}" class="btn btn-info edit-btn">Editar</a>
                                <form action="/games/{{$game->id}}">
                                    @csrf
                                    @method('DELETE')
                                    <button type = "submit" class = "btn btn-danger delete-btn">Deletar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Você ainda não tem jogos lançados, <a href="/games/create">Lançar Jogo</a></p>
        @endif
    @else
        @if(count($licenses) > 0)

        @endif
    @endif
</div>
@endsection