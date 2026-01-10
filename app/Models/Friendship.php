<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    use HasFactory;

    // Esta é a linha que resolve o erro do LOG:
    protected $fillable = ['user_id', 'friend_id', 'status'];

    // Relacionamento com quem enviou o convite
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relacionamento com quem recebeu o convite
    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    /**
     * Opcional: Relacionamentos para facilitar buscas futuras
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
