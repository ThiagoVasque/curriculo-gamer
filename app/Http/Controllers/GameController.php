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
        $myGames = $user->games()->latest()->get();

        // Lógica de amigos revisada e segura
        $amigos = \App\Models\Friendship::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhere('friend_id', $user->id);
        })
            ->where('status', 'accepted')
            ->with(['user', 'friend']) // Eager loading para evitar consultas extras
            ->get()
            ->map(function ($f) use ($user) {
                // Retorna o objeto do amigo, garantindo que não seja nulo
                return $f->user_id === $user->id ? $f->friend : $f->user;
            })
            ->filter(); // Remove qualquer valor nulo que possa ter sobrado

        $stats = [
            'total' => $myGames->count(),
            'zerados' => $myGames->where('status', 'zerado')->count(),
            'jogando' => $myGames->where('status', 'jogando')->count(),
            'amigos_count' => $amigos->count(),
        ];

        return view('dashboard', compact('myGames', 'stats', 'amigos'));
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
        $query = $request->input('search');
        $page = (int) $request->input('page', 1);
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        if (!$query) return response()->json([]);

        try {
            $token = $this->getIgdbToken();

            // Corpo da requisição sem quebras de linha para evitar erro de sintaxe na IGDB
            $body = "fields name, cover.url, summary, involved_companies.company.name, first_release_date, rating_count, total_rating, platforms.name; where name ~ *\"$query\"* & cover != null; sort rating_count desc; limit $perPage; offset $offset;";

            /** @var Response $response */
            $response = Http::withoutVerifying()->withHeaders([
                'Client-ID' => (string) env('IGDB_CLIENT_ID'),
                'Authorization' => 'Bearer ' . $token,
            ])->withBody($body, 'text/plain')->post('https://api.igdb.com/v4/games');

            // Agora o editor reconhecerá failed(), status(), etc.
            if ($response->failed()) {
                return response()->json([
                    'error' => 'Erro na IGDB',
                    'details' => $response->body()
                ], $response->status());
            }

            $data = $response->json();

            return response()->json(is_array($data) ? $data : []);
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

        // --- CORREÇÃO AQUI ---
        // Em vez de: ($request->status == 'zerado') ? 150 : 50;
        // Usamos o método centralizado do Model:
        $xpGanho = $game->getXpValue();

        $upou = $user->addExp($xpGanho);

        return redirect()->back()->with('success', $upou ? "LEVEL UP! +{$xpGanho} XP" : "Jogo adicionado! +{$xpGanho} XP");
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

        // 1. Captura o valor de XP ATUAL do jogo antes da mudança
        $xpAntigo = $game->getXpValue();

        // 2. Atualiza os dados no banco
        $game->update([
            'status' => $request->status,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        // 3. Captura o NOVO valor de XP do jogo (o Model já recalcula com o novo status/review)
        $game->refresh(); // Garante que o Model pegue os dados recém-salvos
        $xpNovo = $game->getXpValue();

        // 4. Calcula a diferença
        $diferencaXP = $xpNovo - $xpAntigo;

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($diferencaXP !== 0) {
            // Se for positivo, soma. Se for negativo, o addExp subtrai.
            $user->addExp($diferencaXP);
        }

        $mensagem = $diferencaXP >= 0
            ? "Progresso salvo! +{$diferencaXP} XP obtido."
            : "Progresso atualizado! XP ajustado em {$diferencaXP}.";

        return redirect()->back()->with('success', $mensagem);
    }

    public function destroy(Game $game)
    {
        if ($game->user_id !== Auth::id()) {
            abort(403);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Pergunta ao model quanto o jogo vale no momento da exclusão
        $xpParaRemover = $game->getXpValue();

        // 2. Remove o XP total (Status + Review)
        $user->removeExp($xpParaRemover);

        // 3. Deleta
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
