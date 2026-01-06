<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    // Campos que o Laravel permite salvar no banco
    protected $fillable = [
        'user_id',
        'igdb_id',
        'title',
        'cover_url',
        'status',
        'rating',
        'year_completed',
        'hltb_hours',
        'review'
    ];

    // Relacionamento: Um jogo pertence a um usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}