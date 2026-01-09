<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('igdb_id');
            $table->string('title');
            $table->string('cover_url')->nullable();

            // Atualizado: Incluindo 'Desejado' e 'jogando' para evitar o erro de truncamento
            $table->enum('status', ['zerado', 'quero_jogar', 'favorito', 'jogando', 'platinado'])
                ->default('quero_jogar');
                
            $table->integer('rating')->nullable();
            $table->integer('year_completed')->nullable();
            $table->string('developer')->nullable(); // Campo para o estúdio do jogo
            $table->integer('release_year')->nullable(); // Ano de lançamento vindo da IGDB
            $table->text('summary')->nullable(); // Descrição do jogo
            $table->text('review')->nullable();
            $table->timestamps();
            $table->text('platforms')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
