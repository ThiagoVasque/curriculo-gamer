<x-app-layout>
    <style>
        /* Sistema de Meias Estrelas (Rating 0-10) */
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            cursor: pointer;
            width: 18px;
            height: 35px;
            background-repeat: no-repeat;
            background-size: 35px 35px;
            transition: 0.2s;
            filter: grayscale(1) opacity(0.3);
        }

        .star-rating .half {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23fbbf24'%3E%3Cpath d='M12 2L9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27V2z'/%3E%3C/svg%3E");
            background-position: left;
        }

        .star-rating .full {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23fbbf24'%3E%3Cpath d='M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2v15.27z'/%3E%3C/svg%3E");
            background-position: right;
            margin-right: 5px;
        }

        .star-rating input:checked~label,
        .star-rating label:hover,
        .star-rating label:hover~label {
            filter: grayscale(0) opacity(1) drop-shadow(0 0 5px rgba(251, 191, 36, 0.5));
        }
    </style>

    <x-slot name="header">
        <h2 class="font-black text-2xl text-white leading-tight uppercase italic tracking-tighter">
            {{ __('🎮 Catálogo de Games') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#0f111a] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            <div class="bg-[#1a1d2e] p-6 rounded-3xl border border-gray-800 shadow-2xl">
                <div id="searchResults" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @forelse ($games as $game)
                    @php
                    $coverUrl = isset($game['cover']['url']) ? str_replace('t_thumb', 't_cover_big', $game['cover']['url']) : 'https://via.placeholder.com/400x600?text=Sem+Capa';
                    if (strpos($coverUrl, '//') === 0) $coverUrl = 'https:' . $coverUrl;

                    $gameData = json_encode([
                    'title' => str_replace('"', '', $game['name']),
                    'cover_url' => $coverUrl,
                    'summary' => str_replace(["\r", "\n", '"'], '', $game['summary'] ?? 'Sem sinopse.'),
                    'developer' => str_replace('"', '', $game['involved_companies'][0]['company']['name'] ?? 'N/A'),
                    'release_year' => isset($game['first_release_date']) ? date('Y', $game['first_release_date']) : 'N/A',
                    'igdb_id' => $game['id'],
                    'first_release_date' => $game['first_release_date'] ?? ''
                    ]);
                    @endphp

                    <div onclick="abrirModalParaAdicionar('{{ addslashes($gameData) }}')"
                        class="group bg-[#0f111a] rounded-xl overflow-hidden border border-gray-800 hover:border-indigo-500 transition-all duration-300 hover:-translate-y-1 shadow-lg cursor-pointer flex flex-col">
                        <div class="aspect-[3/4] relative overflow-hidden bg-black">
                            <img src="{{ $coverUrl }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
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

    @include('components.modal-game-engine')

    <script>
        let sinopseOriginalDash = "";
        // Variáveis globais para rastrear a busca atual
        let currentQuery = "";
        let currentSearchPage = 1;

        // Função atualizada para aceitar a página
        async function ejecutarBusca(query, page = 1) {
            const resultsDiv = document.getElementById('searchResults');
            const paginationStatic = document.getElementById('paginationNav');

            if (!query) return;

            currentQuery = query;
            currentSearchPage = page;

            // Esconder paginação estática (Blade)
            if (paginationStatic) paginationStatic.classList.add('hidden');

            // Loader
            resultsDiv.innerHTML = `
            <div class="col-span-full flex flex-col items-center justify-center py-20">
                <div class="animate-spin w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full mb-4"></div>
                <p class="text-indigo-500 font-black uppercase tracking-widest animate-pulse text-[10px]">Consultando IGDB (Pág ${page})...</p>
            </div>`;

            try {
                // Envia a página para o Controller
                const response = await fetch(`/buscar-jogo?q=${encodeURIComponent(query)}&page=${page}`);
                const games = await response.json();
                resultsDiv.innerHTML = '';

                if (!games || games.length === 0) {
                    resultsDiv.innerHTML = '<p class="text-gray-500 text-center col-span-full py-10 uppercase font-black text-xs italic">Fim dos resultados.</p>';
                    return;
                }

                // Renderiza os Cards
                games.forEach(game => {
                    const cover = game.cover ? game.cover.url.replace('t_thumb', 't_cover_big') : '//via.placeholder.com/264x352?text=Sem+Capa';
                    const title = game.name.replace(/['"]/g, '');
                    const year = game.first_release_date ? new Date(game.first_release_date * 1000).getFullYear() : 'N/A';

                    const gameData = JSON.stringify({
                        title: title,
                        cover_url: `https:${cover}`,
                        summary: (game.summary || '').replace(/['"]/g, '').replace(/[\r\n]+/g, ' '),
                        developer: game.involved_companies ? game.involved_companies[0].company.name : 'N/A',
                        release_year: year,
                        igdb_id: game.id,
                        first_release_date: game.first_release_date || ''
                    }).replace(/"/g, '&quot;');

                    resultsDiv.innerHTML += `
                    <div onclick="abrirModalParaAdicionar('${gameData}')"
                         class="group bg-[#0f111a] rounded-xl overflow-hidden border border-gray-800 hover:border-indigo-500 transition-all duration-300 hover:-translate-y-1 shadow-lg cursor-pointer flex flex-col">
                        <div class="aspect-[3/4] relative overflow-hidden bg-black">
                            <img src="https:${cover}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-indigo-600/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <span class="bg-white text-indigo-600 text-[9px] font-black px-2 py-1 rounded uppercase italic shadow-2xl">+ Adicionar</span>
                            </div>
                        </div>
                        <div class="p-2 text-center">
                            <h4 class="text-white font-bold truncate text-[11px] uppercase tracking-tighter italic">${title}</h4>
                            <p class="text-gray-500 font-black text-[9px] uppercase tracking-widest mt-1">${year}</p>
                        </div>
                    </div>`;
                });

                // INSERIR PAGINAÇÃO DINÂMICA APÓS OS CARDS
                resultsDiv.innerHTML += `
                <div class="col-span-full flex justify-center items-center gap-6 mt-12 pb-10">
                    <button onclick="ejecutarBusca('${currentQuery}', ${page - 1})" 
                        ${page <= 1 ? 'disabled class="opacity-20 cursor-not-allowed"' : 'class="bg-gray-800 text-white font-black text-[10px] px-6 py-3 rounded-xl hover:bg-gray-700 transition"'} >
                        ← ANTERIOR
                    </button>
                    
                    <span class="text-indigo-500 font-black text-xs italic tracking-widest bg-indigo-500/10 px-4 py-2 rounded-lg border border-indigo-500/20">
                        PÁGINA ${page}
                    </span>

                    <button onclick="ejecutarBusca('${currentQuery}', ${page + 1})" 
                        class="bg-indigo-600 hover:bg-indigo-500 text-white font-black text-[10px] px-6 py-3 rounded-xl shadow-lg shadow-indigo-600/20 transition">
                        PRÓXIMA →
                    </button>
                </div>
            `;

                // Scroll suave para o topo dos resultados ao trocar de página
                window.scrollTo({
                    top: resultsDiv.offsetTop - 100,
                    behavior: 'smooth'
                });

            } catch (error) {
                console.error(error);
                resultsDiv.innerHTML = '<p class="text-red-500 text-center col-span-full py-10">Erro ao carregar resultados.</p>';
            }
        }

        // Modal, Fechar e Init (Mesma lógica sua)
        function abrirModalParaAdicionar(gameJson) {
            const game = JSON.parse(gameJson);
            const form = document.getElementById('editForm');
            form.reset();

            document.getElementById('inputTitle').value = game.title;
            document.getElementById('inputCover').value = game.cover_url;
            document.getElementById('inputIgdbId').value = game.igdb_id;
            document.getElementById('inputDeveloper').value = game.developer;
            document.getElementById('inputRelease').value = game.first_release_date;
            document.getElementById('inputSummary').value = game.summary;

            sinopseOriginalDash = game.summary;
            document.getElementById('modalTitle').innerText = game.title;
            document.getElementById('modalCover').src = game.cover_url;
            document.getElementById('modalSummary').innerText = game.summary;
            document.getElementById('modalDeveloper').innerText = game.developer;
            document.getElementById('modalYear').innerText = `Lançamento: ${game.release_year}`;

            form.action = "{{ route('games.store') }}";
            const patchInput = form.querySelector('input[name="_method"]');
            if (patchInput) patchInput.disabled = true;

            document.getElementById('btnSalvar').innerText = "Adicionar à Coleção";
            document.getElementById('btnDeleteTrigger').classList.add('hidden');
            document.getElementById('modalEdicao').classList.remove('hidden');
        }

        function fecharModal() {
            document.getElementById('modalEdicao').classList.add('hidden');
        }

        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const searchQuery = urlParams.get('search');
            if (searchQuery) {
                if (document.getElementById('navGameQuery')) document.getElementById('navGameQuery').value = searchQuery;
                ejecutarBusca(searchQuery);
            }
        });
    </script>
</x-app-layout>