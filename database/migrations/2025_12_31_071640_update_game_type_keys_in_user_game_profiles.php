<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Map old keys to new keys
        $mapping = [
            'Mobile Legends' => 'mlbb',
            'Valorant' => 'valorant',
            'PUBG Mobile' => 'pubgm',
            'Free Fire' => 'freefire',
            'eFootball' => 'efootball',
            'FIFA Mobile' => 'fcmobile',
        ];

        foreach ($mapping as $old => $new) {
            DB::table('user_game_profiles')
                ->where('game_type', $old)
                ->update(['game_type' => $new]);
        }
    }

    public function down(): void
    {
        // Map new keys back to old keys
        $mapping = [
            'mlbb' => 'Mobile Legends',
            'valorant' => 'Valorant',
            'pubgm' => 'PUBG Mobile',
            'freefire' => 'Free Fire',
            'efootball' => 'eFootball',
            'fcmobile' => 'FIFA Mobile',
        ];

        foreach ($mapping as $old => $new) {
            DB::table('user_game_profiles')
                ->where('game_type', $old)
                ->update(['game_type' => $new]);
        }
    }
};
