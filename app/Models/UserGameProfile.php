<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGameProfile extends Model
{
    protected $fillable = [
        'user_id',
        'game_type',
        'username',
        'game_id',
        'server',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // List game kompetitif yang didukung
    public static function supportedGames(): array
    {
        return [
            'mlbb' => [
                'name' => 'Mobile Legends: Bang Bang',
                'icon' => 'ml',
                'has_server' => true,
                'id_placeholder' => '123456789',
                'server_placeholder' => '1234',
            ],
            'valorant' => [
                'name' => 'Valorant',
                'icon' => 'val',
                'has_server' => false,
                'id_placeholder' => 'Username#TAG',
            ],
            'pubgm' => [
                'name' => 'PUBG Mobile',
                'icon' => 'pubg',
                'has_server' => false,
                'id_placeholder' => '5123456789',
            ],
            'freefire' => [
                'name' => 'Free Fire',
                'icon' => 'ff',
                'has_server' => false,
                'id_placeholder' => '123456789',
            ],
            'efootball' => [
                'name' => 'eFootball (PES)',
                'icon' => 'pes',
                'has_server' => false,
                'id_placeholder' => '123456789',
            ],
            'fcmobile' => [
                'name' => 'EA FC Mobile',
                'icon' => 'fifa',
                'has_server' => false,
                'id_placeholder' => 'Username',
            ],
        ];
    }

    /**
     * Get display name for game type key
     */
    public static function getGameName(string $key): string
    {
        return self::supportedGames()[$key]['name'] ?? $key;
    }
}
