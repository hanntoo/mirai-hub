<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tournament;
use App\Models\Participant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo admin user
        $user = User::create([
            'name' => 'Admin Demo',
            'email' => 'admin@mirai.test',
            'password' => Hash::make('password'),
        ]);

        // Create sample tournament
        $tournament = Tournament::create([
            'user_id' => $user->id,
            'title' => 'MIRAI Cup Season 1 - Mobile Legends',
            'slug' => 'mirai-cup-s1-mlbb',
            'game_type' => 'mlbb',
            'event_date' => now()->addWeeks(2),
            'fee' => 50000,
            'max_slots' => 32,
            'description' => "Turnamen Mobile Legends terbesar di MIRAI!\n\nHadiah Total: Rp 5.000.000\n- Juara 1: Rp 2.500.000\n- Juara 2: Rp 1.500.000\n- Juara 3: Rp 1.000.000\n\nFormat: Single Elimination\nPendaftaran: Gratis untuk 32 tim pertama!",
            'status' => 'open',
            'form_schema' => [
                [
                    'id' => 'player_ids',
                    'type' => 'textarea',
                    'label' => 'ID Game Semua Pemain (5 orang)',
                    'required' => true,
                    'placeholder' => 'Masukkan ID game setiap pemain, pisahkan dengan enter',
                ],
                [
                    'id' => 'rank_tertinggi',
                    'type' => 'select',
                    'label' => 'Rank Tertinggi Tim',
                    'required' => true,
                    'options' => ['Warrior', 'Elite', 'Master', 'Grandmaster', 'Epic', 'Legend', 'Mythic', 'Mythical Glory'],
                ],
                [
                    'id' => 'bukti_transfer',
                    'type' => 'file',
                    'label' => 'Bukti Transfer Pembayaran',
                    'required' => true,
                ],
                [
                    'id' => 'info_note',
                    'type' => 'text_block',
                    'content' => 'Pastikan bukti transfer jelas dan mencantumkan nama tim. Pembayaran ke: BCA 1234567890 a.n. MIRAI Esports',
                ],
            ],
        ]);

        // Create another tournament
        Tournament::create([
            'user_id' => $user->id,
            'title' => 'Valorant Community Cup',
            'slug' => 'valorant-community-cup',
            'game_type' => 'valorant',
            'event_date' => now()->addWeeks(3),
            'fee' => 0,
            'max_slots' => 16,
            'description' => "Turnamen Valorant gratis untuk komunitas!\n\nFormat: Double Elimination\nMap Pool: All Competitive Maps",
            'status' => 'open',
            'form_schema' => [
                [
                    'id' => 'riot_ids',
                    'type' => 'textarea',
                    'label' => 'Riot ID Semua Pemain (5 orang)',
                    'required' => true,
                    'placeholder' => 'Format: Name#TAG',
                ],
                [
                    'id' => 'discord',
                    'type' => 'text',
                    'label' => 'Discord Server/Username Kapten',
                    'required' => true,
                    'placeholder' => 'username#1234',
                ],
            ],
        ]);

        // Create sample participants
        Participant::create([
            'tournament_id' => $tournament->id,
            'team_name' => 'Team Phoenix',
            'captain_name' => 'John Doe',
            'whatsapp' => '081234567890',
            'submission_data' => [
                'player_ids' => "12345678\n23456789\n34567890\n45678901\n56789012",
                'rank_tertinggi' => 'Mythic',
            ],
            'payment_status' => 'verified',
            'registered_at' => now()->subDays(2),
        ]);

        Participant::create([
            'tournament_id' => $tournament->id,
            'team_name' => 'Dragon Squad',
            'captain_name' => 'Jane Smith',
            'whatsapp' => '089876543210',
            'submission_data' => [
                'player_ids' => "11111111\n22222222\n33333333\n44444444\n55555555",
                'rank_tertinggi' => 'Legend',
            ],
            'payment_status' => 'pending',
            'registered_at' => now()->subDay(),
        ]);
    }
}
