<nav x-data="{ open: false }" class="bg-[#0f111a] border-b-4 border-indigo-600/30 sticky top-0 z-50 shadow-[0_15px_50px_rgba(0,0,0,0.6)]">
    <div class="max-w-[1600px] mx-auto px-6 lg:px-10">
        <div class="flex justify-between h-28">

            <div class="flex items-center gap-10">
                <div class="shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-5 group">
                        <div class="relative">
                            <div class="absolute -inset-3 bg-indigo-600 rounded-xl blur-xl opacity-30 group-hover:opacity-80 transition duration-500"></div>
                            <div class="relative bg-gradient-to-br from-indigo-500 to-indigo-900 text-white w-14 h-14 flex items-center justify-center rounded-2xl border-2 border-white/10 group-hover:scale-110 transition-all">
                                <span class="text-3xl font-black italic tracking-tighter">CG</span>
                            </div>
                        </div>
                        <div class="hidden md:flex flex-col">
                            <span class="text-white uppercase font-black tracking-tighter text-3xl leading-none group-hover:text-indigo-400">
                                Currículo<span class="text-indigo-500">Gamer</span>
                            </span>
                        </div>
                    </a>
                </div>

                <div class="hidden xl:flex items-center h-full gap-4">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center justify-center h-16 px-8 rounded-2xl text-[14px] font-black uppercase tracking-widest transition-all duration-300 border-2 
                        {{ request()->routeIs('dashboard') ? 'bg-indigo-600 border-indigo-400 text-white shadow-[0_0_20px_rgba(79,70,229,0.4)]' : 'border-gray-800 text-gray-500 hover:text-white hover:border-indigo-500 hover:bg-indigo-500/10' }}">
                        🏠 Painel
                    </a>

                    <a href="{{ route('catalogo') }}"
                        class="flex items-center justify-center h-16 px-8 rounded-2xl text-[14px] font-black uppercase tracking-widest transition-all duration-300 border-2 
                        {{ request()->routeIs('catalogo') ? 'bg-indigo-600 border-indigo-400 text-white shadow-[0_0_20px_rgba(79,70,229,0.4)]' : 'border-gray-800 text-gray-500 hover:text-white hover:border-indigo-500 hover:bg-indigo-500/10' }}">
                        🕹️ Catálogo
                    </a>
                </div>
            </div>

            <div class="hidden lg:flex items-center flex-1 justify-center px-16">
                <div class="relative w-full max-w-xl group">
                    <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" id="userSearchInput"
                        class="block w-full bg-[#05060a] border-2 border-gray-800 text-white pl-14 pr-10 py-4 rounded-2xl text-sm font-black tracking-widest focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all placeholder-gray-800 uppercase italic"
                        placeholder="BUSCAR PLAYERS NA REDE..."
                        onkeyup="buscarPlayers(this.value)">

                    <div id="userSearchResults" class="absolute w-full mt-3 bg-[#1a1d2e] border-2 border-indigo-500/30 rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.8)] hidden z-[100] overflow-hidden backdrop-blur-xl">
                    </div>
                </div>
            </div>

            {{-- SINO DE NOTIFICAÇÕES --}}
            <div class="relative flex items-center mr-4" x-data="{ openNotify: false }">
                <button @click="openNotify = !openNotify" class="relative p-3 rounded-xl bg-gray-800/50 border-2 border-gray-700 hover:border-indigo-500 hover:bg-indigo-500/10 transition-all text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>

                    {{-- Badge de Notificação (Só aparece se houver pendentes) --}}
                    @php $pendingCount = Auth::user()->pendingFriendRequests()->count(); @endphp
                    @if($pendingCount > 0)
                    <span class="absolute -top-1 -right-1 bg-red-600 text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center border-2 border-[#0f111a] animate-bounce">
                        {{ $pendingCount }}
                    </span>
                    @endif
                </button>

                {{-- Dropdown de Notificações --}}
                <div x-show="openNotify" @click.away="openNotify = false" class="absolute right-0 mt-72 w-80 bg-[#1a1d2e] border-2 border-indigo-500/30 rounded-2xl shadow-2xl z-[110] overflow-hidden">
                    <div class="p-4 bg-[#0a0c14] border-b border-gray-800">
                        <h4 class="text-xs font-black uppercase tracking-widest text-white italic">Convites de Amizade</h4>
                    </div>

                    <div class="max-h-64 overflow-y-auto">
                        @foreach(Auth::user()->pendingFriendRequests as $request)
                        <div class="flex items-center justify-between p-3 border-b border-gray-800 last:border-0 hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded bg-indigo-600 flex items-center justify-center text-white font-black italic text-xs shadow-lg">
                                    {{ substr($request->sender->name ?? 'P', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-white uppercase italic leading-none">{{ $request->sender->name }}</p>
                                    <span class="text-[9px] text-indigo-400 font-bold uppercase tracking-widest">Quer ser seu aliado</span>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <form action="{{ route('friendship.accept', $request->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-7 h-7 bg-green-600 hover:bg-green-500 rounded flex items-center justify-center text-white transition-all active:scale-90">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                </form>

                                <form action="{{ route('friendship.destroy', $request->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-7 h-7 bg-red-600 hover:bg-red-500 rounded flex items-center justify-center text-white transition-all active:scale-90">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center">
                <div class="flex items-center gap-2">
                    {{-- PARTE 1: LINK DO PERFIL (FORA DO DROPDOWN) --}}
                    <a href="{{ route('profile.public', auth()->user()->id) }}"
                        class="flex items-center gap-4 p-2 pr-5 border-2 border-indigo-500/30 rounded-2xl bg-indigo-500/5 hover:border-indigo-500 hover:bg-indigo-500/10 transition-all group">

                        {{-- Avatar com Nível --}}
                        <div class="relative w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white shadow-lg">
                            <span class="font-black text-2xl italic">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            <div class="absolute -top-2 -right-2 bg-white text-indigo-900 w-7 h-7 rounded-lg flex items-center justify-center shadow-xl border-2 border-indigo-600">
                                <span class="text-[12px] font-black italic">{{ Auth::user()->level }}</span>
                            </div>
                        </div>

                        {{-- Nome e XP --}}
                        <div class="text-left">
                            <p class="text-[18px] font-black text-white uppercase tracking-tighter leading-none mb-1 group-hover:text-indigo-400 transition-colors">
                                {{ Auth::user()->name }}
                            </p>
                            <span class="text-[10px] font-black text-indigo-400 italic uppercase leading-none">
                                {{ Auth::user()->xp }} XP TOTAL
                            </span>
                        </div>
                    </a>

                    {{-- PARTE 2: DROPDOWN (APENAS A SETA) --}}
                    <x-dropdown align="right" width="64">
                        <x-slot name="trigger">
                            <button class="flex items-center justify-center w-10 h-16 rounded-xl border-2 border-gray-800 text-gray-500 hover:text-white hover:border-indigo-500 hover:bg-indigo-500/10 transition-all">
                                <svg class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="bg-[#1a1d2e] border-2 border-indigo-500/30 rounded-2xl overflow-hidden shadow-2xl">
                                {{-- Barra de XP no topo do Dropdown --}}
                                <div class="p-4 bg-[#0a0c14] border-b border-gray-800 flex justify-between items-center">
                                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">{{ Auth::user()->xp }} XP</span>
                                    <span class="text-[10px] bg-indigo-500/20 text-indigo-400 px-2 py-0.5 rounded border border-indigo-500/30">LVL {{ Auth::user()->level }}</span>
                                </div>

                                <div class="p-2 space-y-1">
                                    <x-dropdown-link :href="route('profile.edit')"
                                        class="flex items-center gap-4 text-xs uppercase font-black py-4 px-5 text-gray-300 hover:bg-indigo-600 hover:text-white rounded-xl transition-all duration-200">
                                        👤 Configurações
                                    </x-dropdown-link>

                                    <div class="h-px bg-gray-800 mx-4 my-1"></div>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();"
                                            class="flex items-center gap-4 text-xs uppercase font-black py-4 px-5 text-red-400 hover:bg-red-500/10 hover:text-red-400 border border-transparent hover:border-red-500/50 rounded-xl transition-all duration-200">
                                            🚪 Sair do Jogo
                                        </x-dropdown-link>
                                    </form>
                                </div>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </div>
</nav>