<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = [
        'game_id',
        'user_id',
        'license_key',
        'price',
        'rent_price',
        'status'
    ];

    public function game(){
        return $this->belongsTo(Game::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
