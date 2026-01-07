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



        .game-item {

            transition: opacity 0.3s ease, transform 0.3s ease;

        }



        .star-rating input:checked~label,

        .star-rating label:hover,

        .star-rating label:hover~label {

            filter: grayscale(0) opacity(1) drop-shadow(0 0 5px rgba(251, 191, 36, 0.5));

        }



        .custom-scrollbar::-webkit-scrollbar {

            width: 4px;

        }



        .custom-scrollbar::-webkit-scrollbar-track {

            background: #0f111a;

        }



        .custom-scrollbar::-webkit-scrollbar-thumb {

            background: #6366f1;

            border-radius: 10px;

        }
    </style>



    <x-slot name="header">

        <h2 class="font-bold text-2xl text-white leading-tight italic tracking-widest uppercase">

            {{ __('🕹️ Meu Painel de Controle') }}

        </h2>

    </x-slot>



    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-6">

        <div class="bg-[#1a1d2e] border border-gray-800 p-6 rounded-2xl shadow-xl">

            <div class="flex flex-col md:flex-row items-center gap-6">

                <div class="relative flex items-center justify-center">

                    <div class="w-16 h-16 bg-indigo-600 rotate-45 rounded-xl"></div>

                    <span class="absolute text-white font-black text-2xl italic">{{ auth()->user()->level }}</span>

                    <p class="absolute -bottom-2 text-[10px] text-indigo-400 font-black uppercase tracking-tighter">Nível</p>

                </div>



                <div class="flex-1 w-full">

                    <div class="flex justify-between mb-2">

                        <h3 class="text-white font-black uppercase italic tracking-widest text-lg">

                            {{ auth()->user()->name }}

                        </h3>

                        <span class="text-gray-500 font-bold text-xs uppercase">

                            {{ auth()->user()->xp % 1000 }} / 1000 XP para o próximo nível

                        </span>

                    </div>



                    <div class="w-full h-4 bg-[#0f111a] rounded-full border border-gray-800 overflow-hidden">

                        @php

                        // Calcula a porcentagem do progresso para o próximo nível (base 1000)

                        $percent = (auth()->user()->xp % 1000) / 10;

                        @endphp

                        <div class="h-full bg-gradient-to-r from-indigo-600 to-purple-500 shadow-[0_0_15px_rgba(79,70,229,0.5)] transition-all duration-1000"

                            style="width: {{ $percent }}%">

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

            <div class="bg-[#1a1d2e] border border-gray-800 p-4 rounded-xl flex items-center gap-3 group hover:border-indigo-500/50 transition-all">

                <div class="p-2 bg-indigo-500/10 rounded-lg text-indigo-500 group-hover:bg-indigo-500 group-hover:text-white transition-colors">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>

                    </svg>

                </div>

                <div>

                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-tighter">Jogos</p>

                    <h4 class="text-white text-xl font-black italic line-height-1">{{ $stats['total'] }}</h4>

                </div>

            </div>



            <div class="bg-[#1a1d2e] border border-gray-800 p-4 rounded-xl flex items-center gap-3 group hover:border-green-500/50 transition-all">

                <div class="p-2 bg-green-500/10 rounded-lg text-green-500 group-hover:bg-green-500 group-hover:text-white transition-colors">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"></path>

                    </svg>

                </div>

                <div>

                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-tighter">Zerados</p>

                    <h4 class="text-white text-xl font-black italic">{{ $stats['zerados'] }}</h4>

                </div>

            </div>



            <div class="bg-[#1a1d2e] border border-gray-800 p-4 rounded-xl flex items-center gap-3 group hover:border-orange-500/50 transition-all">

                <div class="p-2 bg-orange-500/10 rounded-lg text-orange-500 group-hover:bg-orange-500 group-hover:text-white transition-colors">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>

                    </svg>

                </div>

                <div>

                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-tighter">Jogando</p>

                    <h4 class="text-white text-xl font-black italic">{{ $stats['jogando'] }}</h4>

                </div>

            </div>



            <div class="bg-[#1a1d2e] border border-gray-800 p-4 rounded-xl flex items-center gap-3 group hover:border-red-500/50 transition-all">

                <div class="p-2 bg-red-500/10 rounded-lg text-red-500 group-hover:bg-red-500 group-hover:text-white transition-colors">

                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">

                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />

                    </svg>

                </div>

                <div>

                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-tighter">Favoritos</p>

                    <h4 class="text-white text-xl font-black italic">{{ $myGames->where('status', 'favorito')->count() }}</h4>

                </div>

            </div>

        </div>

    </div>



    <div class="py-6 bg-[#0f111a] min-h-screen">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">



            <div id="searchResults" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 px-4">

            </div>



            <div class="bg-[#1a1d2e] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-800 p-8">

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 border-b border-gray-800 pb-6 gap-4">

                    <div>

                        <h3 class="text-white text-2xl font-black uppercase tracking-tighter italic">Minha Biblioteca</h3>

                        <span class="bg-indigo-500 text-[10px] text-white px-3 py-1 rounded-full font-bold uppercase tracking-widest shadow-lg">Total: {{ count($myGames) }}</span>

                    </div>



                    <div class="flex flex-wrap gap-2">

                        <button onclick="filtrarBiblioteca('todos')" class="filter-btn active bg-indigo-600 text-white text-[10px] font-black px-4 py-2 rounded-lg uppercase transition-all" data-status="todos">Todos</button>

                        <button onclick="filtrarBiblioteca('jogando')" class="filter-btn bg-[#0f111a] text-gray-500 hover:text-white text-[10px] font-black px-4 py-2 rounded-lg uppercase transition-all" data-status="jogando">🎮 Jogando</button>

                        <button onclick="filtrarBiblioteca('zerado')" class="filter-btn bg-[#0f111a] text-gray-500 hover:text-white text-[10px] font-black px-4 py-2 rounded-lg uppercase transition-all" data-status="zerado">✅ Zerado</button>

                        <button onclick="filtrarBiblioteca('quero_jogar')" class="filter-btn bg-[#0f111a] text-gray-500 hover:text-white text-[10px] font-black px-4 py-2 rounded-lg uppercase transition-all" data-status="quero_jogar">⏳ Quero Jogar</button>

                        <button onclick="filtrarBiblioteca('favorito')" class="filter-btn bg-[#0f111a] text-gray-500 hover:text-white text-[10px] font-black px-4 py-2 rounded-lg uppercase transition-all" data-status="favorito">🔥 Favorito</button>

                    </div>

                </div>



                <div id="libraryGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8">

                    @forelse($myGames as $game)

                    <div class="game-item group bg-[#0f111a] rounded-2xl overflow-hidden border border-gray-800 hover:border-indigo-500 transition-all duration-300 hover:-translate-y-2 shadow-lg cursor-pointer"

                        data-status="{{ $game->status }}"

                        data-game="{{ json_encode($game) }}"

                        onclick="abrirModalEdicao(JSON.parse(this.dataset.game))">

                        <div class="aspect-[3/4] relative overflow-hidden">

                            <img src="{{ $game->cover_url }}" class="w-full h-full object-cover">

                            <div class="absolute top-2 right-2">

                                <span class="bg-indigo-600/90 text-[9px] font-black text-white px-2 py-1 rounded uppercase backdrop-blur-sm border border-white/10 italic">

                                    {{ str_replace('_', ' ', $game->status) }}

                                </span>

                            </div>

                        </div>

                        <div class="p-4 text-center">

                            <h4 class="text-white font-bold truncate text-sm mb-2 uppercase tracking-tighter">{{ $game->title }}</h4>

                            <span class="text-yellow-500 font-bold text-xs uppercase tracking-widest">{{ $game->rating }}/10</span>

                        </div>

                    </div>

                    @empty

                    <div class="col-span-full py-20 text-center uppercase tracking-widest text-gray-600">Sua jornada começa aqui.</div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>



    @include('components.modal-game-engine')



    <script>
        // --- LOGICA DE FILTRO DA BIBLIOTECA ---

        function filtrarBiblioteca(status) {

            const cards = document.querySelectorAll('.game-item');

            const buttons = document.querySelectorAll('.filter-btn');



            buttons.forEach(btn => {

                if (btn.getAttribute('data-status') === status) {

                    btn.className = "filter-btn bg-indigo-600 text-white text-[10px] font-black px-4 py-2 rounded-lg uppercase transition-all";

                } else {

                    btn.className = "filter-btn bg-[#0f111a] text-gray-500 hover:text-white text-[10px] font-black px-4 py-2 rounded-lg uppercase transition-all";

                }

            });



            cards.forEach(card => {

                const gameStatus = card.getAttribute('data-status');

                if (status === 'todos' || gameStatus === status) {

                    card.style.display = 'block';

                    setTimeout(() => card.style.opacity = '1', 10);

                } else {

                    card.style.opacity = '0';

                    setTimeout(() => card.style.display = 'none', 300);

                }

            });

        }



        // --- LOGICA DE BUSCA (IGDB) ---

        let currentSearchPage = 1;

        let lastQuery = "";



        async function buscarJogosPelaNav() {

            const navInput = document.getElementById('navGameQuery');

            if (!navInput.value) return;

            if (window.location.pathname !== '/dashboard') {

                window.location.href = `/dashboard?search=${encodeURIComponent(navInput.value)}`;

                return;

            }

            ejecutarBusca(navInput.value);

        }



        async function ejecutarBusca(query, page = 1) {

            const resultsDiv = document.getElementById('searchResults');

            if (!query) return;

            lastQuery = query;

            currentSearchPage = page;



            resultsDiv.innerHTML = `

                <div class="col-span-full flex flex-col items-center justify-center py-20">

                    <div class="animate-spin w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full mb-4"></div>

                    <p class="text-indigo-500 font-black uppercase tracking-widest animate-pulse text-xs">Buscando na Database (Pág. ${page})...</p>

                </div>`;



            resultsDiv.scrollIntoView({

                behavior: 'smooth',

                block: 'start'

            });



            try {

                const response = await fetch(`/buscar-jogo?q=${encodeURIComponent(query)}&page=${page}`);

                const games = await response.json();

                resultsDiv.innerHTML = '';



                if (!games || games.length === 0) {

                    resultsDiv.innerHTML = '<div class="col-span-full text-center py-10"><p class="text-gray-500 uppercase font-black italic">Nenhum resultado encontrado.</p><button onclick="limparBusca()" class="text-indigo-500 text-[10px] font-bold mt-4 uppercase">Voltar</button></div>';

                    return;

                }



                games.forEach(game => {

                    const cover = game.cover ? game.cover.url.replace('t_thumb', 't_cover_big') : '//via.placeholder.com/264x352?text=Sem+Capa';

                    const title = game.name.replace(/['"]/g, '');

                    const summary = game.summary ? game.summary.replace(/['"]/g, '').replace(/[\r\n]+/g, ' ').trim() : 'Sinopse não disponível.';

                    const developer = game.involved_companies ? game.involved_companies[0].company.name.replace(/['"]/g, '') : 'Não informada';

                    const year = game.first_release_date ? new Date(game.first_release_date * 1000).getFullYear() : 'N/A';



                    const gameData = JSON.stringify({

                        title: title,

                        cover_url: `https:${cover}`,

                        summary: summary,

                        developer: developer,

                        release_year: year,

                        igdb_id: game.id,

                        first_release_date: game.first_release_date || ''

                    }).replace(/"/g, '&quot;');



                    resultsDiv.innerHTML += `

                        <div class="bg-[#1a1d2e] p-4 rounded-2xl border border-gray-800 hover:border-indigo-500 cursor-pointer transition-all duration-300 hover:-translate-y-2 shadow-xl group"

                             onclick="abrirModalParaAdicionar('${gameData}')">

                            <div class="relative aspect-[3/4] mb-4 overflow-hidden rounded-xl">

                                <img src="https:${cover}" class="w-full h-full object-cover">

                                <div class="absolute inset-0 bg-indigo-600/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">

                                    <span class="bg-white text-indigo-600 text-[10px] font-black px-3 py-2 rounded-lg shadow-2xl uppercase italic">+ Adicionar</span>

                                </div>

                            </div>

                            <h4 class="text-white text-center font-bold text-[11px] truncate uppercase italic tracking-tighter">${title}</h4>

                        </div>`;

                });



                // Paginação e Botão Fechar

                resultsDiv.innerHTML += `

                    <div class="col-span-full flex flex-col items-center gap-6 mt-10 pb-10 border-b border-gray-800">

                        <div class="flex items-center gap-4">

                            <button onclick="ejecutarBusca('${lastQuery}', ${page - 1})" ${page <= 1 ? 'disabled class="opacity-20 cursor-not-allowed text-gray-500"' : 'class="bg-[#0f111a] border border-gray-800 text-white font-black text-[10px] px-6 py-3 rounded-xl hover:bg-gray-800 transition uppercase"'}>← Anterior</button>

                            <span class="text-indigo-500 font-black text-xs italic tracking-[0.2em] bg-indigo-500/5 px-5 py-3 rounded-xl border border-indigo-500/20">PÁGINA ${page}</span>

                            <button onclick="ejecutarBusca('${lastQuery}', ${page + 1})" class="bg-indigo-600 hover:bg-indigo-500 text-white font-black text-[10px] px-6 py-3 rounded-xl shadow-lg transition uppercase">Próxima →</button>

                        </div>

                        <button onclick="limparBusca()" class="text-gray-500 hover:text-white text-[9px] font-black uppercase tracking-widest transition">✖ Fechar Resultados</button>

                    </div>`;



            } catch (error) {

                resultsDiv.innerHTML = '<p class="text-red-500 text-center col-span-full font-black py-10">Erro na conexão com a API.</p>';

            }

        }



        function limparBusca() {

            document.getElementById('searchResults').innerHTML = '';

            document.getElementById('navGameQuery').value = '';

            document.getElementById('libraryGrid').scrollIntoView({

                behavior: 'smooth'

            });

        }



        window.addEventListener('DOMContentLoaded', () => {

            const urlParams = new URLSearchParams(window.location.search);

            const searchQuery = urlParams.get('search');

            if (searchQuery) {

                document.getElementById('navGameQuery').value = searchQuery;

                ejecutarBusca(searchQuery);

            }

        });



        // --- MODAL E EDIÇÃO ---

        let sinopseOriginalDash = "";



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

            const methodInput = document.getElementById('formMethod');

            if (methodInput) methodInput.disabled = true;



            document.getElementById('btnSalvar').innerText = "Adicionar à Coleção";

            document.getElementById('btnDeleteTrigger').classList.add('hidden');

            mudarEstiloBotaoDash('en');

            document.getElementById('modalEdicao').classList.remove('hidden');

        }



        function abrirModalEdicao(game) {

            const form = document.getElementById('editForm');

            const methodInput = document.getElementById('formMethod');

            if (methodInput) {

                methodInput.value = 'PATCH';

                methodInput.disabled = false;

            }



            sinopseOriginalDash = game.summary || "Nenhuma sinopse disponível.";

            document.getElementById('modalTitle').innerText = game.title;

            document.getElementById('modalCover').src = game.cover_url;

            document.getElementById('modalSummary').innerText = sinopseOriginalDash;

            document.getElementById('modalDeveloper').innerText = game.developer || "---";

            document.getElementById('modalYear').innerText = `Lançamento: ${game.release_year || 'N/A'}`;



            document.getElementById('inputSummary').value = sinopseOriginalDash;

            document.getElementById('editStatus').value = game.status;

            document.getElementById('editReview').value = game.review || '';



            document.querySelectorAll('.star-rating input').forEach(el => el.checked = false);

            if (game.rating) {

                const star = document.getElementById(`star${game.rating}`);

                if (star) star.checked = true;

            }



            form.action = `/games/${game.id}`;

            document.getElementById('deleteForm').action = `/games/${game.id}`;

            document.getElementById('btnSalvar').innerText = "Salvar Alterações";

            document.getElementById('btnDeleteTrigger').classList.remove('hidden');

            mudarEstiloBotaoDash('en');

            document.getElementById('modalEdicao').classList.remove('hidden');

        }



        async function traduzirNoDashboard(lang) {
            const summaryElement = document.getElementById('modalSummary');
            const inputSummary = document.getElementById('inputSummary');

            if (!summaryElement) return;

            if (lang === 'en') {
                summaryElement.innerText = sinopseOriginalDash;
                inputSummary.value = sinopseOriginalDash;
                mudarEstiloBotaoDash('en');
                return;
            }

            mudarEstiloBotaoDash('pt');
            summaryElement.innerHTML = '<span class="animate-pulse text-indigo-400 italic">Traduzindo sinopse...</span>';

            try {
                const response = await fetch('/traduzir', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Obrigatório para rotas POST no Laravel
                    },
                    body: JSON.stringify({
                        texto: sinopseOriginalDash
                    })
                });

                if (!response.ok) throw new Error("Erro no servidor");

                const data = await response.json();
                const traducao = data.traducao || sinopseOriginalDash;

                summaryElement.innerText = traducao;
                inputSummary.value = traducao; // Garante que a tradução seja salva no banco

            } catch (error) {
                console.error("Erro na tradução:", error);
                summaryElement.innerText = sinopseOriginalDash;
                inputSummary.value = sinopseOriginalDash;
            }
        }



        function mudarEstiloBotaoDash(lang) {

            const btnEn = document.getElementById('btn-dash-en');

            const btnPt = document.getElementById('btn-dash-pt');

            if (btnEn && btnPt) {

                btnEn.className = lang === 'en' ? "text-[10px] px-2 py-0.5 rounded font-bold bg-indigo-600 text-white transition" : "text-[10px] px-2 py-0.5 rounded font-bold text-gray-500 hover:text-white transition";

                btnPt.className = lang === 'pt' ? "text-[10px] px-2 py-0.5 rounded font-bold bg-indigo-600 text-white transition" : "text-[10px] px-2 py-0.5 rounded font-bold text-gray-500 hover:text-white transition";

            }

        }



        function fecharModal() {

            document.getElementById('modalEdicao').classList.add('hidden');

        }



        function confirmarExclusao() {

            if (confirm('Remover da biblioteca?')) document.getElementById('deleteForm').submit();

        }
    </script>

</x-app-layout>