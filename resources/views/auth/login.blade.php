<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-white uppercase text-[11px] font-black tracking-[0.15em] mb-2" />
            <x-text-input id="email" class="block mt-1 w-full bg-[#050508] border-gray-700 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-3 px-4 shadow-inner" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-input-label for="password" :value="__('Senha')" class="text-white uppercase text-[11px] font-black tracking-[0.15em] mb-2" />
            <x-text-input id="password" class="block mt-1 w-full bg-[#050508] border-gray-700 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-3 px-4 shadow-inner" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-600 bg-[#050508] text-indigo-600 shadow-sm focus:ring-indigo-500 transition cursor-pointer" name="remember">
                <span class="ms-2 text-[10px] text-gray-300 group-hover:text-white uppercase font-black tracking-widest transition">{{ __('Manter logado') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-[10px] text-indigo-400 hover:text-white transition uppercase font-black tracking-widest border-b border-transparent hover:border-white" href="{{ route('password.request') }}">
                    {{ __('Esqueceu a senha?') }}
                </a>
            @endif
        </div>

        <div class="flex flex-col gap-5 mt-10">
            <x-primary-button class="w-full justify-center py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black uppercase tracking-[0.2em] transition-all duration-300 shadow-[0_0_20px_rgba(99,102,241,0.4)] border-none">
                {{ __('Entrar no Painel 🕹️') }}
            </x-primary-button>

            <div class="flex justify-between items-center text-[10px] uppercase font-black tracking-[0.2em] pt-6 border-t border-gray-800/50">
                <a href="/" class="text-gray-400 hover:text-white transition flex items-center gap-2 group">
                    <span class="group-hover:scale-125 transition-transform">🏠</span> 
                    <span class="border-b border-transparent group-hover:border-white">Início</span>
                </a>

                <a href="{{ route('register') }}" class="text-indigo-400 hover:text-white transition flex items-center gap-2 group">
                    <span class="border-b border-transparent group-hover:border-white">Criar Conta</span>
                    <span class="group-hover:rotate-12 transition-transform">✨</span>
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>