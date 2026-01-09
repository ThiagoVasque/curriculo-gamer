<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Currículo Gamer') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;700&display=swap" rel="stylesheet">

    {{-- Mude de: --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Para: --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/game-logic.js'])

    <style>
        body {
            font-family: 'Rajdhani', sans-serif;
        }
    </style>
</head>

<body class="antialiased bg-[#0f111a]">
    <div class="min-h-screen">
        @include('layouts.navigation')

        @isset($header)
        <header class="bg-[#1a1d2e] border-b border-gray-800 shadow-lg">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endisset

        <main>
            {{ $slot }}
        </main>
    </div>

    <script>
        function buscarJogosPelaNav() {
            const navInput = document.getElementById('navGameQuery');
            const query = navInput.value.trim();

            // Se o campo estiver vazio, não faz nada
            if (query.length === 0) return;

            /**
             * REDIRECIONAMENTO ESTRATÉGICO:
             * Nós enviamos o usuário para a rota 'catalogo' 
             * passando o termo de busca na URL (?search=nome-do-jogo)
             */
            window.location.href = "{{ route('catalogo') }}?search=" + encodeURIComponent(query);
        }

        let playerSearchTimer;

        function buscarPlayers(query) {
            clearTimeout(playerSearchTimer);
            const resultsDiv = document.getElementById('userSearchResults');

            if (query.length < 2) {
                resultsDiv.classList.add('hidden');
                return;
            }

            playerSearchTimer = setTimeout(async () => {
                try {
                    const response = await fetch(`/buscar-usuarios?q=${encodeURIComponent(query)}`);
                    const users = await response.json();

                    resultsDiv.innerHTML = '';
                    resultsDiv.classList.remove('hidden');

                    if (users.length === 0) {
                        resultsDiv.innerHTML = '<div class="p-6 text-center text-gray-500 uppercase font-black text-[10px] italic">Nenhum player encontrado</div>';
                        return;
                    }

                    users.forEach(user => {
                        resultsDiv.innerHTML += `
                    <div class="p-4 hover:bg-indigo-600/10 border-b border-gray-800 last:border-0 flex items-center justify-between group transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-black italic shadow-lg">
                                ${user.name.charAt(0)}
                            </div>
                            <div>
                                <p class="text-white font-black uppercase text-xs tracking-tighter">${user.name}</p>
                                <span class="text-indigo-400 font-bold text-[9px] uppercase tracking-widest italic">LVL ${user.level || 1}</span>
                            </div>
                        </div>
                        <button onclick="enviarSolicitacao(${user.id})" class="bg-indigo-600 hover:bg-indigo-500 text-white px-3 py-1.5 rounded-lg text-[9px] font-black uppercase italic transition-all active:scale-95">
                            + Add Player
                        </button>
                    </div>
                `;
                    });
                } catch (error) {
                    console.error('Erro na busca:', error);
                }
            }, 400);
        }
    </script>
</body>

</html>