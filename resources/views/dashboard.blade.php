<x-app-layout>
    @vite(['resources/css/app.css'])

    <x-slot name="header">
        <h2 class="font-bold text-2xl text-white leading-tight italic tracking-widest uppercase">
            {{ __('🕹️ Meu Painel de Controle') }}
        </h2>
    </x-slot>

    {{-- 1. Stats e Header --}}
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
                        <h3 class="text-white font-black uppercase italic tracking-widest text-lg">{{ auth()->user()->name }}</h3>
                        <span class="text-gray-500 font-bold text-xs uppercase">{{ auth()->user()->xp % 1000 }} / 1000 XP para o próximo nível</span>
                    </div>
                    <div class="w-full h-4 bg-[#0f111a] rounded-full border border-gray-800 overflow-hidden">
                        @php $percent = (auth()->user()->xp % 1000) / 10; @endphp
                        <div class="h-full bg-gradient-to-r from-indigo-600 to-purple-500 transition-all duration-1000" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Stats Cards com Efeito Hover Glow --}}
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">

            {{-- Total de Jogos --}}
            <div class="group bg-[#1a1d2e] border border-gray-800 p-4 rounded-xl flex items-center gap-3 transition-all duration-300 hover:border-indigo-500 hover:shadow-[0_0_20px_rgba(79,70,229,0.2)] hover:-translate-y-1">
                <div class="p-2 bg-indigo-500/10 rounded-lg text-indigo-500 italic font-black text-xl group-hover:scale-110 transition-transform">
                    {{ $stats['total'] }}
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-tighter">Biblioteca</p>
                    <p class="text-[9px] text-indigo-400 font-bold hidden group-hover:block">Total</p>
                </div>
            </div>

            {{-- Jogando --}}
            <div class="group bg-[#1a1d2e] border border-gray-800 p-4 rounded-xl flex items-center gap-3 transition-all duration-300 hover:border-blue-500 hover:shadow-[0_0_20px_rgba(59,130,246,0.2)] hover:-translate-y-1">
                <div class="p-2 bg-blue-500/10 rounded-lg text-blue-500 italic font-black text-xl group-hover:scale-110 transition-transform">
                    {{ $stats['jogando'] }}
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-tighter">Jogando</p>
                    <p class="text-[9px] text-blue-400 font-bold hidden group-hover:block italic">Ativo agora</p>
                </div>
            </div>

            {{-- Quero Jogar (O novo que faltava) --}}
            <div class="group bg-[#1a1d2e] border border-gray-800 p-4 rounded-xl flex items-center gap-3 transition-all duration-300 hover:border-amber-500 hover:shadow-[0_0_20px_rgba(245,158,11,0.2)] hover:-translate-y-1">
                <div class="p-2 bg-amber-500/10 rounded-lg text-amber-500 italic font-black text-xl group-hover:scale-110 transition-transform">
                    {{ $myGames->where('status', 'quero_jogar')->count() }}
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-tighter">Backlog</p>
                    <p class="text-[9px] text-amber-400 font-bold hidden group-hover:block italic">Quero Jogar</p>
                </div>
            </div>

            {{-- Zerados --}}
            <div class="group bg-[#1a1d2e] border border-gray-800 p-4 rounded-xl flex items-center gap-3 transition-all duration-300 hover:border-green-500 hover:shadow-[0_0_20px_rgba(16,185,129,0.2)] hover:-translate-y-1">
                <div class="p-2 bg-green-500/10 rounded-lg text-green-500 italic font-black text-xl group-hover:scale-110 transition-transform">
                    {{ $stats['zerados'] }}
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-tighter">Finalizados</p>
                    <p class="text-[9px] text-green-400 font-bold hidden group-hover:block italic">Zerados</p>
                </div>
            </div>

            {{-- Favoritos --}}
            <div class="group bg-[#1a1d2e] border border-gray-800 p-4 rounded-xl flex items-center gap-3 transition-all duration-300 hover:border-rose-500 hover:shadow-[0_0_20px_rgba(244,63,94,0.2)] hover:-translate-y-1">
                <div class="p-2 bg-rose-500/10 rounded-lg text-rose-500 italic font-black text-xl group-hover:scale-110 transition-transform">
                    {{ $myGames->where('status', 'favorito')->count() }}
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 font-black uppercase tracking-tighter">Favoritos</p>
                    <p class="text-[9px] text-rose-400 font-bold hidden group-hover:block italic">Melhores</p>
                </div>
            </div>

            <div class="group bg-[#1a1d2e] border border-gray-800 p-4 rounded-xl flex items-center gap-3 transition-all duration-300 hover:border-slate-300 hover:shadow-[0_0_20px_rgba(226,232,240,0.2)] hover:-translate-y-1">
                <div class="p-2 bg-slate-500/10 rounded-lg text-slate-300 italic font-black text-xl">
                    {{ $myGames->where('status', 'platinado')->count() }}
                </div>
                <p class="text-[10px] text-gray-500 font-black uppercase tracking-tighter">Platinados</p>
            </div>

        </div>
    </div>

    {{-- 3. Biblioteca --}}
    {{-- BIBLIOTECA --}}
    <div class="py-6 bg-[#0f111a]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-[#1a1d2e] overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-800 p-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 border-b border-gray-800 pb-6 gap-4">
                    <h3 class="text-white text-2xl font-black uppercase tracking-tighter italic">Minha Biblioteca</h3>
                    <div class="flex flex-wrap gap-2">
                        <button onclick="filtrarBiblioteca('todos')" class="filter-btn bg-indigo-600 text-white text-[10px] font-black px-4 py-2 rounded-lg uppercase transition-all">Todos</button>
                        {{-- ... outros botões de filtro ... --}}
                    </div>
                </div>

                {{-- GRID DE JOGOS (Corrigido: ID único e um único loop) --}}
                <div id="libraryGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8">
                    @forelse($myGames as $game)
                    <div class="game-card-anime group cursor-pointer relative flex flex-col"
                        data-status="{{ $game->status }}"
                        data-game="{{ json_encode($game) }}"
                        onclick="abrirModalPeloDataset(this)">

                        {{-- STATUS BADGE --}}
                        <div class="absolute top-2 left-2 z-30 pointer-events-none">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black text-white uppercase tracking-tighter shadow-xl border-b border-white/20
                            @if($game->status == 'platinado') bg-cyan-100 !text-cyan-900 shadow-cyan-400/50 @endif
                            @if($game->status == 'zerado') bg-slate-200 !text-slate-900 shadow-slate-400/50 @endif
                            @if($game->status == 'quero_jogar') bg-amber-500 shadow-amber-500/50 @endif
                            @if($game->status == 'jogando') bg-blue-500 shadow-blue-500/50 @endif
                            @if($game->status == 'favorito') bg-rose-500 shadow-rose-500/50 @endif">
                                <span class="mr-1">
                                    @if($game->status == 'platinado') 🏆 @endif
                                    @if($game->status == 'zerado') ✅ @endif
                                    @if($game->status == 'quero_jogar') ⏳ @endif
                                    @if($game->status == 'jogando') 🎮 @endif
                                    @if($game->status == 'favorito') 🔥 @endif
                                </span>
                                {{ str_replace('_', ' ', ucwords($game->status, '_')) }}
                            </span>
                        </div>

                        <div class="aspect-[3/4] relative overflow-hidden transition-all duration-500 shadow-2xl
    @if($game->status == 'platinado')
        moldura-quadro moldura-platinada rounded-lg z-10
    @else
        bg-gray-900 border border-white/10 rounded-2xl
    @endif
    group-hover:scale-[1.05] group-hover:z-30">

                            {{-- A Sombra interna que dá profundidade --}}
                            @if($game->status == 'platinado')
                            <div class="inner-shadow-overlay"></div>
                            @endif

                            {{-- A imagem agora precisa de z-index para ficar acima do brilho --}}
                            <img src="{{ $game->cover_url }}" alt="{{ $game->title }}"
                                class="relative z-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110
        {{ $game->status == 'platinado' ? 'rounded-sm' : 'rounded-2xl' }}">

                            {{-- Overlay de Detalhes --}}
                            <div class="absolute inset-0 z-20 bg-gradient-to-t from-black/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center">
                                <span class="bg-white text-indigo-600 text-[10px] font-black px-4 py-2 rounded-xl uppercase italic shadow-2xl">Ver Detalhes</span>
                            </div>
                        </div>

                        {{-- TÍTULO --}}
                        <div class="py-3 px-1 text-center">
                            <h4 class="text-white font-black truncate text-xs uppercase italic tracking-tighter group-hover:text-indigo-400 transition-colors">
                                {{ $game->title }}
                            </h4>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500 uppercase font-black italic">Nenhum jogo encontrado.</p>
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