<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currículo Gamer - Explore os melhores jogos</title>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Rajdhani', sans-serif;
            background-color: #0f111a;
        }

        .glass {
            background: rgba(26, 29, 46, 0.8);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="text-white antialiased">

    <nav class="p-6 flex justify-between items-center max-w-7xl mx-auto">
        <div class="text-3xl font-black tracking-tighter text-indigo-500 flex items-center gap-2">
            <span class="bg-indigo-500 text-white px-2 py-0.5 rounded shadow-[0_0_15px_rgba(99,102,241,0.5)]">CG</span>
            <span>CURRÍCULO<span class="text-white">GAMER</span></span>
        </div>
        <div class="flex gap-4 items-center">
            @if (Route::has('login'))
            @auth
            <a href="{{ url('/dashboard') }}"
                class="text-indigo-400 font-bold border border-indigo-500 px-4 py-2 rounded-lg hover:bg-indigo-500 hover:text-white transition">
                Meu Painel
            </a>
            @else
            <a href="{{ route('login') }}" class="text-gray-300 font-bold py-2 hover:text-white transition">Login</a>
            <a href="{{ route('register') }}"
                class="bg-indigo-600 px-5 py-2 rounded-lg font-bold shadow-lg hover:bg-indigo-500 transition">
                Começar Agora
            </a>
            @endauth
            @endif
        </div>
    </nav>

    <header class="max-w-4xl mx-auto text-center py-20 px-6">
        <h1 class="text-5xl md:text-7xl font-bold mb-6 italic">
            Crie seu <span class="text-indigo-500 uppercase">currículo gamer</span> profissional.
        </h1>
        <p class="text-gray-400 text-xl mb-10">
            Explore milhares de jogos, acompanhe seu progresso e compartilhe suas conquistas com o mundo.
        </p>

        <div class="relative max-w-2xl mx-auto">
            <input type="text" id="publicQuery" placeholder="Pesquise um jogo agora..."
                onkeypress="if(event.key === 'Enter') buscarPublico()"
                class="w-full p-5 pl-8 rounded-2xl bg-[#1a1d2e] border-2 border-gray-800 focus:border-indigo-500 text-white transition-all outline-none text-lg shadow-2xl">
            <button onclick="buscarPublico()"
                class="absolute right-3 top-3 bg-indigo-600 px-8 py-2.5 rounded-xl font-bold hover:bg-indigo-500 transition shadow-lg">
                Buscar
            </button>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 pb-20">
        <div id="publicResults" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
        </div>
    </main>

    <div id="modalPublico" class="hidden fixed inset-0 bg-black/95 backdrop-blur-md z-50 flex items-center justify-center p-4">
        <div class="bg-[#1a1d2e] w-full max-w-4xl rounded-3xl border border-gray-700 overflow-hidden shadow-2xl relative flex flex-col md:flex-row">

            <div class="w-full md:w-1/3 bg-[#0f111a] p-6 flex flex-col items-center border-r border-gray-800">
                <img id="pubCover" src="" class="w-full rounded-2xl shadow-2xl mb-4 border border-gray-700">
                <p id="pubDeveloper" class="text-indigo-400 text-xs font-black uppercase tracking-widest text-center"></p>
            </div>

            <div class="w-full md:w-2/3 p-8">
                <div class="flex justify-between items-start mb-4">
                    <h3 id="pubTitle" class="text-white text-3xl font-black italic uppercase"></h3>
                    <button onclick="fecharModalPublico()" class="text-gray-500 hover:text-white transition text-3xl">&times;</button>
                </div>

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="text-gray-500 text-[10px] font-bold uppercase tracking-widest">Sinopse</h4>
                        <div class="flex gap-2 bg-[#0f111a] p-1 rounded-lg border border-gray-800">
                            <button onclick="traduzirSinopse('en')" id="btn-en" class="text-[10px] px-2 py-0.5 rounded font-bold transition bg-indigo-600 text-white">EN</button>
                            <button onclick="traduzirSinopse('pt')" id="btn-pt" class="text-[10px] px-2 py-0.5 rounded font-bold transition text-gray-500 hover:text-white">PT</button>
                        </div>
                    </div>
                    <p id="pubSummary" class="text-gray-300 text-sm leading-relaxed overflow-y-auto max-h-60 pr-2 italic"></p>
                </div>

                <div class="mt-8 border-t border-gray-800 pt-6">
                    <a href="{{ route('register') }}" class="inline-block bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-6 py-3 rounded-xl transition shadow-lg uppercase text-sm">
                        🎮 Crie sua conta para adicionar à coleção
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Armazena os resultados globalmente para acessar no modal
        let gamesCache = [];
        let sinopseOriginal = ""; // Guarda o texto em inglês original

        async function buscarPublico() {
            const query = document.getElementById('publicQuery').value;
            const resultsDiv = document.getElementById('publicResults');

            if (!query) return;

            resultsDiv.innerHTML = `
            <div class="col-span-full text-center py-10">
                <div class="animate-spin inline-block w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full mb-4"></div>
                <p class="text-gray-500">Consultando base de dados...</p>
            </div>
        `;

            try {
                // Chamada para a sua rota interna (sem tradução na busca para ser rápido)
                const response = await fetch(`/buscar-publico?q=${encodeURIComponent(query)}`);
                const games = await response.json();
                gamesCache = games;

                resultsDiv.innerHTML = '';

                if (games.length === 0) {
                    resultsDiv.innerHTML = '<p class="col-span-full text-center text-gray-500">Nenhum jogo encontrado.</p>';
                    return;
                }

                games.forEach((game, index) => {
                    const cover = game.cover ?
                        game.cover.url.replace('t_thumb', 't_cover_big') :
                        '//via.placeholder.com/264x352?text=Sem+Capa';

                    resultsDiv.innerHTML += `
                    <div class="bg-[#1a1d2e] rounded-xl overflow-hidden border border-gray-800 hover:border-indigo-500 transition-all duration-300 group shadow-lg hover:-translate-y-1 cursor-pointer"
                         onclick="abrirModalPublico(${index})">
                        <div class="aspect-[3/4] overflow-hidden">
                            <img src="https:${cover}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" loading="lazy">
                        </div>
                        <div class="p-3">
                            <h4 class="text-sm font-bold truncate text-white uppercase">${game.name}</h4>
                            <p class="text-[9px] text-indigo-400 uppercase mt-1 font-black tracking-tighter italic">Clique para detalhes</p>
                        </div>
                    </div>
                `;
                });
            } catch (error) {
                resultsDiv.innerHTML = '<p class="col-span-full text-center text-red-500">Erro na conexão.</p>';
            }
        }

        // Função que abre o modal e chama a tradução no seu backend
        async function abrirModalPublico(index) {
            const game = gamesCache[index];
            if (!game) return;

            // 1. Preenche os dados básicos (Inglês por padrão)
            sinopseOriginal = game.summary || "Sinopse não disponível.";
            document.getElementById('pubTitle').innerText = game.name;
            document.getElementById('pubCover').src = game.cover ?
                'https:' + game.cover.url.replace('t_thumb', 't_cover_big') :
                '//via.placeholder.com/264x352?text=Sem+Capa';

            const dev = game.involved_companies ? game.involved_companies[0].company.name : "Desenvolvedora Desconhecida";
            document.getElementById('pubDeveloper').innerText = dev;

            // 2. Mostra o modal
            document.getElementById('modalPublico').classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // 3. Chama a tradução automática para PT
            traduzirSinopse('pt');
        }

        // Alternar idiomas manualmente dentro do modal
        async function traduzirSinopse(lang) {
            const summaryElement = document.getElementById('pubSummary');

            if (lang === 'en') {
                summaryElement.innerText = sinopseOriginal;
                mudarEstiloBotao('en');
                return;
            }

            mudarEstiloBotao('pt');
            summaryElement.innerHTML = '<span class="animate-pulse text-indigo-400 italic">Traduzindo sinopse...</span>';

            try {
                const response = await fetch('/traduzir', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        // O Laravel exige esse token para rotas POST por segurança
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        texto: sinopseOriginal
                    })
                });

                if (!response.ok) throw new Error("Erro no servidor");

                const data = await response.json();

                // Exibe a tradução retornada pelo GameController
                summaryElement.innerText = data.traducao || sinopseOriginal;

            } catch (error) {
                console.error("Erro na tradução:", error);
                summaryElement.innerText = sinopseOriginal;
            }
        }

        function mudarEstiloBotao(lang) {
            const btnEn = document.getElementById('btn-en');
            const btnPt = document.getElementById('btn-pt');
            if (!btnEn || !btnPt) return;

            if (lang === 'en') {
                btnEn.className = "text-[10px] px-2 py-0.5 rounded font-bold bg-indigo-600 text-white transition";
                btnPt.className = "text-[10px] px-2 py-0.5 rounded font-bold text-gray-500 hover:text-white transition";
            } else {
                btnPt.className = "text-[10px] px-2 py-0.5 rounded font-bold bg-indigo-600 text-white transition";
                btnEn.className = "text-[10px] px-2 py-0.5 rounded font-bold text-gray-500 hover:text-white transition";
            }
        }

        function fecharModalPublico() {
            document.getElementById('modalPublico').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        document.getElementById('modalPublico').addEventListener('click', function(e) {
            if (e.target === this) fecharModalPublico();
        });
    </script>
</body>

</html>