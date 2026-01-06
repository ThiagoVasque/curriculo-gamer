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

    <script>
        async function buscarPublico() {
            const query = document.getElementById('publicQuery').value;
            const resultsDiv = document.getElementById('publicResults');

            if (!query) return;

            resultsDiv.innerHTML = `
                <div class="col-span-full text-center py-10">
                    <div class="animate-spin inline-block w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full mb-4"></div>
                    <p class="text-gray-500">Consultando base de dados da IGDB...</p>
                </div>
            `;

            try {
                const response = await fetch(`/buscar-publico?q=${query}`);
                const games = await response.json();

                resultsDiv.innerHTML = '';

                if (games.length === 0) {
                    resultsDiv.innerHTML = '<p class="col-span-full text-center text-gray-500">Nenhum jogo encontrado.</p>';
                    return;
                }

                games.forEach(game => {
                    const cover = game.cover ?
                        game.cover.url.replace('t_thumb', 't_cover_big') :
                        '//via.placeholder.com/264x352?text=Sem+Capa';

                    resultsDiv.innerHTML += `
                        <div class="bg-[#1a1d2e] rounded-xl overflow-hidden border border-gray-800 hover:border-indigo-500 transition-all duration-300 group shadow-lg hover:-translate-y-1">
                            <div class="aspect-[3/4] overflow-hidden">
                                <img src="https:${cover}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" loading="lazy">
                            </div>
                            <div class="p-3">
                                <h4 class="text-sm font-bold truncate text-white">${game.name}</h4>
                                <p class="text-[10px] text-indigo-400 uppercase mt-1 font-semibold tracking-wider">🔒 Logue para salvar</p>
                            </div>
                        </div>
                    `;
                });
            } catch (error) {
                resultsDiv.innerHTML = '<p class="col-span-full text-center text-red-500">Erro ao buscar jogos. Tente novamente.</p>';
            }
        }
    </script>
</body>

</html>