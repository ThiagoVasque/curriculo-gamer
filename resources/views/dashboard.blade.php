<x-app-layout>
    @vite(['resources/css/app.css'])

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-white leading-tight italic tracking-widest uppercase">
                {{ __('🕹️ Meu Painel de Controle') }}
            </h2>
            <div class="flex gap-2">
                {{-- Link rápido para o Perfil Público (onde agora ficam os amigos) --}}
                <a href="{{ route('profile.public', auth()->id()) }}" class="text-[10px] bg-indigo-600/20 border border-indigo-500/50 text-indigo-400 px-4 py-2 rounded-lg hover:bg-indigo-600 hover:text-white transition-all uppercase font-black italic">
                    👤 Meu Perfil
                </a>
                <a href="{{ route('profile.edit') }}" class="text-[10px] bg-white/5 border border-white/10 text-gray-400 px-4 py-2 rounded-lg hover:bg-white/10 transition-all uppercase font-black italic">
                    ⚙️ Configurações
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 1. Header de Status do Usuário --}}
            <div class="bg-[#1a1d2e] border border-gray-800 p-6 rounded-2xl shadow-xl mb-6">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div class="relative flex items-center justify-center shrink-0">
                        <div class="w-16 h-16 bg-indigo-600 rotate-45 rounded-xl shadow-[0_0_20px_rgba(79,70,229,0.4)]"></div>
                        <span class="absolute text-white font-black text-2xl italic">{{ auth()->user()->level }}</span>
                        <p class="absolute -bottom-2 text-[10px] text-indigo-400 font-black uppercase tracking-tighter">Nível</p>
                    </div>
                    <div class="flex-1 w-full">
                        <div class="flex justify-between mb-2">
                            <h3 class="text-white font-black uppercase italic tracking-widest text-lg">{{ auth()->user()->name }}</h3>
                            <span class="text-gray-500 font-bold text-xs uppercase">{{ auth()->user()->xp % 1000 }} / 1000 XP</span>
                        </div>

                        {{-- Lógica para evitar erro de sintaxe no editor --}}
                        @php
                        $currentXp = auth()->user()->xp % 1000;
                        $percentValue = $currentXp / 10;
                        @endphp

                        <div class="w-full h-4 bg-[#0f111a] rounded-full border border-gray-800 overflow-hidden p-[2px]">
                            <div class="h-full bg-gradient-to-r from-indigo-600 via-purple-500 to-indigo-400 rounded-full transition-all duration-1000 shadow-[0_0_15px_rgba(79,70,229,0.6)]"
                                style="--progress-width: {{ $percentValue }}; width: calc(var(--progress-width) * 1%)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                {{-- Todos --}}
                <div class="bg-[#1a1d2e] border border-gray-800 p-4 rounded-xl flex items-center gap-3 transition-all hover:border-indigo-500">
                    <div class="p-2 bg-indigo-500/10 rounded-lg text-indigo-500 italic font-black text-xl">{{ $myGames->count() }}</div>
                    <p class="text-[9px] text-gray-500 font-black uppercase tracking-tighter">Total</p>
                </div>

                {{-- Quero Jogar --}}
                <div class="bg-[#1a1d2e] border border-gray-800 p-4 rounded-xl flex items-center gap-3 transition-all hover:border-amber-500">
                    <div class="p-2 bg-amber-500/10 rounded-lg text-amber-500 italic font-black text-xl">{{ $myGames->where('status', 'quero_jogar')->count() }}</div>
                    <p class="text-[9px] text-gray-500 font-black uppercase tracking-tighter">Wishlist</p>
                </div>

                {{-- Jogando --}}
                <div class="bg-[#1a1d2e] border border-gray-800 p-4 rounded-xl flex items-center gap-3 transition-all hover:border-blue-500">
                    <div class="p-2 bg-blue-500/10 rounded-lg text-blue-500 italic font-black text-xl">{{ $myGames->where('status', 'jogando')->count() }}</div>
                    <p class="text-[9px] text-gray-500 font-black uppercase tracking-tighter">Jogando</p>
                </div>

                {{-- Zerados --}}
                <div class="bg-[#1a1d2e] border border-gray-800 p-4 rounded-xl flex items-center gap-3 transition-all hover:border-green-500">
                    <div class="p-2 bg-green-500/10 rounded-lg text-green-500 italic font-black text-xl">{{ $myGames->where('status', 'zerado')->count() }}</div>
                    <p class="text-[9px] text-gray-500 font-black uppercase tracking-tighter">Zerados</p>
                </div>

                {{-- Platinados --}}
                <div class="bg-[#1a1d2e] border border-gray-800 p-4 rounded-xl flex items-center gap-3 transition-all hover:border-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.1)]">
                    <div class="p-2 bg-cyan-500/10 rounded-lg text-cyan-400 italic font-black text-xl">{{ $myGames->where('status', 'platinado')->count() }}</div>
                    <p class="text-[9px] text-gray-500 font-black uppercase tracking-tighter">Platinas</p>
                </div>

                {{-- Favoritos --}}
                <div class="bg-[#1a1d2e] border border-gray-800 p-4 rounded-xl flex items-center gap-3 transition-all hover:border-rose-500">
                    <div class="p-2 bg-rose-500/10 rounded-lg text-rose-500 italic font-black text-xl">{{ $myGames->where('is_favorite', true)->count() }}</div>
                    <p class="text-[9px] text-gray-500 font-black uppercase tracking-tighter">Favoritos</p>
                </div>
            </div>

            {{-- 3. Biblioteca de Jogos (Ocupando a largura total) --}}
            <div class="bg-[#1a1d2e] shadow-2xl rounded-2xl border border-gray-800 p-6 md:p-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 border-b border-gray-800 pb-6 gap-4">
                    <div>
                        <h3 class="text-white text-2xl font-black uppercase tracking-tighter italic">Minha Biblioteca</h3>
                        <p class="text-gray-500 text-[10px] uppercase font-bold tracking-[0.2em] mt-1">Sincronizada com IGDB Cloud</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        {{-- Botões de filtro com estilo melhorado --}}
                        <button onclick="filtrarBiblioteca('todos')" class="filter-btn active bg-indigo-600 text-white text-[9px] font-black px-4 py-2 rounded-lg uppercase transition-all shadow-lg shadow-indigo-500/20">Todos</button>
                        <button onclick="filtrarBiblioteca('quero_jogar')" class="filter-btn bg-white/5 text-gray-400 border border-white/10 text-[9px] font-black px-4 py-2 rounded-lg uppercase hover:border-amber-500/50 hover:text-amber-500 transition-all">Wishlist</button>
                        <button onclick="filtrarBiblioteca('jogando')" class="filter-btn bg-white/5 text-gray-400 border border-white/10 text-[9px] font-black px-4 py-2 rounded-lg uppercase hover:border-blue-500/50 hover:text-blue-500 transition-all">Jogando</button>
                        <button onclick="filtrarBiblioteca('zerado')" class="filter-btn bg-white/5 text-gray-400 border border-white/10 text-[9px] font-black px-4 py-2 rounded-lg uppercase hover:border-green-500/50 hover:text-green-500 transition-all">Zerados</button>
                        <button onclick="filtrarBiblioteca('platinado')" class="filter-btn bg-white/5 text-gray-400 border border-white/10 text-[9px] font-black px-4 py-2 rounded-lg uppercase hover:border-cyan-400/50 hover:text-cyan-400 transition-all">Platinas</button>
                    </div>
                </div>

                <div id="libraryGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-8">
                    @forelse($myGames as $game)
                    <div class="game-card-anime group cursor-pointer relative"
                        data-status="{{ $game->status }}"
                        data-game-id="{{ $game->id }}"
                        data-game="{{ json_encode($game) }}"
                        onclick="abrirModalPeloDataset(this)">

                        {{-- Container da Imagem com Moldura Dinâmica --}}
                        {{-- Se for platinado, aplica 'moldura-quadro' e 'moldura-platinada' --}}
                        <div class="relative aspect-[3/4] rounded-2xl overflow-hidden shadow-2xl transition-all duration-500 group-hover:-translate-y-2 group-hover:shadow-indigo-500/20 
        {{ $game->status == 'platinado' ? 'moldura-quadro moldura-platinada aura-platinada' : 'border border-white/10' }}">

                            {{-- Imagem do Jogo --}}
                            <img src="{{ $game->cover_url }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 {{ $game->status == 'platinado' ? 'p-[2px] rounded-2xl' : '' }}">

                            {{-- Overlay de Sombra Interna (Sempre ativo para profundidade) --}}
                            <div class="inner-shadow-overlay"></div>

                            {{-- Badge de Status Flutuante --}}
                            <div class="absolute top-3 left-3 flex gap-1 z-20">
                                @if($game->status == 'platinado')
                                <span class="bg-white text-black font-black text-[8px] px-2 py-0.5 rounded shadow-lg uppercase italic animate-pulse">PLATINA</span>
                                @elseif($game->is_favorite)
                                <span class="bg-rose-600 text-white font-black text-[8px] px-2 py-0.5 rounded shadow-lg uppercase italic">★</span>
                                @endif
                            </div>

                            {{-- Botão Hover --}}
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all z-20">
                                <div class="bg-white/10 backdrop-blur-md border border-white/20 p-3 rounded-full scale-50 group-hover:scale-100 transition-all duration-500">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Info do Jogo --}}
                        <div class="mt-4 text-center">
                            <h4 class="text-white font-bold text-[12px] uppercase tracking-tighter italic truncate group-hover:text-indigo-400 transition-colors">
                                {{ $game->title }}
                            </h4>
                            <div class="flex items-center justify-center gap-2 mt-1">
                                <span class="text-[9px] font-black uppercase tracking-widest {{ $game->status == 'zerado' ? 'text-green-500' : ($game->status == 'jogando' ? 'text-blue-400' : ($game->status == 'platinado' ? 'text-cyan-400' : 'text-gray-500')) }}">
                                    {{ str_replace('_', ' ', $game->status) }}
                                </span>
                                @if($game->rating)
                                <span class="text-indigo-400 text-[9px] font-black border-l border-gray-800 pl-2">
                                    {{ $game->rating }}/10
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    {{-- Empty State --}}
                    <div class="col-span-full text-center py-20 bg-white/5 rounded-3xl border border-dashed border-gray-800">
                        <p class="text-gray-500 uppercase font-black italic text-xl">Estante Vazia</p>
                        <a href="{{ route('catalogo') }}" class="inline-block mt-4 text-indigo-400 text-xs font-black uppercase tracking-widest hover:text-indigo-300 transition-all border-b border-indigo-500/30 pb-1">Ir ao Catálogo →</a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @include('components.modal-game-engine')

    <script>
        window.DashboardConfig = {
            csrfToken: '{{ csrf_token() }}',
            routeUpdate: "/games",
            routeTraduzir: "/traduzir"
        };
    </script>
    @vite(['resources/js/game-logic.js'])
</x-app-layout>