@extends('layouts.main')

@section('title', "Criando Jogo")

@section('content')

<div class="create-game-wrapper">
    <div class="create-game-card">
        
        <h1>Criar Jogo</h1>

        <form action="/games" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="image">Capa do Jogo:</label>
                <input type="file" id="image" name="image" class="form-control-file">
            </div>
            <div class="form-group">
                <label for="name_game">Nome:</label>
                <input type="text" id="name_game" name="name_game" class="form-control" placeholder="Digite o nome do jogo">
            </div>
            <div class="form-group">
                <label for="dt_launch">Data de Lançamento:</label>
                <input type="date" class="form-control" id="dt_launch" name="dt_launch">
            </div>
            <div class="form-group">
                <label for="new_tags">Tags / Gêneros:</label>
                <input type="text" 
                    class="form-control" 
                    id="new_tags" 
                    name="new_tags" 
                    placeholder="Ex: Ação, RPG, Estratégia (Separe por vírgula)">
                <small class="text-muted">Opcional. Você pode criar novas tags digitando aqui.</small>
            </div>
            <div class="form-group">
                <label for="description">Descrição:</label>
                <textarea name="description" id="description" class="form-control" placeholder="Digite a descrição do jogo"></textarea>
            </div>
            <div class="form-group">
                <label for="initial_quantity">Quantidade Inicial de Licenças:</label>
                <input type="number" class="form-control" id="initial_quantity" name="initial_quantity">
            </div>
            <div class="form-group">
                <label for="price">Preço Inicial (R$):</label>
                <input type="number" step="0.01" min="0" class="form-control" id="price" name="price">
            </div>
            
            <input type="submit" class="btn btn-primary" value="Criar jogo na loja">
        </form>

    </div>
</div>

@endsection