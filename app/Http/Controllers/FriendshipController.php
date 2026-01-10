<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Adicione este import

class FriendshipController extends Controller
{
    public function accept($id)
    {
        $friendship = Friendship::findOrFail($id);
        $myId = Auth::id(); // Usar Auth::id() é mais limpo e o VS Code aceita melhor

        if ($friendship->friend_id !== $myId) {
            return back()->with('error', 'Ação não autorizada.');
        }

        $friendship->update(['status' => 'accepted']);

        return back()->with('success', 'Convite aceito!');
    }

    public function store($id)
    {
        $myId = Auth::id();

        // 1. Verifica se já existe qualquer relação (pendente ou aceita)
        $exists = Friendship::where(function ($q) use ($myId, $id) {
            $q->where('user_id', $myId)->where('friend_id', $id);
        })->orWhere(function ($q) use ($myId, $id) {
            $q->where('user_id', $id)->where('friend_id', $myId);
        })->first();

        if ($exists) {
            return response()->json(['message' => 'Relação já existente'], 422);
        }

        Friendship::create([
            'user_id'   => $myId,
            'friend_id' => $id,
            'status'    => 'pending'
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $friendship = \App\Models\Friendship::findOrFail($id);

        // Verifica se quem está deletando é quem recebeu ou quem enviou
        if ($friendship->user_id == Auth::id() || $friendship->friend_id == Auth::id()) {
            $friendship->delete();
            return back()->with('success', 'Solicitação removida!');
        }

        return back()->with('error', 'Ação não autorizada.');
    }
}
