<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Friendship; // Import necessário
use App\Models\Game;       // Import necessário

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'xp',
        'level',
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
     * Relacionamento para o SINO DE NOTIFICAÇÕES (O QUE FALTAVA)
     */
    public function pendingFriendRequests()
    {
        // friend_id é quem recebe o convite
        return $this->hasMany(Friendship::class, 'friend_id')->where('status', 'pending');
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
     */
    public function addExp($amount)
    {
        // Se o amount for negativo (como no caso do update), o max(0) também protege aqui
        $this->xp = max(0, $this->xp + $amount);
        $this->save();
    }

    public function removeExp($amount)
    {
        $this->xp = max(0, $this->xp - $amount);
        $this->save();
    }
}
