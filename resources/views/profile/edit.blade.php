<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <h2 class="font-extrabold text-3xl text-white leading-tight tracking-tight">
                {{ __('Configurações do Perfil') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-[#13151a] min-h-screen"> 
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="p-8 sm:p-10 bg-[#1f232b] shadow-2xl sm:rounded-3xl border border-gray-700">
                <div class="max-w-2xl">
                    <header class="mb-10 border-l-4 border-indigo-500 pl-6">
                        <h3 class="text-white text-xl font-bold uppercase tracking-wider">Dados do Jogador</h3>
                        <p class="text-slate-300 text-base mt-2">Atualize seu nome de usuário e endereço de e-mail principal.</p>
                    </header>
                    
                    <div class="text-white">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <div class="p-8 sm:p-10 bg-[#1f232b] shadow-2xl sm:rounded-3xl border border-gray-700">
                <div class="max-w-2xl">
                    <header class="mb-10 border-l-4 border-indigo-500 pl-6">
                        <h3 class="text-white text-xl font-bold uppercase tracking-wider">Segurança</h3>
                        <p class="text-slate-300 text-base mt-2">Recomendamos usar uma senha forte para proteger sua conta.</p>
                    </header>

                    <div class="text-white">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <div class="p-8 sm:p-10 bg-[#1f232b] shadow-2xl sm:rounded-3xl border border-red-500/50">
                <div class="max-w-2xl">
                    <header class="mb-10 border-l-4 border-red-500 pl-6">
                        <h3 class="text-red-500 text-xl font-bold uppercase tracking-wider">Zona Crítica</h3>
                        <p class="text-slate-300 text-base mt-2">Atenção: Ao deletar a conta, todos os seus dados serão apagados permanentemente.</p>
                    </header>

                    <div class="text-white">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>