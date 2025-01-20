<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'game_session_id',
        'score',
        'user_id',
    ];
    public function gameSession(){
        return $this->belongsTo(GameSession::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
