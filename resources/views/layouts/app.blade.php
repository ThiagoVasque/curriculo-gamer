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
            @if (session('success'))
            <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                <div class="bg-green-600/20 border border-green-500 text-green-400 px-4 py-3 rounded-lg flex justify-between items-center shadow-[0_0_15px_rgba(34,197,94,0.2)]">
                    <div class="flex items-center gap-3">
                        <span class="font-black uppercase italic text-sm">(Sucesso):</span>
                        <p class="text-xs font-bold uppercase tracking-widest">{{ session('success') }}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-green-400 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <script>
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
                    // Ajustado para coincidir com a rota do seu web.php
                    const response = await fetch(`/search-players?q=${encodeURIComponent(query)}`);
                    const users = await response.json();

                    
                    console.log(users); // <--- ADICIONE ISSO AQUI



                    resultsDiv.innerHTML = '';
                    resultsDiv.classList.remove('hidden');

                    if (users.length === 0) {
                        resultsDiv.innerHTML = '<div class="p-6 text-center text-gray-500 uppercase font-black text-[10px] italic">Nenhum player encontrado</div>';
                        return;
                    }

                    users.forEach(user => {
                        let acaoHtml = '';

                        // Verifica o status que vem do servidor
                        if (user.friendship_status === 'accepted') {
                            acaoHtml = `<span class="text-green-500 font-black uppercase text-[10px] italic">✔ Amigo</span>`;
                        } else if (user.friendship_status === 'pending') {
                            acaoHtml = `<span class="text-yellow-500 font-black uppercase text-[10px] italic">Pendente...</span>`;
                        } else {
                            acaoHtml = `
            <button onclick="enviarSolicitacao(${user.id})" class="bg-indigo-600 hover:bg-indigo-500 text-white px-3 py-1.5 rounded-lg text-[9px] font-black uppercase italic transition-all">
                + Add Player
            </button>`;
                        }

                        resultsDiv.innerHTML += `
        <div class="p-4 hover:bg-indigo-600/10 border-b border-gray-800 last:border-0 flex items-center justify-between transition-all">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-black italic shadow-lg">
                    ${user.name.charAt(0)}
                </div>
                <div>
                    <p class="text-white font-black uppercase text-xs tracking-tighter">${user.name}</p>
                    <span class="text-indigo-400 font-bold text-[9px] uppercase tracking-widest italic">LVL ${user.level || 1}</span>
                </div>
            </div>
            <div>${acaoHtml}</div>
        </div>
    `;
                    });
                } catch (error) {
                    console.error('Erro na busca:', error);
                }
            }, 400);
        }

        // NOVA FUNÇÃO: Envia o convite sem recarregar a página (AJAX)
        async function enviarSolicitacao(userId) {
            // Seleciona o botão que foi clicado para dar um feedback visual nele
            const btn = event.target;
            const originalText = btn.innerText;

            btn.innerText = 'Enviando...';
            btn.disabled = true;

            try {
                const response = await fetch(`/friendship/add/${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json' // Importante para o Laravel saber que é AJAX
                    }
                });

                if (response.ok) {
                    // Feedback de sucesso
                    btn.innerText = '✅ Enviado!';
                    btn.classList.remove('bg-indigo-600');
                    btn.classList.add('bg-green-600');

                    // Fecha o menu de busca após 1.5 segundos
                    setTimeout(() => {
                        document.getElementById('userSearchResults').classList.add('hidden');
                        document.getElementById('userSearchInput').value = '';
                    }, 1500);

                } else {
                    throw new Error('Erro ao enviar');
                }
            } catch (error) {
                console.error('Erro:', error);
                btn.innerText = '❌ Erro';
                btn.disabled = false;
            }
        }
    </script>
</body>

</html>
