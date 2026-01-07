<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'GamerCV') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=black-ops-one:400|inter:400,700,900" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#050508] text-gray-100">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            
            <div class="mb-8 transform hover:scale-105 transition-transform duration-300">
                <a href="/">
                    <span class="text-4xl font-black italic tracking-tighter uppercase text-white drop-shadow-[0_0_15px_rgba(99,102,241,0.8)]">
                        Currículo<span class="text-indigo-500">Gamer</span>
                    </span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-10 py-12 bg-[#12141F] border border-gray-800 shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden sm:rounded-3xl relative">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-indigo-500 to-transparent opacity-50"></div>
                
                {{ $slot }}
            </div>

            <p class="mt-8 text-gray-600 text-[10px] font-bold uppercase tracking-[0.3em]">
                Sistema de Rank de Jogadores v1.0
            </p>
        </div>
    </body>
</html>