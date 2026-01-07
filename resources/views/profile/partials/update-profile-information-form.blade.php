<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Seu Nome de Jogador')" class="text-white text-base font-bold mb-2" />
            <x-text-input id="name" name="name" type="text" class="block mt-1 w-full bg-[#13151a] border-gray-600 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-3 px-4 shadow-inner" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('E-mail da Conta')" class="text-white text-base font-bold mb-2" />
            <x-text-input id="email" name="email" type="email" class="block mt-1 w-full bg-[#13151a] border-gray-600 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-3 px-4 shadow-inner" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-200">
                        {{ __('Seu endereço de e-mail não foi verificado.') }}

                        <button form="send-verification" class="underline text-sm text-indigo-400 hover:text-indigo-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Clique aqui para reenviar o e-mail de verificação.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-400">
                            {{ __('Um novo link de verificação foi enviado para o seu e-mail.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4">
            <x-primary-button class="bg-indigo-600 hover:bg-indigo-500 text-white px-8 py-3 rounded-xl font-bold uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(79,70,229,0.4)] border-none">
                {{ __('Salvar Alterações') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-400 font-bold"
                >{{ __('Salvo com sucesso! ✅') }}</p>
            @endif
        </div>
    </form>
</section>