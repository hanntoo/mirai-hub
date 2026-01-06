<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('whatsapp')->nullable()->after('avatar');
            $table->string('game_id')->nullable()->after('whatsapp'); // ID Game utama (ML, Valorant, dll)
            $table->string('game_type')->nullable()->after('game_id'); // Game favorit
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['whatsapp', 'game_id', 'game_type']);
        });
    }
};
