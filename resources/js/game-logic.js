// resources/js/game-logic.js

window.sinopseBase = "";

// --- 1. MOTOR DE TRADUÇÃO ---
window.traduzirEngine = async function (lang) {
    const summaryDisplay = document.getElementById('modalSummary');
    const summaryInput = document.getElementById('inputSummary');
    const btnEn = document.getElementById('btn-engine-en');
    const btnPt = document.getElementById('btn-engine-pt');
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!summaryDisplay || !window.sinopseBase) return;

    const ativo = "bg-indigo-600 text-white shadow-lg";
    const inativo = "text-gray-500 hover:text-white bg-white/5";

    if (lang === 'en') {
        summaryDisplay.innerText = window.sinopseBase;
        if (summaryInput) summaryInput.value = window.sinopseBase;
        btnEn.className = `text-[9px] px-2 py-0.5 rounded font-black transition uppercase ${ativo}`;
        btnPt.className = `text-[9px] px-2 py-0.5 rounded font-black transition uppercase ${inativo}`;
        return;
    }

    btnEn.className = `text-[9px] px-2 py-0.5 rounded font-black transition uppercase ${inativo}`;
    btnPt.className = `text-[9px] px-2 py-0.5 rounded font-black transition uppercase ${ativo}`;
    summaryDisplay.innerHTML = '<span class="animate-pulse text-indigo-400">Traduzindo...</span>';

    try {
        const response = await fetch('/traduzir', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ text: window.sinopseBase, target: 'pt' })
        });

        const data = await response.json();
        const textoTraduzido = data.traducao || data.translated || data.text || window.sinopseBase;

        summaryDisplay.innerText = textoTraduzido;
        if (summaryInput) summaryInput.value = textoTraduzido;
    } catch (e) {
        console.error("Erro na tradução:", e);
        summaryDisplay.innerText = window.sinopseBase;
    }
};

// --- 2. PREENCHIMENTO DO MODAL ---
window.preencherModalMestre = function (game) {
    window.sinopseBase = game.summary || "Sem sinopse disponível.";

    // UI Reset
    const btnEn = document.getElementById('btn-engine-en');
    const btnPt = document.getElementById('btn-engine-pt');
    if (btnEn) btnEn.className = "text-[9px] px-2 py-0.5 rounded font-black transition uppercase bg-indigo-600 text-white shadow-lg";
    if (btnPt) btnPt.className = "text-[9px] px-2 py-0.5 rounded font-black transition uppercase text-gray-500 hover:text-white bg-white/5";

    // Dados Principais
    document.getElementById('modalTitle').innerText = game.title || game.name || "Título Indisponível";
    document.getElementById('modalCover').src = game.cover_url || "";
    document.getElementById('modalSummary').innerText = window.sinopseBase;
    document.getElementById('modalDeveloper').innerText = game.developer || "N/A";
    document.getElementById('modalYear').innerText = `Lançamento: ${game.release_year || 'N/A'}`;

    // Plataformas
    const container = document.getElementById('modalPlatforms');
    let pArray = [];
    if (container) {
        container.innerHTML = '';
        let pData = game.platforms || game.platform || game.plataformas || null;
        if (pData) {
            if (Array.isArray(pData)) {
                pArray = pData.map(p => (typeof p === 'object' ? (p.name || p.slug) : p));
            } else if (typeof pData === 'string' && pData !== "N/A") {
                try {
                    const parsed = JSON.parse(pData);
                    pArray = Array.isArray(parsed) ? parsed.map(p => typeof p === 'object' ? p.name : p) : [parsed];
                } catch (e) {
                    pArray = pData.replace(/[\[\]"']/g, "").split(',').map(p => p.trim());
                }
            }
        }
        pArray = [...new Set(pArray.filter(name => name && name !== "" && name !== "N/A"))];

        pArray.forEach(name => {
            const span = document.createElement('span');
            span.className = "bg-slate-950/80 text-indigo-400 border-t border-l border-indigo-500/50 text-[10px] font-black px-4 py-1.5 rounded-xl uppercase tracking-widest transition-all duration-300 hover:scale-105 hover:text-white flex items-center justify-center backdrop-blur-sm shadow-xl";
            span.style.boxShadow = "0 0 15px rgba(99, 102, 241, 0.2)";
            span.innerText = name;
            container.appendChild(span);
        });
    }

    // Sincronização de Inputs Ocultos
    document.getElementById('inputTitle').value = game.title || game.name || "";
    document.getElementById('inputSummary').value = window.sinopseBase;
    document.getElementById('inputCover').value = game.cover_url || "";
    document.getElementById('inputIgdbId').value = game.igdb_id || game.id || "";
    document.getElementById('inputDeveloper').value = game.developer || "";
    document.getElementById('inputReleaseYear').value = game.release_year || "";
    if (document.getElementById('inputPlatforms')) document.getElementById('inputPlatforms').value = pArray.join(', ');

    // --- CORREÇÃO DO STATUS ---
    // Ajustado para o ID correto do seu Blade: 'modal_game_status'
    const statusField = document.getElementById('modal_game_status');
    if (statusField) {
        statusField.value = game.status || 'quero_jogar';
    }

    if (document.getElementById('editReview')) {
        document.getElementById('editReview').value = game.review || '';
    }

    // Rating (Estrelas)
    document.querySelectorAll('.star-rating input').forEach(r => r.checked = false);
    if (game.rating) {
        const star = document.getElementById(`star${game.rating}`) || document.querySelector(`.star-rating input[value="${game.rating}"]`);
        if (star) star.checked = true;
    }

    // Favorito
    const favCheckbox = document.getElementById('is_favorite');
    if (favCheckbox) favCheckbox.checked = (game.is_favorite == 1 || game.is_favorite == true);

    document.getElementById('modalEdicao').classList.remove('hidden');
};

// --- 3. GATILHOS ---
window.abrirModalPeloDataset = function (element) {
    const game = JSON.parse(element.dataset.game);
    const form = document.getElementById('editForm');
    if (form) {
        form.action = `/games/${game.id}`;
        document.getElementById('formMethod').value = 'PATCH';
        document.getElementById('btnDeleteTrigger').classList.remove('hidden');
        document.getElementById('btnSalvar').innerText = "Atualizar Jogo";
    }
    window.preencherModalMestre(game);
};

window.abrirModalParaAdicionar = function (gameDataStr) {
    const game = typeof gameDataStr === 'string' ? JSON.parse(gameDataStr) : gameDataStr;
    const form = document.getElementById('editForm');
    if (form) {
        form.action = "/salvar-jogo";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('btnDeleteTrigger').classList.add('hidden');
        document.getElementById('btnSalvar').innerText = "Adicionar à Coleção";
    }
    window.preencherModalMestre(game);
};

window.confirmarExclusao = function () {
    if (confirm('Deseja realmente remover este jogo?')) {
        document.getElementById('formMethod').value = 'DELETE';
        document.getElementById('editForm').submit();
    }
};

window.fecharModal = () => document.getElementById('modalEdicao').classList.add('hidden');

// --- 4. FILTROS ---
window.filtrarBiblioteca = (filtro) => {
    const cards = document.querySelectorAll('.game-card-anime');
    const botoes = document.querySelectorAll('.filter-btn');

    cards.forEach(card => {
        const gameData = JSON.parse(card.dataset.game);
        const statusDoCard = card.dataset.status;

        if (filtro === 'todos') card.style.display = 'block';
        else if (filtro === 'favorito') card.style.display = (gameData.is_favorite == true || gameData.is_favorite == 1) ? 'block' : 'none';
        else card.style.display = (statusDoCard === filtro) ? 'block' : 'none';
    });

    botoes.forEach(btn => {
        btn.classList.toggle('bg-indigo-600', btn.getAttribute('onclick').includes(`'${filtro}'`));
        btn.classList.toggle('text-white', btn.getAttribute('onclick').includes(`'${filtro}'`));
    });
};

window.initPremiumFrames = function () {
    document.querySelectorAll('.game-card-anime[data-status="platinado"]').forEach(card => {
        const wrapper = card.querySelector('.moldura-quadro');
        if (!wrapper || wrapper.querySelector('.js-shimmer')) return;

        const shimmer = document.createElement('div');
        shimmer.className = 'js-shimmer absolute inset-0 pointer-events-none z-0';
        shimmer.style.background = 'linear-gradient(45deg, transparent 0%, rgba(255,255,255, 0.1) 45%, rgba(255,255,255, 0.6) 50%, rgba(255,255,255, 0.1) 55%, transparent 100%)';
        shimmer.style.backgroundSize = "250% 250%";
        wrapper.prepend(shimmer);

        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            shimmer.style.backgroundPosition = `${((e.clientX - rect.left) / rect.width) * 100}% ${((e.clientY - rect.top) / rect.height) * 100}%`;
        });
    });
};

// --- 5. BUSCA IGDB ---
let debounceTimer;
window.debounceSearch = function (query) {
    clearTimeout(debounceTimer);
    if (query.length === 0) return location.reload();
    if (query.length < 3) return;

    debounceTimer = setTimeout(async () => {
        const loader = document.getElementById('searchLoader');
        if (loader) loader.classList.remove('hidden');

        try {
            const response = await fetch(`${window.CatalogoConfig.routeSearch}?search=${query}`);
            const games = await response.json();
            const container = document.getElementById('searchResults');
            container.innerHTML = games.length ? games.map(g => renderGameCardSearch(g)).join('') : '<div class="col-span-full py-20 text-center text-gray-500">Nenhum game encontrado.</div>';
            document.getElementById('paginationNav')?.classList.add('hidden');
        } catch (e) { console.error('Erro na busca:', e); } 
        finally { loader?.classList.add('hidden'); }
    }, 600);
};

function renderGameCardSearch(game) {
    const cover = game.cover ? game.cover.url.replace('t_thumb', 't_cover_big').replace('//', 'https://') : 'https://via.placeholder.com/400x600';
    const year = game.first_release_date ? new Date(game.first_release_date * 1000).getFullYear() : 'N/A';
    const gameData = JSON.stringify({
        title: game.name, cover_url: cover, summary: game.summary || 'Sem sinopse.',
        developer: game.involved_companies?.[0]?.company?.name || 'N/A', release_year: year,
        igdb_id: game.id, platforms: game.platforms?.map(p => p.name).join(', ') || 'N/A'
    }).replace(/'/g, "\\'").replace(/"/g, '&quot;');

    return `
        <div onclick="abrirModalParaAdicionar('${gameData}')" class="group bg-[#0f111a] rounded-xl border border-gray-800 hover:border-indigo-500 transition-all cursor-pointer flex flex-col relative shadow-lg">
            <div class="aspect-[3/4] relative overflow-hidden bg-black rounded-t-xl">
                <img src="${cover}" class="w-full h-full object-cover group-hover:scale-105 transition-all">
                <div class="absolute inset-0 bg-indigo-600/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <span class="bg-white text-indigo-600 text-[9px] font-black px-2 py-1 rounded uppercase">+ Adicionar</span>
                </div>
            </div>
            <div class="p-2 text-center">
                <h4 class="text-white font-bold truncate text-[11px] uppercase italic">${game.name}</h4>
                <p class="text-gray-500 font-black text-[9px] uppercase mt-1">${year}</p>
            </div>
        </div>`;
}