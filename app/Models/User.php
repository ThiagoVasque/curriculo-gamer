<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'xp',      // Adicionei aqui para permitir preenchimento
        'level',   // Adicionei aqui também
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'xp' => 'integer',
            'level' => 'integer',
        ];
    }

    /**
     * Relacionamento: Um usuário possui muitos jogos.
     */
    public function games()
    {
        return $this->hasMany(Game::class);
    }

    /**
     * Sistema de Experiência (XP)
     * Este é o método que estava faltando e causando o erro!
     */
    public function addExp($amount)
    {
        $this->xp += $amount;
        $this->level = floor($this->xp / 1000) + 1;
        return $this->save();
    }

    public function removeExp($amount)
    {
        $this->xp -= $amount;
        if ($this->xp < 0) $this->xp = 0;

        // Recalcula o nível (se o XP cair muito, o level desce)
        $this->level = floor($this->xp / 1000) + 1;

        return $this->save();
    }
}
