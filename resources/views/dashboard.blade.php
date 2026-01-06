<x-app-layout>
    <style>
        /* Sistema de Meias Estrelas (Rating 0-10) */
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }
        .star-rating input { display: none; }
        .star-rating label {
            cursor: pointer;
            width: 18px; /* Metade da largura original */
            height: 35px;
            background-repeat: no-repeat;
            background-size: 35px 35px; /* Tamanho da estrela cheia */
            transition: 0.2s;
            filter: grayscale(1) opacity(0.3);
        }

        /* Lado Esquerdo da Estrela (Meia) */
        .star-rating .half {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23fbbf24'%3E%3Cpath d='M12 2L9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27V2z'/%3E%3C/svg%3E");
            background-position: left;
        }

        /* Lado Direito da Estrela (Inteira) */
        .star-rating .full {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23fbbf24'%3E%3Cpath d='M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2v15.27z'/%3E%3C/svg%3E");
            background-position: right;
            margin-right: 5px;
        }

        /* Lógica de Cores (Hover e Checked) */
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            filter: grayscale(0) opacity(1) drop-shadow(0 0 5px rgba(251, 191, 36, 0.5));
        }
    </style>

    <x-slot name="header">
        <h2 class="font-bold text-2xl text-white leading-tight italic tracking-widest uppercase">
            {{ __('🕹️ Meu Painel de Controle') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#0f111a] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-[#1a1d2e] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-800 p-8">
                <h3 class="text-white text-xl font-bold mb-6 flex items-center gap-2 italic uppercase">
                    <span class="text-indigo-500">#</span> Adicionar Novo Game
                </h3>
                <div class="flex gap-4">
                    <input type="text" id="gameQuery" onkeypress="if(event.key === 'Enter') buscarJogos()" 
                        placeholder="Qual jogo você zerou hoje?" 
                        class="w-full rounded-xl bg-[#0f111a] text-white border-2 border-gray-700 focus:border-indigo-500 transition-all py-3 px-6 text-lg">
                    <button onclick="buscarJogos()" class="bg-indigo-600 hover:bg-indigo-500 text-white px-8 py-3 rounded-xl font-black transition-all shadow-lg uppercase">Buscar</button>
                </div>
                <div id="searchResults" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mt-8"></div>
            </div>

            <div class="bg-[#1a1d2e] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-800 p-8">
                <div class="flex justify-between items-center mb-8 border-b border-gray-800 pb-4">
                    <h3 class="text-white text-2xl font-black uppercase tracking-tighter italic">Minha Biblioteca</h3>
                    <span class="bg-indigo-500 text-xs text-white px-4 py-1.5 rounded-full font-bold uppercase tracking-widest shadow-lg">Total: {{ count($myGames) }}</span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8">
                    @forelse($myGames as $game)
                        <div class="group bg-[#0f111a] rounded-2xl overflow-hidden border border-gray-800 hover:border-indigo-500 transition-all duration-300 hover:-translate-y-2 shadow-lg cursor-pointer" 
                             onclick="abrirModalEdicao({{ json_encode($game) }})">
                            <div class="aspect-[3/4] relative overflow-hidden">
                                <img src="{{ $game->cover_url }}" class="w-full h-full object-cover">
                                <div class="absolute top-2 right-2">
                                    <span class="bg-indigo-600/90 text-[9px] font-black text-white px-2 py-1 rounded uppercase backdrop-blur-sm border border-white/10 italic">
                                        {{ str_replace('_', ' ', $game->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h4 class="text-white font-bold truncate text-sm mb-2 uppercase tracking-tighter">{{ $game->title }}</h4>
                                <div class="flex justify-between items-center">
                                    <div class="flex text-yellow-400">
                                        @for ($i = 1; $i <= 10; $i++)
                                            @if($i % 2 != 0) <div class="relative w-3.5 h-3.5">
                                                    <svg class="absolute w-full h-full text-gray-800" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                    
                                                    @if($game->rating >= $i && $game->rating < $i + 1)
                                                        <div class="absolute w-1/2 h-full overflow-hidden text-yellow-400">
                                                            <svg class="w-[14px] h-[14px]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                        </div>
                                                    @endif

                                                    @if($game->rating >= $i + 1)
                                                        <svg class="absolute w-full h-full text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                                    @endif
                                                </div>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-gray-500 text-[10px] font-bold italic">{{ $game->rating }}/10</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center uppercase tracking-widest text-gray-600">Sua jornada começa aqui.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div id="modalEdicao" class="hidden fixed inset-0 bg-black/95 backdrop-blur-md z-50 flex items-center justify-center p-4">
        <div class="bg-[#1a1d2e] w-full max-w-lg rounded-3xl border border-gray-700 p-8 shadow-2xl relative">
            <div class="flex justify-between items-center mb-6 border-b border-gray-800 pb-4">
                <h3 id="modalTitle" class="text-white text-2xl font-black italic uppercase text-indigo-500">Editar Jogo</h3>
                <button onclick="fecharModal()" class="text-gray-500 hover:text-white transition text-3xl">&times;</button>
            </div>

            <form id="editForm" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')
                
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-1">
                        <label class="text-gray-500 text-[10px] font-bold uppercase mb-2 block tracking-widest">Status</label>
                        <select name="status" id="editStatus" class="w-full bg-[#0f111a] border-gray-700 rounded-xl text-white text-sm focus:ring-indigo-500 uppercase font-bold">
                            <option value="zerado">✅ Zerado</option>
                            <option value="quero_jogar">⏳ Quero Jogar</option>
                            <option value="favorito">🔥 Favorito</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="text-gray-500 text-[10px] font-bold uppercase mb-2 block tracking-widest text-center">Nota (1 a 10)</label>
                        <div class="star-rating flex-row-reverse justify-center">
                            @for ($i = 10; $i >= 1; $i--)
                                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" />
                                <label for="star{{ $i }}" class="{{ $i % 2 == 0 ? 'full' : 'half' }}"></label>
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="text-gray-500 text-[10px] font-bold uppercase mb-2 block tracking-widest">Ano de Conclusão</label>
                        <input type="number" name="year_completed" id="editYear" class="w-full bg-[#0f111a] border-gray-700 rounded-xl text-white text-sm focus:ring-indigo-500" placeholder="Ex: 2024">
                    </div>
                    <div>
                        <label class="text-gray-500 text-[10px] font-bold uppercase mb-2 block tracking-widest">Sua Experiência (Review)</label>
                        <textarea name="review" id="editReview" rows="3" class="w-full bg-[#0f111a] border-gray-700 rounded-xl text-white text-sm focus:ring-indigo-500 resize-none" placeholder="O que achou desse game?"></textarea>
                    </div>
                </div>

                <div class="flex gap-4 pt-4 border-t border-gray-800">
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white font-black py-4 rounded-2xl transition uppercase tracking-widest shadow-lg">Atualizar</button>
            </form>
            
            <form id="deleteForm" method="POST" onsubmit="return confirm('Apagar para sempre?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600/10 hover:bg-red-600 text-red-500 hover:text-white px-6 py-4 rounded-2xl transition border border-red-600/20">🗑️</button>
            </form>
                </div>
        </div>
    </div>

    <script>
        // ... (Mesma lógica de buscarJogos anterior) ...
        async function buscarJogos() {
            const query = document.getElementById('gameQuery').value;
            const resultsDiv = document.getElementById('searchResults');
            if (!query) return;

            resultsDiv.innerHTML = '<div class="col-span-full flex justify-center py-10"><div class="animate-spin w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full"></div></div>';

            const response = await fetch(`/buscar-jogo?q=${query}`);
            const games = await response.json();

            resultsDiv.innerHTML = '';
            games.forEach(game => {
                const cover = game.cover ? game.cover.url.replace('t_thumb', 't_cover_big') : '//via.placeholder.com/264x352?text=Sem+Capa';
                resultsDiv.innerHTML += `
                    <div class="bg-[#0f111a] p-4 rounded-2xl border border-gray-800 flex flex-col items-center hover:border-indigo-500 transition shadow-xl">
                        <img src="https:${cover}" class="w-full aspect-[3/4] rounded-xl mb-4 object-cover">
                        <span class="text-white text-center font-bold text-xs mb-4 truncate w-full px-2 uppercase italic">${game.name}</span>
                        <form action="{{ route('games.store') }}" method="POST" class="w-full space-y-2">
                            @csrf
                            <input type="hidden" name="title" value="${game.name}">
                            <input type="hidden" name="cover_url" value="https:${cover}">
                            <input type="hidden" name="igdb_id" value="${game.id}">
                            <input type="hidden" name="rating" value="10">
                            <select name="status" class="w-full text-[10px] rounded-lg bg-[#1a1d2e] text-gray-300 border-gray-700 py-1 uppercase font-black">
                                <option value="zerado">✅ Zerado</option>
                                <option value="quero_jogar">⏳ Quero Jogar</option>
                                <option value="favorito">🔥 Favorito</option>
                            </select>
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-black text-[10px] py-2.5 rounded-lg uppercase tracking-widest transition shadow-md">Adicionar</button>
                        </form>
                    </div>`;
            });
        }

        function abrirModalEdicao(game) {
            document.getElementById('modalTitle').innerText = game.title;
            document.getElementById('editStatus').value = game.status;
            document.getElementById('editYear').value = game.year_completed;
            document.getElementById('editReview').value = game.review || '';
            
            // Lógica para marcar a meia-estrela ou estrela cheia (1 a 10)
            if(game.rating) {
                const starInput = document.getElementById(`star${game.rating}`);
                if(starInput) starInput.checked = true;
            } else {
                document.querySelectorAll('.star-rating input').forEach(el => el.checked = false);
            }
            
            document.getElementById('editForm').action = `/games/${game.id}`;
            document.getElementById('deleteForm').action = `/games/${game.id}`;
            document.getElementById('modalEdicao').classList.remove('hidden');
        }

        function fecharModal() {
            document.getElementById('modalEdicao').classList.add('hidden');
        }
    </script>
</x-app-layout>