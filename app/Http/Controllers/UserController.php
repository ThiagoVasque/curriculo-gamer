<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function buscarUsuarios(Request $request)
    {
        $query = $request->input('q');

        // Busca usuários pelo nome, exceto o usuário logado
        $usuarios = User::where('name', 'LIKE', "%{$query}%")
            ->where('id', '!=', auth()->id())
            ->get();

        return response()->json($usuarios);
    }
}
