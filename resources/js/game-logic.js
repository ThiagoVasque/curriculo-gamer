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
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                text: window.sinopseBase,
                target: 'pt'
            })
        });

        const data = await response.json();
        // Ajuste aqui para garantir que pegue o campo correto da sua API de tradução
        const textoTraduzido = data.traducao || data.translated || data.text || window.sinopseBase;

        summaryDisplay.innerText = textoTraduzido;
        if (summaryInput) summaryInput.value = textoTraduzido;

    } catch (e) {
        console.error("Erro na tradução:", e);
        summaryDisplay.innerText = window.sinopseBase;
    }
};

// --- 2. FUNÇÃO PARA ABRIR O MODAL ---
window.preencherModalMestre = function (game) {
    window.sinopseBase = game.summary || "Sem sinopse disponível.";

    // Reset botões tradução
    const btnEn = document.getElementById('btn-engine-en');
    const btnPt = document.getElementById('btn-engine-pt');
    if (btnEn) btnEn.className = "text-[9px] px-2 py-0.5 rounded font-black transition uppercase bg-indigo-600 text-white shadow-lg";
    if (btnPt) btnPt.className = "text-[9px] px-2 py-0.5 rounded font-black transition uppercase text-gray-500 hover:text-white bg-white/5";

    document.getElementById('modalTitle').innerText = game.title || game.name || "Título Indisponível";
    document.getElementById('modalCover').src = game.cover_url || "";
    document.getElementById('modalSummary').innerText = window.sinopseBase;
    document.getElementById('modalDeveloper').innerText = game.developer || "N/A";
    document.getElementById('modalYear').innerText = `Lançamento: ${game.release_year || 'N/A'}`;

    // 3. Plataformas (Visual Elegante e Colorido)
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

        // ... dentro de window.preencherModalMestre ...

        if (pArray.length > 0) {
            pArray.forEach(name => {
                const span = document.createElement('span');

                // Estilo Padronizado Premium (Efeito Dark Glass Neon)
                // Usamos um fundo escuro semi-transparente e bordas fixas
                span.className = `
            bg-slate-950/80 
            text-indigo-400 
            border-t border-l border-indigo-500/50 
            text-[10px] font-black px-4 py-1.5 
            rounded-xl uppercase tracking-widest 
            transition-all duration-300 
            hover:scale-105 hover:text-white hover:border-indigo-400
            cursor-default flex items-center justify-center 
            backdrop-blur-sm shadow-xl
        `.replace(/\s+/g, ' ').trim();

                // Brilho Neon Fixo (Indigo) para todas
                span.style.boxShadow = "0 0 15px rgba(99, 102, 241, 0.2)";
                span.style.textShadow = "0 0 8px rgba(99, 102, 241, 0.4)";

                span.innerText = name;
                container.appendChild(span);
            });
        } else {
            container.innerHTML = '<span class="text-gray-600 text-[10px] font-bold uppercase italic tracking-tighter">Plataformas N/A</span>';
        }
    }

    // 4. Inputs Ocultos
    document.getElementById('inputTitle').value = game.title || game.name || "";
    document.getElementById('inputSummary').value = window.sinopseBase;
    document.getElementById('inputCover').value = game.cover_url || "";
    document.getElementById('inputIgdbId').value = game.igdb_id || game.id || "";
    document.getElementById('inputDeveloper').value = game.developer || "";
    document.getElementById('inputReleaseYear').value = game.release_year || "";
    if (document.getElementById('inputPlatforms')) document.getElementById('inputPlatforms').value = pArray.join(', ');

    // 5. Dados de Edição
    if (document.getElementById('editStatus')) document.getElementById('editStatus').value = game.status || 'quero_jogar';
    if (document.getElementById('editReview')) document.getElementById('editReview').value = game.review || '';

    // Estrelas
    document.querySelectorAll('.star-rating input').forEach(r => r.checked = false);
    if (game.rating) {
        const star = document.getElementById(`star${game.rating}`);
        if (star) star.checked = true;
    }

    document.getElementById('modalEdicao').classList.remove('hidden');
};

// --- 3. GATILHOS E EXCLUSÃO ---
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

// FUNÇÃO DE EXCLUSÃO CORRIGIDA
window.confirmarExclusao = function () {
    if (confirm('Deseja realmente remover este jogo da sua coleção?')) {
        const methodInput = document.getElementById('formMethod');
        if (methodInput) {
            methodInput.value = 'DELETE'; // Muda o método para DELETE antes de enviar
            document.getElementById('editForm').submit();
        }
    }
};

window.fecharModal = () => document.getElementById('modalEdicao').classList.add('hidden');

window.filtrarBiblioteca = (status) => {
    document.querySelectorAll('.game-card-anime').forEach(card => {
        card.style.display = (status === 'todos' || card.dataset.status === status) ? 'block' : 'none';
    });
};

// --- 4. MOTOR DE MOLDURAS DINÂMICAS (PLATINA) ---

window.initPremiumFrames = function() {
    const platinados = document.querySelectorAll('.game-card-anime[data-status="platinado"]');
    
    platinados.forEach(card => {
        const wrapper = card.querySelector('.moldura-quadro');
        if (!wrapper) return;

        // Criamos um elemento de brilho extra via JS
        if (!wrapper.querySelector('.js-shimmer')) {
            const shimmer = document.createElement('div');
            shimmer.className = 'js-shimmer absolute inset-0 pointer-events-none z-0';
            shimmer.style.background = `
                linear-gradient(
                    45deg, 
                    transparent 0%, 
                    rgba(255,255,255, 0.1) 45%, 
                    rgba(255,255,255, 0.6) 50%, 
                    rgba(255,255,255, 0.1) 55%, 
                    transparent 100%
                )
            `;
            shimmer.style.backgroundSize = "250% 250%";
            shimmer.style.transition = "background-position 0.5s ease";
            wrapper.prepend(shimmer);
        }

        // Efeito: O brilho segue o movimento do mouse
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            
            const shimmer = wrapper.querySelector('.js-shimmer');
            if (shimmer) {
                shimmer.style.backgroundPosition = `${x}% ${y}%`;
            }
        });

        // Reset ao sair
        card.addEventListener('mouseleave', () => {
            const shimmer = wrapper.querySelector('.js-shimmer');
            if (shimmer) {
                shimmer.style.backgroundPosition = "0% 0%";
            }
        });
    });
};
