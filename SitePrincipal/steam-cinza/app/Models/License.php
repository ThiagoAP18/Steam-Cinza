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
        'rent_expires_at',
        'rent_time',
        'status'
    ];

    protected $casts = [
        'updated_at' => 'datetime',
        'rent_expires_at' => 'datetime'
    ];

    public function game(){
        return $this->belongsTo(Game::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function lastOwner(){
        return $this->belongsTo(User::class, 'last_owner_id');
    }
}
