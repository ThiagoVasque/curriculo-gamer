<div id="modalEdicao" class="hidden fixed inset-0 bg-black/95 backdrop-blur-md z-50 flex items-center justify-center p-4">
    <div class="bg-[#1a1d2e] w-full max-w-4xl rounded-3xl border border-gray-700 overflow-hidden shadow-2xl relative flex flex-col md:flex-row max-h-[90vh] overflow-y-auto custom-scrollbar">

        {{-- Lado Esquerdo: Capa e Infos --}}
        <div class="w-full md:w-1/3 bg-[#0f111a] p-6 flex flex-col items-center border-r border-gray-800 relative">
            <div id="modalIgdbRatingContainer" class="absolute top-8 right-8 z-10 hidden">
                <div class="bg-indigo-600/90 backdrop-blur-sm text-white font-black text-[11px] px-2 py-1 rounded-lg shadow-xl border border-indigo-400 flex items-center gap-1">
                    <span class="text-[8px] opacity-70">IGDB</span>
                    <span id="modalIgdbRating"></span>
                </div>
            </div>
            <img id="modalCover" src="" class="w-full rounded-2xl shadow-2xl mb-4 border border-gray-700 object-cover aspect-[3/4]">
            <p id="modalDeveloper" class="text-indigo-400 text-[10px] font-black uppercase tracking-widest text-center italic"></p>
            <div id="modalPlatforms" class="flex flex-wrap justify-center gap-1.5 mt-3 px-2"></div>
            <p id="modalYear" class="text-gray-500 text-[10px] font-bold mt-4 uppercase border-t border-gray-800 pt-3 w-full text-center"></p>
        </div>

        {{-- Lado Direito: Sinopse e Form --}}
        <div class="w-full md:w-2/3 p-8 flex flex-col">
            <div class="flex justify-between items-start mb-6">
                <h3 id="modalTitle" class="text-white text-3xl font-black italic uppercase leading-tight tracking-tighter"></h3>
                <button onclick="fecharModal()" class="text-gray-500 hover:text-white transition-colors text-3xl leading-none">&times;</button>
            </div>

            <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-gray-500 text-[10px] font-black uppercase tracking-[0.2em]">Sinopse</h4>
                    <div class="flex gap-2 bg-[#0f111a] p-1 rounded-lg border border-gray-800">
                        <button type="button" onclick="window.traduzirEngine('en')" id="btn-engine-en" class="text-[9px] px-2 py-0.5 rounded font-black transition uppercase">EN</button>
                        <button type="button" onclick="window.traduzirEngine('pt')" id="btn-engine-pt" class="text-[9px] px-2 py-0.5 rounded font-black transition uppercase">PT</button>
                    </div>
                </div>
                <div class="bg-[#0f111a]/50 p-4 rounded-xl border border-white/5 text-gray-300 text-sm italic leading-relaxed overflow-y-auto max-h-32 custom-scrollbar">
                    <p id="modalSummary" class="transition-all"></p>
                </div>
            </div>

            <form id="editForm" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="PATCH">
                <input type="hidden" name="title" id="inputTitle">
                <input type="hidden" name="cover_url" id="inputCover">
                <input type="hidden" name="igdb_id" id="inputIgdbId">
                <input type="hidden" name="developer" id="inputDeveloper">
                <input type="hidden" name="release_year" id="inputReleaseYear">
                <input type="hidden" name="first_release_date" id="inputFullDate">
                <input type="hidden" name="summary" id="inputSummary">
                <input type="hidden" name="platforms" id="inputPlatforms">

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-gray-500 text-[10px] font-black uppercase mb-2 block tracking-widest italic">Status</label>
                        <select id="modal_game_status" name="status" class="bg-[#0f111a] border-gray-800 text-white rounded-lg w-full">
                            <option value="quero_jogar">⏳ Quero Jogar</option>
                            <option value="jogando">🎮 Jogando</option>
                            <option value="zerado">✅ Zerado</option>
                            <option value="platinado">🏆 Platinado</option> {{-- NOVO STATUS --}}
                            <option value="favorito">🔥 Favorito</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-gray-500 text-[10px] font-black uppercase mb-2 block tracking-widest text-center italic">Sua Nota</label>
                        <div class="star-rating">
                            @for ($i = 10; $i >= 1; $i--)
                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" />
                            <label for="star{{ $i }}" class="{{ $i % 2 == 0 ? 'full' : 'half' }}"></label>
                            @endfor
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-gray-500 text-[10px] font-black uppercase mb-2 block tracking-widest italic text-indigo-500">Minhas Notas e Review</label>
                    <textarea name="review" id="editReview" rows="4" class="w-full bg-[#0f111a] border-gray-700 rounded-2xl text-white text-xs focus:ring-indigo-500 resize-none custom-scrollbar p-4" placeholder="Escreva aqui o que achou..."></textarea>
                </div>

                <div class="flex gap-4 pt-2">
                    <button type="submit" id="btnSalvar" class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white font-black py-4 rounded-2xl transition uppercase tracking-widest shadow-lg italic">Confirmar</button>
                    <button type="button" id="btnDeleteTrigger" onclick="confirmarExclusao()" class="bg-red-600/10 hover:bg-red-600 text-red-500 hover:text-white px-6 py-4 rounded-2xl transition border border-red-600/20 hidden">🗑️</button>
                </div>
            </form>
        </div>
    </div>
</div>