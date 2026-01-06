<?php

namespace App\Livewire\Admin;

use App\Models\Tournament;
use App\Models\Participant;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $userId = auth()->id();
        
        $stats = [
            'total_tournaments' => Tournament::where('user_id', $userId)->count(),
            'open_tournaments' => Tournament::where('user_id', $userId)->where('status', 'open')->count(),
            'total_participants' => Participant::whereHas('tournament', fn($q) => $q->where('user_id', $userId))->count(),
            'pending_payments' => Participant::whereHas('tournament', fn($q) => $q->where('user_id', $userId))
                ->where('payment_status', 'pending')->count(),
            'verified_payments' => Participant::whereHas('tournament', fn($q) => $q->where('user_id', $userId))
                ->where('payment_status', 'verified')->count(),
        ];

        $recentTournaments = Tournament::where('user_id', $userId)
            ->withCount('participants')
            ->latest()
            ->take(5)
            ->get();

        $recentParticipants = Participant::whereHas('tournament', fn($q) => $q->where('user_id', $userId))
            ->with('tournament')
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.admin.dashboard', compact('stats', 'recentTournaments', 'recentParticipants'))
            ->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}
