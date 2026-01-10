<x-app-layout>

    <x-slot name="header">
        <h2 class="font-black text-2xl text-white leading-tight uppercase italic tracking-tighter">
            {{ __('🎮 Catálogo de Games') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#0f111a] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Categorias --}}
            <div class="bg-[#1a1d2e] p-3 rounded-2xl border border-gray-800 shadow-2xl flex flex-wrap gap-2 justify-center">
                @php $categories = ['Mais Famosos', 'Todos', 'Ação', 'RPG', 'FPS', 'Indie', 'Estratégia']; @endphp
                @foreach($categories as $cat)
                <a href="{{ route('catalogo', ['genre' => $cat]) }}"
                    class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] transition-all
                    {{ ($genreName == $cat) ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/40' : 'text-gray-500 hover:text-white hover:bg-white/5' }}">
                    {{ $cat }}
                </a>
                @endforeach
            </div>

            {{-- Search Bar --}}
            <div class="relative group max-w-2xl mx-auto">
                <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-1000"></div>
                <div class="relative flex items-center bg-[#1a1d2e] rounded-2xl border border-gray-700/50 overflow-hidden shadow-2xl">
                    <div class="pl-5 text-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text"
                        id="mainSearchInput"
                        onkeyup="debounceSearch(this.value)"
                        placeholder="PROCURAR UM TÍTULO ESPECÍFICO NA IGDB..."
                        class="w-full bg-transparent border-none text-white text-xs font-black p-5 focus:ring-0 placeholder:text-gray-600 uppercase tracking-widest italic">

                    <div id="searchLoader" class="hidden pr-5">
                        <div class="animate-spin h-4 w-4 border-2 border-indigo-500 border-t-transparent rounded-full"></div>
                    </div>
                </div>
            </div>

            {{-- Grid de Resultados --}}
            <div class="bg-[#1a1d2e] p-6 rounded-3xl border border-gray-800 shadow-2xl">
                <div id="searchResults" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @forelse ($games as $game)
                    {{-- Logica de processamento de dados do game mantida --}}
                    @php
                    $coverUrl = isset($game['cover']['url']) ? str_replace('t_thumb', 't_cover_big', $game['cover']['url']) : 'https://via.placeholder.com/400x600?text=Sem+Capa';
                    if (strpos($coverUrl, '//') === 0) $coverUrl = 'https:' . $coverUrl;
                    $platNames = isset($game['platforms']) ? array_column($game['platforms'], 'name') : [];
                    $platforms = !empty($platNames) ? implode(', ', $platNames) : 'N/A';
                    $rating = isset($game['total_rating']) ? round($game['total_rating']) : 0;

                    $gameData = json_encode([
                    'title' => str_replace('"', '', $game['name']),
                    'cover_url' => $coverUrl,
                    'summary' => str_replace(["\r", "\n", '"'], '', $game['summary'] ?? 'Sem sinopse.'),
                    'developer' => str_replace('"', '', $game['involved_companies'][0]['company']['name'] ?? 'N/A'),
                    'release_year' => isset($game['first_release_date']) ? date('Y', $game['first_release_date']) : 'N/A',
                    'igdb_id' => $game['id'],
                    'first_release_date' => $game['first_release_date'] ?? '',
                    'platforms' => $platforms,
                    'rating' => $rating
                    ]);
                    @endphp

                    <div onclick="abrirModalParaAdicionar('{{ addslashes($gameData) }}')"
                        class="group bg-[#0f111a] rounded-xl overflow-hidden border border-gray-800 hover:border-indigo-500 transition-all duration-300 hover:-translate-y-1 shadow-lg cursor-pointer flex flex-col relative">
                        <div class="aspect-[3/4] relative overflow-hidden bg-black">
                            <img src="{{ $coverUrl }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @if($rating)
                            <div class="rating-badge-container">
                                <div class="rating-circle">
                                    <span class="rating-value">{{ $rating }}</span>
                                    <span class="rating-label">IGDB</span>
                                </div>
                            </div>
                            @endif
                            <div class="absolute inset-0 bg-indigo-600/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <span class="bg-white text-indigo-600 text-[9px] font-black px-2 py-1 rounded uppercase italic shadow-2xl">+ Adicionar</span>
                            </div>
                        </div>
                        <div class="p-2 text-center">
                            <h4 class="text-white font-bold truncate text-[11px] uppercase tracking-tighter italic">{{ $game['name'] }}</h4>
                            <p class="text-gray-500 font-black text-[9px] uppercase tracking-widest mt-1">
                                {{ isset($game['first_release_date']) ? date('Y', $game['first_release_date']) : 'N/A' }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-20 text-center uppercase tracking-widest text-gray-600 font-bold">Nenhum game encontrado.</div>
                    @endforelse
                </div>

                {{-- Paginação --}}
                @if(!request('search'))
                <div id="paginationNav" class="mt-10 flex justify-center items-center gap-4">
                    @if($page > 1)
                    <a href="?page={{ $page - 1 }}&genre={{ $genreName }}" class="bg-gray-800 text-white text-[10px] font-bold px-4 py-2 rounded-lg hover:bg-gray-700 transition">ANTERIOR</a>
                    @endif
                    <span class="text-indigo-500 font-black text-xs uppercase tracking-widest italic">Página {{ $page }}</span>
                    <a href="?page={{ $page + 1 }}&genre={{ $genreName }}" class="bg-indigo-600 text-white text-[10px] font-bold px-4 py-2 rounded-lg hover:bg-indigo-500 transition shadow-lg shadow-indigo-500/20">PRÓXIMA</a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Componente do Modal compartilhado --}}
    @include('components.modal-game-engine')

    {{-- Configurações Globais para os scripts JS --}}
    <script>
        window.CatalogoConfig = {
            csrfToken: '{{ csrf_token() }}',
            routeStore: '{{ route("games.store") }}',
            routeTraduzir: '/traduzir',
            routeSearch: '{{ route("catalogo.search") }}'
        };
    </script>

    {{-- CHAMADA VITE CORRIGIDA: Adicionamos o game-logic aqui também --}}
    @vite(['resources/js/game-logic.js'])
</x-app-layout>
