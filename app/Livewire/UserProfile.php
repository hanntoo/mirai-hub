<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\UserGameProfile;

class UserProfile extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $whatsapp;
    public $avatar;
    public $newAvatar;
    
    // Password change
    public $current_password;
    public $new_password;
    public $new_password_confirmation;
    
    // Game profile form
    public $showGameForm = false;
    public $editingGameId = null;
    public $gameForm = [
        'game_type' => '',
        'username' => '',
        'game_id' => '',
        'server' => '',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->whatsapp = $user->whatsapp;
        $this->avatar = $user->avatar;
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'newAvatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = Auth::user();

        if ($this->newAvatar) {
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }
            $this->avatar = $this->newAvatar->store('avatars', 'public');
        }

        $user->update([
            'name' => $this->name,
            'whatsapp' => $this->whatsapp,
            'avatar' => $this->avatar,
        ]);

        $this->newAvatar = null;
        session()->flash('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required_if:has_password,true',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if ($user->password && !Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password saat ini salah');
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('password_success', 'Password berhasil diubah!');
    }

    // Game Profile Methods
    public function openGameForm($gameType = null)
    {
        if ($gameType) {
            $profile = Auth::user()->gameProfiles()->where('game_type', $gameType)->first();
            if ($profile) {
                $this->editingGameId = $profile->id;
                $this->gameForm = [
                    'game_type' => $profile->game_type,
                    'username' => $profile->username,
                    'game_id' => $profile->game_id,
                    'server' => $profile->server ?? '',
                ];
            }
        } else {
            $this->editingGameId = null;
            $this->gameForm = [
                'game_type' => '',
                'username' => '',
                'game_id' => '',
                'server' => '',
            ];
        }
        $this->showGameForm = true;
    }

    public function closeGameForm()
    {
        $this->showGameForm = false;
        $this->editingGameId = null;
        $this->gameForm = [
            'game_type' => '',
            'username' => '',
            'game_id' => '',
            'server' => '',
        ];
        $this->resetValidation();
    }

    public function saveGameProfile()
    {
        $this->validate([
            'gameForm.game_type' => 'required|string',
            'gameForm.username' => 'required|string|max:100',
            'gameForm.game_id' => 'required|string|max:100',
            'gameForm.server' => 'nullable|string|max:50',
        ], [
            'gameForm.game_type.required' => 'Pilih game terlebih dahulu',
            'gameForm.username.required' => 'Username wajib diisi',
            'gameForm.game_id.required' => 'ID Game wajib diisi',
        ]);

        $user = Auth::user();

        // Check if game already exists (for new profile)
        if (!$this->editingGameId) {
            $exists = $user->gameProfiles()->where('game_type', $this->gameForm['game_type'])->exists();
            if ($exists) {
                $this->addError('gameForm.game_type', 'Kamu sudah punya profil untuk game ini');
                return;
            }
        }

        if ($this->editingGameId) {
            $profile = UserGameProfile::find($this->editingGameId);
            $profile->update([
                'username' => $this->gameForm['username'],
                'game_id' => $this->gameForm['game_id'],
                'server' => $this->gameForm['server'] ?: null,
            ]);
            session()->flash('game_success', 'Profil game berhasil diperbarui!');
        } else {
            $user->gameProfiles()->create([
                'game_type' => $this->gameForm['game_type'],
                'username' => $this->gameForm['username'],
                'game_id' => $this->gameForm['game_id'],
                'server' => $this->gameForm['server'] ?: null,
            ]);
            session()->flash('game_success', 'Profil game berhasil ditambahkan!');
        }

        $this->closeGameForm();
    }

    public function deleteGameProfile($id)
    {
        $profile = UserGameProfile::where('id', $id)->where('user_id', Auth::id())->first();
        if ($profile) {
            $profile->delete();
            session()->flash('game_success', 'Profil game berhasil dihapus!');
        }
    }

    public function getAvatarUrlProperty()
    {
        if (!$this->avatar) {
            return null;
        }
        
        if (str_starts_with($this->avatar, 'http')) {
            return $this->avatar;
        }
        
        return Storage::url($this->avatar);
    }

    public function getSupportedGamesProperty()
    {
        return UserGameProfile::supportedGames();
    }

    public function getGameProfilesProperty()
    {
        return Auth::user()->gameProfiles()->get();
    }

    public function getAvailableGamesProperty()
    {
        $existingGames = Auth::user()->gameProfiles()->pluck('game_type')->toArray();
        return collect(UserGameProfile::supportedGames())->filter(function ($game, $key) use ($existingGames) {
            return !in_array($key, $existingGames);
        });
    }

    public function render()
    {
        return view('livewire.user-profile')->layout('layouts.public');
    }
}
