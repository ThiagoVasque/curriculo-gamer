<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\FriendshipController;
use App\Models\User; // Importante para a rota de busca rápida
use Illuminate\Http\Request;

// Rota pública para a Home e Busca sem login
Route::get('/', [GameController::class, 'welcome'])->name('welcome');
Route::get('/buscar-publico', [GameController::class, 'search'])->name('games.search.public');

// Rota para tradução (fora do auth para funcionar no welcome se precisar)
Route::post('/traduzir', [GameController::class, 'traduzirNoModal']);

// Agrupe todas as rotas que precisam de login aqui
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard (Sua coleção)
    Route::get('/dashboard', [GameController::class, 'index'])->name('dashboard');

    // Catálogo (Jogos da API IGDB)
    Route::get('/catalogo', [GameController::class, 'catalogo'])->name('catalogo');

    // Ações de busca e salvamento de jogos
    Route::get('/catalogo/search', [GameController::class, 'search'])->name('catalogo.search');
    Route::post('/salvar-jogo', [GameController::class, 'store'])->name('games.store');

    // Edição e Exclusão da coleção
    Route::patch('/games/{game}', [GameController::class, 'update'])->name('games.update');
    Route::delete('/games/{game}', [GameController::class, 'destroy'])->name('games.destroy');
    Route::get('/games/{game}/edit', [GameController::class, 'edit'])->name('games.edit');

    // Perfil Privado (Configurações)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Perfil Social Público
    Route::get('/perfil/{id}', [ProfileController::class, 'showPublicProfile'])->name('profile.public');

    // --- SISTEMA DE AMIZADES (AS QUE FALTAVAM) ---

    // 1. Busca de Players para a Navbar (Retorna JSON para o JavaScript)
    Route::get('/search-players', [App\Http\Controllers\UserController::class, 'buscarUsuarios'])->name('users.search');

    // 2. Enviar pedido de amizade
    Route::post('/friendship/add/{id}', [FriendshipController::class, 'store'])->name('friendship.add');

    // 3. Aceitar pedido de amizade (O que aparece no Sino)
    // Rota para Aceitar (PATCH porque estamos atualizando o status)
    Route::patch('/friendship/accept/{id}', [App\Http\Controllers\FriendshipController::class, 'accept'])->name('friendship.accept');

    // Rota para Recusar/Deletar (DELETE)
    Route::delete('/friendship/delete/{id}', [App\Http\Controllers\FriendshipController::class, 'destroy'])->name('friendship.destroy');
});

require __DIR__ . '/auth.php';
