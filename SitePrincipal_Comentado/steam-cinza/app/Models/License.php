<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = [
        'game_id',        // ID do jogo ao qual a licença pertence
        'user_id',        // ID do usuário atual dono da licença
        'license_key',    // Chave da licença (gerada aleatoriamente)
        'price',          // Preço para venda
        'rent_price',     // Preço para aluguel
        'rent_expires_at',// Data em que o aluguel expira
        'rent_time',      // Tempo de aluguel (em dias)
        'status'          // Status da licença (ex: available, sold)
    ];

    // Conversões automáticas de tipos para datas/datetimes
    protected $casts = [
        'updated_at' => 'datetime',      // Converte updated_at para Carbon
        'rent_expires_at' => 'datetime'  // Converte rent_expires_at para Carbon
    ];

    // Relação: cada licença pertence a um jogo
    public function game(){
        return $this->belongsTo(Game::class);
    }

    // Relação: cada licença pertence a um usuário (dono atual)
    public function user(){
        return $this->belongsTo(User::class);
    }

    // Relação: identifica o último dono da licença, usando a coluna last_owner_id
    public function lastOwner(){
        return $this->belongsTo(User::class, 'last_owner_id');
    }
}
