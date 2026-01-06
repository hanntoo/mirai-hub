<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_game_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('game_type'); // Mobile Legends, Valorant, etc
            $table->string('username');
            $table->string('game_id');
            $table->string('server')->nullable(); // untuk ML ada server ID
            $table->timestamps();
            
            // User hanya bisa punya 1 profile per game
            $table->unique(['user_id', 'game_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_game_profiles');
    }
};
