<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class GameController extends Controller
{
    // Esta é a função que estava faltando!
    public function index()
    {
        // Busca os jogos que o usuário logado salvou no banco de dados
        $myGames = Game::where('user_id', Auth::id())->get();

        // Retorna a view do dashboard passando os jogos
        return view('dashboard', compact('myGames'));
    }

    // Função para a Home Pública
    public function welcome()
    {
        return view('welcome');
    }

    // Função de Busca para usuários logados
    public function search(Request $request)
    {
        $query = $request->input('q');
        $token = $this->getIgdbToken();

        $response = Http::withHeaders([
            'Client-ID' => env('IGDB_CLIENT_ID'),
            'Authorization' => 'Bearer ' . $token,
        ])->withBody("search \"$query\"; fields name, cover.url; limit 12;", 'text/plain')
            ->post('https://api.igdb.com/v4/games');

        return response()->json($response->json());
    }

    // Função de Busca para a Home Pública (Welcome)
    public function searchPublic(Request $request)
    {
        return $this->search($request);
    }

    // Método auxiliar para pegar o Token (evita repetição de código)
    private function getIgdbToken()
    {
        $response = Http::post("https://id.twitch.tv/oauth2/token", [
            'client_id' => env('IGDB_CLIENT_ID'),
            'client_secret' => env('IGDB_CLIENT_SECRET'),
            'grant_type' => 'client_credentials',
        ]);
        return $response->json()['access_token'];
    }

    public function store(Request $request)
    {
        Game::create([
            'user_id' => Auth::id(),
            'igdb_id' => $request->igdb_id,
            'title' => $request->title,
            'cover_url' => $request->cover_url,
            'status' => $request->status,
            'rating' => $request->rating,
            'year_completed' => date('Y'),
        ]);

        return redirect()->route('dashboard');
    }

    public function update(Request $request, Game $game)
    {
        // Garante que o usuário só edite os próprios jogos
        if ($game->user_id !== Auth::id())
            abort(403);

        $game->update([
            'status' => $request->status,
            'rating' => $request->rating,
            'review' => $request->review,
            'year_completed' => $request->year_completed,
        ]);

        return redirect()->back()->with('success', 'Jogo atualizado!');
    }

    public function destroy(Game $game)
    {
        if ($game->user_id !== Auth::id())
            abort(403);

        $game->delete();
        return redirect()->back()->with('success', 'Jogo removido!');
    }
}