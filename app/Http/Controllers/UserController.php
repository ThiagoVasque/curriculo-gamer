<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Friendship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // IMPORTANTE

class UserController extends Controller
{
    public function buscarUsuarios(Request $request)
    {
        $query = $request->input('q');
        $myId = Auth::id(); // Use Auth::id() com a Facade importada

        if (empty($query)) return response()->json([]);

        $usuarios = User::where('name', 'LIKE', "%{$query}%")
            ->where('id', '!=', $myId)
            ->get();

        $listaFormatada = $usuarios->map(function ($user) use ($myId) {
            $friendship = Friendship::where(function ($q) use ($myId, $user) {
                $q->where('user_id', $myId)->where('friend_id', $user->id);
            })->orWhere(function ($q) use ($myId, $user) {
                $q->where('user_id', $user->id)->where('friend_id', $myId);
            })->first();

            return [
                'id' => $user->id,
                'name' => $user->name,
                'level' => $user->level ?? 1,
                'friendship_status' => $friendship ? $friendship->status : null,
            ];
        });

        return response()->json($listaFormatada);
    }
}