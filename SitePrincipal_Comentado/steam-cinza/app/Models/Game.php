<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{

    protected $fillable = [
        'name_game',         // Nome do jogo
        'description',       // Descrição do jogo
        'image',             // Nome/arquivo da imagem do jogo
        'user_id',           // ID do criador / publicador (mas na controller você usa publisher_id)
        'dt_launch',         // Data de lançamento
        'initial_quantity',  // Quantidade inicial de licenças
        'actual_quantity',   // Quantidade atual de licenças disponíveis
        'price'              // Preço inicial (observação: na controller você usa initial_price)
    ];
    
    protected $dates = ['date']; 

    protected $casts = [
        'dt_launch' => 'datetime'
    ];
    
    protected $guarded = [];

    // Relação: Um jogo possui muitas licenças
    public function licenses(){
        return $this->hasMany(License::class);
    }

    // Relação: Um jogo pertence a um desenvolvedor/publicador
    public function developer(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
