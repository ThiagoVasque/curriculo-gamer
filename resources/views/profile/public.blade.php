<x-app-layout>
    <div class="min-h-screen bg-[#1b2838] text-[#c5c3c0] font-sans pb-10">
        {{-- Banner de Fundo --}}
        <div class="h-48 bg-gradient-to-r from-indigo-900 to-slate-900 shadow-inner"></div>

        <div class="max-w-6xl mx-auto px-4 -mt-16">
            {{-- Header Estilo Steam --}}
            <div class="bg-black/40 backdrop-blur-md p-6 rounded-t-lg border-x border-t border-white/10 flex flex-col md:flex-row gap-6 items-end">

                {{-- Foto de Perfil com Borda de Nível --}}
                <div class="relative">
                    <div class="w-32 h-32 rounded-lg border-2 border-indigo-500 overflow-hidden shadow-2xl shadow-indigo-500/20">
                        <img src="{{ $user->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=4f46e5&color=fff' }}" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -top-3 -right-3 bg-indigo-600 w-10 h-10 rounded-full border-2 border-[#1b2838] flex items-center justify-center text-white font-black italic shadow-lg">
                        {{ $user->level }}
                    </div>
                </div>

                {{-- Nick e Status --}}
                <div class="flex-1">
                    <h1 class="text-3xl font-black text-white uppercase tracking-tighter italic">{{ $user->name }}</h1>
                    <p class="text-indigo-400 font-bold text-xs uppercase tracking-widest mt-1">
                        <span class="w-2 h-2 bg-green-500 rounded-full inline-block mr-2 animate-pulse"></span> Online
                    </p>
                </div>

                {{-- Stats Rápidas --}}
                <div class="flex gap-4">
                    <div class="text-center bg-white/5 px-4 py-2 rounded-lg border border-white/5">
                        <p class="text-2xl font-black text-white">{{ $stats['platinas'] }}</p>
                        <p class="text-[9px] uppercase font-bold text-gray-500">Platinas</p>
                    </div>
                    <div class="text-center bg-white/5 px-4 py-2 rounded-lg border border-white/5">
                        <p class="text-2xl font-black text-white">{{ $stats['amigos_count'] ?? 0 }}</p>
                        <p class="text-[9px] uppercase font-bold text-gray-500">Aliados</p>
                    </div>
                </div>
            </div>

            {{-- Conteúdo Principal --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mt-6">

                {{-- Coluna da Esquerda: Jogos (Vitrine) --}}
                <div class="lg:col-span-3 space-y-6">
                    <div class="bg-black/20 p-4 rounded-lg border border-white/5">
                        <h3 class="text-white font-black uppercase italic text-sm mb-4 border-l-4 border-indigo-500 pl-2">Vitrine de Jogos Platinados</h3>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @forelse($user->games->where('status', 'platinado') as $game)
                                <div class="moldura-quadro moldura-platinada rounded-lg relative aspect-[3/4] group">
                                    <div class="inner-shadow-overlay"></div>
                                    <img src="{{ $game->cover_url }}" class="w-full h-full object-cover rounded-sm transition-transform group-hover:scale-105">
                                    <div class="absolute bottom-0 left-0 right-0 p-2 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <p class="text-[10px] text-white font-bold truncate">{{ $game->title }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="col-span-full text-center py-10 text-gray-600 uppercase font-black italic">Nenhuma platina conquistada ainda.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Coluna da Direita: Amigos e XP --}}
                <div class="space-y-6">
                    
                    {{-- Bloco de Ação Dinâmico --}}
                    <div class="bg-black/20 p-4 rounded-lg border border-white/5 text-center">
                        @if(auth()->id() === $user->id)
                            {{-- Se o perfil for MEU --}}
                            <a href="{{ route('profile.edit') }}" class="w-full inline-block bg-white/5 border border-white/10 text-white hover:bg-white/10 font-black py-3 rounded-lg uppercase italic text-sm transition-all">
                                ⚙️ Editar Perfil
                            </a>
                        @else
                            {{-- Se for de OUTRO --}}
                            @if($friendship && $friendship->status === 'accepted')
                                <form action="{{ route('friendship.destroy', $friendship->id) }}" method="POST" onsubmit="return confirm('Deseja remover este aliado?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full bg-red-600/20 border border-red-500 text-red-500 hover:bg-red-600 hover:text-white font-black py-3 rounded-lg uppercase italic text-sm transition-all shadow-[0_0_15px_rgba(239,68,68,0.2)]">
                                        ✖ Remover Aliado
                                    </button>
                                </form>
                            @elseif($friendship && $friendship->status === 'pending')
                                <button disabled class="w-full bg-yellow-600/20 border border-yellow-500 text-yellow-500 font-black py-3 rounded-lg uppercase italic text-sm cursor-not-allowed">
                                    Solicitação Pendente
                                </button>
                            @else
                                <button onclick="enviarSolicitacao({{ $user->id }})" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3 rounded-lg uppercase italic text-sm transition-all shadow-[0_0_15px_rgba(79,70,229,0.3)]">
                                    + Adicionar Amigo
                                </button>
                            @endif
                        @endif
                    </div>

                    {{-- Barra de XP --}}
                    <div class="bg-black/20 p-4 rounded-lg border border-white/5">
                        <h3 class="text-white font-black uppercase italic text-[10px] mb-3 tracking-widest text-gray-400">Progresso de Nível</h3>
                        <div class="w-full h-2 bg-gray-800 rounded-full overflow-hidden border border-white/5">
                            @php $percent = ($user->xp % 1000) / 10; @endphp
                            <div class="h-full bg-gradient-to-r from-indigo-600 to-indigo-400 shadow-[0_0_10px_rgba(79,70,229,0.5)]" style="width: {{ $percent }}%"></div>
                        </div>
                        <p class="text-[9px] text-right mt-2 font-bold text-indigo-400 uppercase italic">{{ $user->xp % 1000 }} / 1000 XP</p>
                    </div>

                    {{-- Lista de Amigos --}}
                    <div class="bg-black/20 p-4 rounded-lg border border-white/5">
                        <h3 class="text-white font-black uppercase italic text-xs mb-4 flex justify-between">
                            Aliados <span class="text-indigo-500">{{ count($amigos) }}</span>
                        </h3>
                        <div class="grid grid-cols-4 gap-2">
                            @forelse($amigos as $amigo)
                                <a href="{{ route('profile.public', $amigo->id) }}" class="group relative" title="{{ $amigo->name }}">
                                    <div class="aspect-square rounded border border-white/10 overflow-hidden group-hover:border-indigo-500 transition-all">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($amigo->name) }}&background=4f46e5&color=fff" class="w-full h-full object-cover">
                                    </div>
                                    {{-- Pequeno indicador online (opcional) --}}
                                    <div class="absolute bottom-0 right-0 w-2 h-2 bg-green-500 rounded-full border border-[#1b2838]"></div>
                                </a>
                            @empty
                                <p class="col-span-4 text-[9px] text-gray-600 uppercase font-black italic text-center py-2">Nenhum aliado recrutado.</p>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Script de Solicitação --}}
    <script>
        function enviarSolicitacao(userId) {
            fetch(`/friendship/send/${userId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                alert(data.success || data.error);
                location.reload();
            });
        }
    </script>
</x-app-layout>