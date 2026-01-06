<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

// Rota pública para a Home e Busca sem login
Route::get('/', [GameController::class, 'welcome'])->name('welcome');
Route::get('/buscar-publico', [GameController::class, 'searchPublic'])->name('games.search.public');

// Agrupe todas as rotas que precisam de login aqui
Route::middleware(['auth', 'verified'])->group(function () {

    // Rota Principal: agora controlada pelo GameController para carregar seus jogos
    Route::get('/dashboard', [GameController::class, 'index'])->name('dashboard');

    // Rotas da API de Games
    Route::get('/buscar-jogo', [GameController::class, 'search'])->name('games.search');
    Route::post('/salvar-jogo', [GameController::class, 'store'])->name('games.store');

    // Rotas de Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::patch('/games/{game}', [GameController::class, 'update'])->name('games.update');
Route::delete('/games/{game}', [GameController::class, 'destroy'])->name('games.destroy');
});

require __DIR__ . '/auth.php';