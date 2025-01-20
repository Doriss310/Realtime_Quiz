<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GameSession extends Model
{

    protected $with = ['players'];
    protected $fillable = ['code', 'quiz_id', 'host_id', 'status'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->code = strtoupper(Str::random(6));
        });
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }
}
