@extends('layouts.main')

@section('title', "Editando Jogo")

@section('content')

<div class="create-game-wrapper">
    <div class="create-game-card">
        
        <h1>Editar Jogo</h1>

        <form action="/games/update/{{$game->id}}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="image">Capa do Jogo:</label>
                <input type="file" id="image" name="image" class="form-control-file">
                <img src="/img/games/{{$game->image}}" alt="{{$game->title}}" class="img-preview">
            </div>
            <div class="form-group">
                <label for="name_game">Nome:</label>
                <input type="text" id="name_game" name="name_game" class="form-control" placeholder="Digite o nome do jogo" value="{{$game->name_game}}">
            </div>
            <div class="form-group">
                <label for="dt_launch">Data de Lançamento:</label>
                <input type="date" class="form-control" id="dt_launch" name="dt_launch" value="{{$game->dt_launch->format('Y-m-d')}}">
            </div>
            <div class="form-group">
                <label for="new_tags">Tags / Gêneros:</label>
                <input type="text" 
                    class="form-control" 
                    id="new_tags" 
                    name="new_tags" 
                    placeholder="Ex: Ação, RPG"
                    value="{{ $game->tags->pluck('name')->implode(', ') }}">
            </div>
            <div class="form-group">
                <label for="description">Descrição:</label>
                <textarea name="description" id="description" class="form-control" placeholder="Digite a descrição do jogo">{{$game->description}}</textarea>
            </div>
            <div class="form-group">
                <label for="new_quantity">Quantidade Atual de Licenças:</label>
                <input type="number" class="form-control" id="new_quantity" name="new_quantity" value="{{$game->actual_quantity}}">
            </div>
            <div class="form-group">
                <label for="price">Preço Atual (R$):</label>
                <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" value="{{$game->initial_price}}">
            </div>
            
            <input type="submit" class="btn btn-primary" value="Editar Jogo">
        </form>
    </div>
</div>

@endsection