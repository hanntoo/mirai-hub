<?php

namespace App\Livewire\Admin;

use App\Models\Tournament;
use Livewire\Component;
use Livewire\WithPagination;

class TournamentList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function deleteTournament(int $id): void
    {
        $tournament = Tournament::where('user_id', auth()->id())->findOrFail($id);
        $tournament->delete();
        session()->flash('success', 'Tournament berhasil dihapus!');
    }

    public function toggleStatus(int $id): void
    {
        $tournament = Tournament::where('user_id', auth()->id())->findOrFail($id);
        $newStatus = match($tournament->status) {
            'draft' => 'open',
            'open' => 'closed',
            'closed' => 'open',
            default => 'draft',
        };
        $tournament->update(['status' => $newStatus]);
    }

    public function render()
    {
        $tournaments = Tournament::where('user_id', auth()->id())
            ->when($this->search, fn($q) => $q->where('title', 'ilike', "%{$this->search}%"))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->withCount('participants')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.tournament-list', compact('tournaments'))
            ->layout('layouts.admin', ['title' => 'Daftar Tournament']);
    }
}
