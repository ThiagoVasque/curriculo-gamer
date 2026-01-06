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
            $table->enum('status', ['zerado', 'quero_jogar', 'favorito']);
            $table->integer('rating')->nullable(); // Nota de 0 a 10
            $table->integer('year_completed')->nullable(); // Ano que zerou
            $table->integer('hltb_hours')->nullable(); // Tempo do HowLongToBeat
            $table->text('review')->nullable(); // Review pessoal
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};