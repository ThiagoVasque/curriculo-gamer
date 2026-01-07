<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

// Rota pública para a Home e Busca sem login
Route::get('/', [GameController::class, 'welcome'])->name('welcome');
Route::get('/buscar-publico', [GameController::class, 'search'])->name('games.search.public');

// ADICIONE ESTA LINHA AQUI (Fora do middleware auth)
Route::post('/traduzir', [GameController::class, 'traduzirNoModal']);
// Agrupe todas as rotas que precisam de login aqui
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard (Sua coleção)
    Route::get('/dashboard', [GameController::class, 'index'])->name('dashboard');

    // Catálogo (Jogos da API IGDB)
    // REMOVEMOS o function() e colocamos o Controller:
    Route::get('/catalogo', [GameController::class, 'catalogo'])->name('catalogo');

    // Ações de busca e salvamento
    Route::get('/buscar-jogo', [GameController::class, 'search'])->name('games.search');
    Route::post('/salvar-jogo', [GameController::class, 'store'])->name('games.store');

    // Edição e Exclusão da coleção
    Route::patch('/games/{game}', [GameController::class, 'update'])->name('games.update');
    Route::delete('/games/{game}', [GameController::class, 'destroy'])->name('games.destroy');

    Route::get('/games/{game}/edit', [GameController::class, 'edit'])->name('games.edit');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
