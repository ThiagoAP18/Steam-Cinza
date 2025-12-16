<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'name_game',
        'description',
        'image',
        'user_id',
        'dt_launch',
        'initial_quantity',
        'actual_quantity',
        'price'
    ];
    
    protected $dates = ['date'];

    protected $casts = [
        'dt_launch' => 'datetime'
    ];
    
    protected $guarded = [];

    public function licenses(){
        return $this->hasMany(License::class);
    }

    public function developer(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags(){
        return $this->belongsToMany(Tag::class);
    }
}
