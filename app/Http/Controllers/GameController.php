<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response; // Importante para a tipagem
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;

class GameController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Agora o editor vai reconhecer o método games() sem erro
        $myGames = $user->games()->latest()->get();

        // Estatísticas para os cards
        $stats = [
            'total' => $myGames->count(),
            'zerados' => $myGames->where('status', 'zerado')->count(),
            'jogando' => $myGames->where('status', 'jogando')->count(),
        ];

        return view('dashboard', compact('myGames', 'stats'));
    }

    public function welcome()
    {
        try {
            $token = $this->getIgdbToken();

            // Busca alguns jogos populares para preencher a página inicial
            // Procure a linha $body dentro de welcome e deixe assim:
            $body = "fields name, cover.url, total_rating, summary, platforms.name; 
         where cover != null & total_rating != null; 
         sort total_rating desc; 
         limit 6;";

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withoutVerifying()->withHeaders([
                'Client-ID' => (string) env('IGDB_CLIENT_ID'),
                'Authorization' => 'Bearer ' . $token,
            ])->withBody($body, 'text/plain')->post('https://api.igdb.com/v4/games');

            // Agora o erro no successful() e json() deve sumir
            $featuredGames = $response->successful() ? $response->json() : [];

            // Traduz as sinopses antes de enviar para a Welcome
            if (is_array($featuredGames)) {
                foreach ($featuredGames as &$game) {
                    if (isset($game['summary'])) {
                        $game['summary'] = $this->traduzirTexto($game['summary']);
                    }
                }
            }

            return view('welcome', compact('featuredGames'));
        } catch (\Exception $e) {
            // Se algo falhar, envia um array vazio para não quebrar a página
            return view('welcome', ['featuredGames' => []]);
        }
    }


    public function search(Request $request)
    {
        $query = $request->input('q');
        $page = (int) $request->input('page', 1);
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        if (!$query) return response()->json([]);

        try {
            $token = $this->getIgdbToken();

            // Trocamos 'search' por 'where name ~' para permitir o 'sort'
            // O rating_count desc garante que os mais populares/conhecidos venham primeiro
            // Procure a linha $body dentro de search e deixe assim:
            $body = "fields name, cover.url, summary, involved_companies.company.name, first_release_date, rating_count, total_rating, platforms.name; 
         where name ~ *\"$query\"* & cover != null; 
         sort rating_count desc; 
         limit $perPage; 
         offset $offset;";

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withoutVerifying()->withHeaders([
                'Client-ID' => (string) env('IGDB_CLIENT_ID'),
                'Authorization' => 'Bearer ' . $token,
            ])->withBody($body, 'text/plain')->post('https://api.igdb.com/v4/games');

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function catalogo(Request $request)
    {
        try {
            $token = $this->getIgdbToken();
            $genreName = $request->query('genre', 'Mais Famosos');
            $letter = $request->query('letter');

            $perPage = 20;
            $page = (int) $request->query('page', 1);
            $offset = ($page - 1) * $perPage;

            $conditions = ["cover != null"];

            // 1. Se for "Mais Famosos" (Home do catálogo), mantém como está
            if ($genreName === 'Mais Famosos') {
                $sort = "sort rating_count desc;";
                $conditions[] = "rating_count != null";
            }
            // 2. Se for uma categoria específica (Ação, RPG, etc.)
            elseif ($genreName !== 'Todos') {
                $map = ['Ação' => 4, 'RPG' => 12, 'FPS' => 5, 'Estratégia' => 15, 'Indie' => 32, 'Aventura' => 31];
                if (isset($map[$genreName])) {
                    $conditions[] = "genres = ({$map[$genreName]})";
                }
                // ALTERAÇÃO AQUI: Mudamos de 'name asc' para 'rating_count desc'
                // Isso traz os jogos mais populares daquela categoria primeiro
                $sort = "sort rating_count desc;";
            }
            // 3. Se for "Todos", podemos manter alfabético ou por fama também
            else {
                $sort = "sort name asc;";
            }

            // Se o usuário clicar em uma letra, a ordem alfabética faz mais sentido
            if ($letter && ctype_alpha($letter)) {
                $upperLetter = strtoupper($letter);
                $conditions[] = "name ~ \"$upperLetter\"*";
                $sort = "sort name asc;";
            }

            $whereClause = "where " . implode(' & ', $conditions);

            // No método catalogo do seu GameController
            // No método catalogo do seu GameController
            $body = "fields name, cover.url, genres.name, total_rating, rating_count, summary, involved_companies.company.name, first_release_date, platforms.name; 
         $whereClause; 
         $sort 
         limit $perPage; 
         offset $offset;";

            /** @var Response $response */
            $response = Http::withoutVerifying()->withHeaders([
                'Client-ID' => (string) env('IGDB_CLIENT_ID'),
                'Authorization' => 'Bearer ' . $token,
            ])->withBody($body, 'text/plain')->post('https://api.igdb.com/v4/games');

            $games = $response->successful() ? $response->json() : [];

            return view('catalogo', compact('games', 'page', 'genreName', 'letter'));
        } catch (\Exception $e) {
            return view('catalogo', ['games' => [], 'page' => 1, 'genreName' => 'Mais Famosos', 'letter' => null]);
        }
    }

    private function getIgdbToken()
    {
        // Tenta recuperar do Cache. Se não existir, executa a função e guarda por 24 horas (86400 segundos)
        return cache()->remember('igdb_access_token', 86400, function () {
            /** @var Response $response */
            $response = Http::withoutVerifying()->post("https://id.twitch.tv/oauth2/token", [
                'client_id'     => env('IGDB_CLIENT_ID'),
                'client_secret' => env('IGDB_CLIENT_SECRET'),
                'grant_type'    => 'client_credentials',
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            throw new \Exception("Falha ao obter Token da IGDB. Verifique suas credenciais no .env");
        });
    }

    public function store(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'title' => [
                'required',
                Rule::unique('games')->where(fn($q) => $q->where('user_id', $userId)),
            ],
            'igdb_id' => 'required',
            'status' => 'required',
        ]);

        $game = Game::create([
            'user_id'      => $userId,
            'title'        => $request->title,
            'cover_url'    => $request->cover_url,
            'igdb_id'      => $request->igdb_id,
            'summary'      => $request->summary,
            'developer'    => $request->developer,
            'release_year' => $request->release_year,
            'platforms'    => $request->platforms,
            'status'       => $request->status,
            'rating'       => $request->rating,
            'review'       => $request->review,
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $xpGanho = ($request->status == 'zerado') ? 150 : 50;
        $upou = $user->addExp($xpGanho);

        return redirect()->back()->with('success', $upou ? 'LEVEL UP!' : 'Jogo adicionado!');
    }

    public function update(Request $request, Game $game)
    {
        if ($game->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required',
            'rating' => 'nullable|integer|min:0|max:10',
            'review' => 'nullable|string',
        ]);

        // Guarda o status antigo antes de atualizar
        $statusAntigo = $game->status;

        // Atualiza os dados
        $game->update([
            'status' => $request->status,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $mensagem = 'Progresso salvo!';

        // LÓGICA DE RECOMPENSA DINÂMICA
        // Se o jogo NÃO era zerado e AGORA FOI zerado, ganha +100 XP
        if ($statusAntigo !== 'zerado' && $request->status === 'zerado') {
            $user->addExp(100);
            $mensagem = 'Boa! Você zerou +1 jogo e ganhou 100 XP!';
        }

        // Se ele escreveu uma review pela primeira vez, ganha +30 XP
        if (empty($game->getOriginal('review')) && !empty($request->review)) {
            $user->addExp(30);
            $mensagem = 'Review publicada! +30 XP de Crítico Gamer!';
        }

        return redirect()->back()->with('success', $mensagem);
    }

    public function destroy(Game $game)
    {
        // 1. Segurança: Verifica se o jogo é do usuário logado
        // Usamos Auth::id() para ser mais explícito para o editor
        if ($game->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        // 2. Calcula quanto XP esse jogo valia
        $xpParaRemover = ($game->status === 'zerado') ? 150 : 50;

        if (!empty($game->review)) {
            $xpParaRemover += 30;
        }

    // 3. Remove o XP do usuário
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Agora o removeExp() será reconhecido sem erros
        $user->removeExp($xpParaRemover);

        // 4. Deleta o registro do banco
        $game->delete();

        return redirect()->back()->with('success', "Jogo removido! Você perdeu {$xpParaRemover} XP.");
    }

    private function traduzirTexto($texto)
    {
        if (empty($texto)) return $texto;

        try {
            // Usando uma API pública de tradução via GET (mais estável para PHP)
            $response = Http::get("https://translate.googleapis.com/translate_a/single", [
                'client' => 'gtx',
                'sl' => 'en',
                'tl' => 'pt',
                'dt' => 't',
                'q' => $texto,
            ]);

            if ($response->successful()) {
                $translation = $response->json();
                // O Google retorna um array complexo, precisamos montar as partes
                $frases = "";
                foreach ($translation[0] as $parte) {
                    $frases .= $parte[0];
                }
                return $frases;
            }
            return $texto;
        } catch (\Exception $e) {
            return "Erro técnico: " . $e->getMessage();
        }
    }

    // Método que o seu JavaScript (fetch) vai chamar no Modal
    public function traduzirNoModal(Request $request)
    {
        // Mudamos de 'texto' para 'text' para bater com o seu JavaScript
        $texto = $request->input('text');

        if (!$texto) {
            return response()->json(['traducao' => 'Texto não recebido']);
        }

        $resultado = $this->traduzirTexto($texto);
        return response()->json(['traducao' => $resultado]);
    }
}
