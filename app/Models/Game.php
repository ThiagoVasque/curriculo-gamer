<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    // Campos que o Laravel permite salvar no banco
    protected $fillable = [
        'title',
        'igdb_id',
        'cover_url',
        'status',
        'rating',
        'summary',
        'developer',
        'release_year',
        'review',
        'user_id',
        'platforms' // ADICIONE ISSO AQUI
    ];

    // Relacionamento: Um jogo pertence a um usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getXpValue()
    {
        $pesos = [
            'platinado'   => 150,
            'zerado'      => 100,
            'jogando'     => 10,
            'quero_jogar' => 0,
        ];

        $valor = $pesos[$this->status] ?? 0;

        if (!empty($this->review)) {
            $valor += 30;
        }

        return $valor;
    }
}
