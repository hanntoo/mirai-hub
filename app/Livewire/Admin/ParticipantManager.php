<?php

namespace App\Livewire\Admin;

use App\Models\Tournament;
use App\Models\Participant;
use Livewire\Component;
use Livewire\WithPagination;

class ParticipantManager extends Component
{
    use WithPagination;

    public Tournament $tournament;
    public string $search = '';
    public string $statusFilter = '';
    public ?Participant $selectedParticipant = null;
    public bool $showDetailModal = false;

    public function mount(Tournament $tournament): void
    {
        $this->tournament = $tournament;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function viewDetail(int $id): void
    {
        $this->selectedParticipant = Participant::findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedParticipant = null;
    }

    public function updateStatus(int $id, string $status): void
    {
        $participant = Participant::where('tournament_id', $this->tournament->id)->findOrFail($id);
        $participant->update(['payment_status' => $status]);
        session()->flash('success', 'Status pembayaran berhasil diupdate!');
        
        if ($this->selectedParticipant && $this->selectedParticipant->id === $id) {
            $this->selectedParticipant->refresh();
        }
    }

    public function deleteParticipant(int $id): void
    {
        $participant = Participant::where('tournament_id', $this->tournament->id)->findOrFail($id);
        $participant->delete();
        session()->flash('success', 'Peserta berhasil dihapus!');
        $this->closeModal();
    }

    public function render()
    {
        $participants = Participant::where('tournament_id', $this->tournament->id)
            ->when($this->search, function($q) {
                $q->where(function($query) {
                    $query->where('team_name', 'ilike', "%{$this->search}%")
                        ->orWhere('captain_name', 'ilike', "%{$this->search}%")
                        ->orWhere('whatsapp', 'ilike', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter, fn($q) => $q->where('payment_status', $this->statusFilter))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.participant-manager', compact('participants'))
            ->layout('layouts.admin', ['title' => 'Peserta: ' . $this->tournament->title]);
    }
}
