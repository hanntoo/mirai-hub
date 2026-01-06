<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = [
            ['slug' => 'mobile-legends', 'name' => 'Mobile Legends: Bang Bang', 'abbreviation' => 'MLBB', 'sort_order' => 1],
            ['slug' => 'pubg-mobile', 'name' => 'PUBG Mobile', 'abbreviation' => 'PUBGM', 'sort_order' => 2],
            ['slug' => 'free-fire', 'name' => 'Free Fire', 'abbreviation' => 'FF', 'sort_order' => 3],
            ['slug' => 'valorant', 'name' => 'Valorant', 'abbreviation' => 'VAL', 'sort_order' => 4],
            ['slug' => 'dota-2', 'name' => 'Dota 2', 'abbreviation' => 'DOTA', 'sort_order' => 5],
            ['slug' => 'league-of-legends', 'name' => 'League of Legends', 'abbreviation' => 'LoL', 'sort_order' => 6],
            ['slug' => 'fifa', 'name' => 'EA Sports FC', 'abbreviation' => 'FC', 'sort_order' => 7],
            ['slug' => 'efootball', 'name' => 'eFootball', 'abbreviation' => 'PES', 'sort_order' => 8],
            ['slug' => 'apex-legends', 'name' => 'Apex Legends', 'abbreviation' => 'APEX', 'sort_order' => 9],
            ['slug' => 'fortnite', 'name' => 'Fortnite', 'abbreviation' => 'FN', 'sort_order' => 10],
            ['slug' => 'call-of-duty-mobile', 'name' => 'Call of Duty: Mobile', 'abbreviation' => 'CODM', 'sort_order' => 11],
            ['slug' => 'arena-of-valor', 'name' => 'Arena of Valor', 'abbreviation' => 'AOV', 'sort_order' => 12],
            ['slug' => 'counter-strike-2', 'name' => 'Counter-Strike 2', 'abbreviation' => 'CS2', 'sort_order' => 13],
            ['slug' => 'wild-rift', 'name' => 'League of Legends: Wild Rift', 'abbreviation' => 'WR', 'sort_order' => 14],
            ['slug' => 'honor-of-kings', 'name' => 'Honor of Kings', 'abbreviation' => 'HOK', 'sort_order' => 15],
            ['slug' => 'roblox', 'name' => 'Roblox', 'abbreviation' => 'RBLX', 'sort_order' => 16],
            ['slug' => 'other', 'name' => 'Lainnya', 'abbreviation' => null, 'sort_order' => 999],
        ];

        foreach ($games as $game) {
            Game::updateOrCreate(
                ['slug' => $game['slug']],
                $game
            );
        }
    }
}
