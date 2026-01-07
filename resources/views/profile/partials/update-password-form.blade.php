<section>
    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Senha Atual')" class="text-white text-base font-bold mb-2" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="block mt-1 w-full bg-[#13151a] border-gray-600 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-3 px-4 shadow-inner" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-red-400" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Nova Senha')" class="text-white text-base font-bold mb-2" />
            <x-text-input id="update_password_password" name="password" type="password" class="block mt-1 w-full bg-[#13151a] border-gray-600 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-3 px-4 shadow-inner" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-red-400" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirmar Nova Senha')" class="text-white text-base font-bold mb-2" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full bg-[#13151a] border-gray-600 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-3 px-4 shadow-inner" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-red-400" />
        </div>

        <div class="flex items-center gap-4 pt-4">
            <x-primary-button class="bg-indigo-600 hover:bg-indigo-500 text-white px-8 py-3 rounded-xl font-bold uppercase tracking-widest transition-all shadow-[0_0_15px_rgba(79,70,229,0.4)] border-none">
                {{ __('Atualizar Senha 🛡️') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-400 font-bold"
                >{{ __('Senha alterada! ✅') }}</p>
            @endif
        </div>
    </form>
</section>