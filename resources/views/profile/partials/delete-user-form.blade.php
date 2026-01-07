<section class="space-y-6">
    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-600 hover:bg-red-500 px-8 py-3 rounded-xl font-bold uppercase tracking-widest shadow-[0_0_15px_rgba(220,38,38,0.3)]"
    >
        {{ __('Excluir Minha Conta ⚠️') }}
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8 bg-[#1a1d24] border border-gray-700 rounded-2xl">
            @csrf
            @method('delete')

            <h2 class="text-xl font-black text-white uppercase tracking-wider">
                {{ __('Você tem certeza disso?') }}
            </h2>

            <p class="mt-3 text-sm text-slate-300 leading-relaxed">
                {{ __('Uma vez que sua conta for excluída, todos os seus dados e conquistas de jogador serão apagados permanentemente. Por favor, insira sua senha para confirmar a exclusão.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Senha') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full bg-[#13151a] border-gray-600 text-white focus:border-red-500 focus:ring-red-500 rounded-xl py-3 px-4 shadow-inner"
                    placeholder="{{ __('Sua Senha Atual') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-red-400" />
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="bg-transparent border border-gray-600 text-gray-300 hover:bg-gray-700 hover:text-white rounded-xl px-6 py-2 transition-all uppercase font-bold text-xs">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-danger-button class="bg-red-600 hover:bg-red-500 text-white rounded-xl px-6 py-2 shadow-[0_0_10px_rgba(220,38,38,0.3)] uppercase font-bold text-xs tracking-widest border-none">
                    {{ __('Confirmar Exclusão') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>