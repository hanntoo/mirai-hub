<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tournament extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'game_type',
        'banner_path',
        'event_date',
        'fee',
        'max_slots',
        'description',
        'status',
        'form_schema',
    ];

    protected $casts = [
        'form_schema' => 'array',
        'event_date' => 'datetime',
        'fee' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function generateSlug(string $title): string
    {
        $slug = Str::slug($title);
        $count = static::where('slug', 'like', $slug . '%')->count();
        return $count ? "{$slug}-{$count}" : $slug;
    }

    public function isFull(): bool
    {
        return $this->participants()->count() >= $this->max_slots;
    }

    public function isOpen(): bool
    {
        return $this->status === 'open' && !$this->isFull();
    }
}
