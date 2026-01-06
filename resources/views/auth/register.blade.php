<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nome de Usuário')" class="text-white uppercase text-[11px] font-black tracking-[0.15em] mb-2" />
            <x-text-input id="name" class="block mt-1 w-full bg-[#050508] border-gray-700 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-3 px-4 shadow-inner" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-input-label for="email" :value="__('Email')" class="text-white uppercase text-[11px] font-black tracking-[0.15em] mb-2" />
            <x-text-input id="email" class="block mt-1 w-full bg-[#050508] border-gray-700 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-3 px-4 shadow-inner" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-input-label for="password" :value="__('Senha')" class="text-white uppercase text-[11px] font-black tracking-[0.15em] mb-2" />
            <x-text-input id="password" class="block mt-1 w-full bg-[#050508] border-gray-700 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-3 px-4 shadow-inner" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" class="text-white uppercase text-[11px] font-black tracking-[0.15em] mb-2" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full bg-[#050508] border-gray-700 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-3 px-4 shadow-inner" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-5 mt-10">
            <x-primary-button class="w-full justify-center py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black uppercase tracking-[0.2em] transition-all duration-300 shadow-[0_0_20px_rgba(99,102,241,0.4)] border-none">
                {{ __('Criar meu Currículo 🚀') }}
            </x-primary-button>

            <div class="flex justify-between items-center text-[10px] uppercase font-black tracking-[0.2em] pt-6 border-t border-gray-800/50">
                <a href="/" class="text-gray-400 hover:text-white transition flex items-center gap-2 group">
                    <span class="group-hover:scale-125 transition-transform">🏠</span> 
                    <span class="border-b border-transparent group-hover:border-white">Início</span>
                </a>

                <a href="{{ route('login') }}" class="text-indigo-400 hover:text-white transition flex items-center gap-2 group">
                    <span class="border-b border-transparent group-hover:border-white">Já tenho conta</span> 
                    <span class="group-hover:rotate-12 transition-transform">🕹️</span>
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>