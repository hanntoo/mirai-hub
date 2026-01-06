<?php

namespace App\Livewire\Admin;

use App\Models\Tournament;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditTournament extends Component
{
    use WithFileUploads;

    public Tournament $tournament;

    // Tournament basic fields
    public string $title = '';
    public string $description = '';
    public string $game_type = 'mobile-legends';
    public string $event_date = '';
    public float $fee = 0;
    public int $max_slots = 32;
    public string $status = 'draft';
    public $banner;
    public ?string $existing_banner = null;

    // Form builder fields
    public array $fields = [];
    public array $imageUploads = [];

    #[Computed]
    public function gameTypes(): array
    {
        return [
            'mobile-legends' => 'Mobile Legends',
            'pubg-mobile' => 'PUBG Mobile',
            'free-fire' => 'Free Fire',
            'valorant' => 'Valorant',
            'dota-2' => 'Dota 2',
            'league-of-legends' => 'League of Legends',
            'fifa' => 'FIFA',
            'pes' => 'eFootball',
            'other' => 'Lainnya',
        ];
    }

    public function mount(Tournament $tournament): void
    {
        $this->tournament = $tournament;
        $this->title = $tournament->title;
        $this->description = $tournament->description ?? '';
        $this->game_type = $tournament->game_type;
        $this->event_date = $tournament->event_date->format('Y-m-d\TH:i');
        $this->fee = (float) $tournament->fee;
        $this->max_slots = $tournament->max_slots;
        $this->status = $tournament->status;
        $this->existing_banner = $tournament->banner_path;
        $this->fields = $tournament->form_schema ?? [];
    }

    // ========== FIELD MANAGEMENT ==========

    public function addField(string $type): void
    {
        $field = [
            'id' => uniqid(),
            'type' => $type,
            'label' => '',
            'required' => false,
            'description' => '',
            'options' => [],
            'content' => null,
            'imageUrl' => '',
            'linkUrl' => '',
            'linkText' => '',
            'showImageInput' => false,
            'showLinkInput' => false,
            'fileSettings' => null,
        ];

        // Set defaults based on type
        if (in_array($type, ['note', 'image-view', 'link'])) {
            $field['label'] = match($type) {
                'note' => 'Judul Baru',
                'link' => 'Judul Link',
                'image-view' => 'Judul Gambar',
            };
            $field['content'] = '';
        }

        if ($type === 'section') {
            $field['label'] = 'Bagian Baru';
        }

        if (in_array($type, ['radio', 'checkbox', 'select'])) {
            $field['options'] = ['Opsi 1', 'Opsi 2'];
        }

        if ($type === 'file') {
            $field['fileSettings'] = [
                'allowedTypes' => ['image', 'pdf'],
                'maxSize' => 1048576,
                'restrictTypes' => false,
            ];
        }

        if (empty($field['label']) && in_array($type, ['text', 'textarea', 'radio', 'checkbox', 'select', 'file', 'date', 'time', 'datetime-local'])) {
            $field['label'] = 'Pertanyaan';
        }

        $this->fields[] = $field;
    }

    public function removeField(int $index): void
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function moveFieldUp(int $index): void
    {
        if ($index <= 0) return;
        $temp = $this->fields[$index];
        $this->fields[$index] = $this->fields[$index - 1];
        $this->fields[$index - 1] = $temp;
    }

    public function moveFieldDown(int $index): void
    {
        if ($index >= count($this->fields) - 1) return;
        $temp = $this->fields[$index];
        $this->fields[$index] = $this->fields[$index + 1];
        $this->fields[$index + 1] = $temp;
    }

    public function duplicateField(int $index): void
    {
        if (!isset($this->fields[$index])) return;
        $duplicate = $this->fields[$index];
        $duplicate['id'] = uniqid();
        $duplicate['label'] = ($duplicate['label'] ?? '') . ' (Copy)';
        array_splice($this->fields, $index + 1, 0, [$duplicate]);
    }

    public function updateFieldType(int $index, string $type): void
    {
        if (!isset($this->fields[$index])) return;
        
        $this->fields[$index]['type'] = $type;

        if (in_array($type, ['radio', 'checkbox', 'select'])) {
            if (empty($this->fields[$index]['options'])) {
                $this->fields[$index]['options'] = ['Opsi 1', 'Opsi 2'];
            }
        }

        if ($type === 'file' && empty($this->fields[$index]['fileSettings'])) {
            $this->fields[$index]['fileSettings'] = [
                'allowedTypes' => ['image', 'pdf'],
                'maxSize' => 1048576,
                'restrictTypes' => false,
            ];
        }
    }

    public function addOption(int $fieldIndex): void
    {
        if (!isset($this->fields[$fieldIndex])) return;
        $this->fields[$fieldIndex]['options'][] = 'Opsi Baru';
    }

    public function removeOption(int $fieldIndex, int $optionIndex): void
    {
        if (!isset($this->fields[$fieldIndex]['options'][$optionIndex])) return;
        unset($this->fields[$fieldIndex]['options'][$optionIndex]);
        $this->fields[$fieldIndex]['options'] = array_values($this->fields[$fieldIndex]['options']);
    }

    public function toggleRequired(int $index): void
    {
        if (!isset($this->fields[$index])) return;
        $this->fields[$index]['required'] = !$this->fields[$index]['required'];
    }

    public function toggleImageInput(int $index): void
    {
        if (!isset($this->fields[$index])) return;
        $this->fields[$index]['showImageInput'] = !$this->fields[$index]['showImageInput'];
    }

    public function toggleLinkInput(int $index): void
    {
        if (!isset($this->fields[$index])) return;
        $this->fields[$index]['showLinkInput'] = !$this->fields[$index]['showLinkInput'];
    }

    public function clearImage(int $index): void
    {
        if (!isset($this->fields[$index])) return;
        $this->fields[$index]['imageUrl'] = '';
        $this->fields[$index]['showImageInput'] = false;
    }

    public function clearLink(int $index): void
    {
        if (!isset($this->fields[$index])) return;
        $this->fields[$index]['linkUrl'] = '';
        $this->fields[$index]['linkText'] = '';
        $this->fields[$index]['showLinkInput'] = false;
    }

    public function toggleRestrictTypes(int $index): void
    {
        if (!isset($this->fields[$index]['fileSettings'])) return;
        $this->fields[$index]['fileSettings']['restrictTypes'] = !$this->fields[$index]['fileSettings']['restrictTypes'];
    }

    public function toggleFileType(int $index, string $fileType): void
    {
        if (!isset($this->fields[$index]['fileSettings'])) return;
        
        $types = $this->fields[$index]['fileSettings']['allowedTypes'] ?? [];
        $key = array_search($fileType, $types);
        
        if ($key !== false) {
            unset($types[$key]);
        } else {
            $types[] = $fileType;
        }
        
        $this->fields[$index]['fileSettings']['allowedTypes'] = array_values($types);
    }

    // ========== SAVE METHOD ==========

    public function updated($propertyName): void
    {
        if (str_starts_with($propertyName, 'imageUploads.')) {
            $parts = explode('.', $propertyName);
            $index = isset($parts[1]) ? (int) $parts[1] : -1;
            if ($index >= 0) {
                $this->handleImageUpload($index);
            }
        }
    }

    protected function handleImageUpload(int $index): void
    {
        if (!isset($this->imageUploads[$index]) || !isset($this->fields[$index])) {
            return;
        }

        $file = $this->imageUploads[$index];
        $path = $file->store('form-embeds', 'public');

        if ($this->fields[$index]['type'] === 'image-view') {
            $old = $this->fields[$index]['description'] ?? null;
            if ($old && !str_starts_with($old, 'http')) {
                Storage::disk('public')->delete($old);
            }
            $this->fields[$index]['description'] = $path;
            $this->fields[$index]['content'] = null;
        } else {
            $old = $this->fields[$index]['imageUrl'] ?? null;
            if ($old && !str_starts_with($old, 'http')) {
                Storage::disk('public')->delete($old);
            }
            $this->fields[$index]['imageUrl'] = $path;
        }

        unset($this->imageUploads[$index]);
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|min:3|max:255',
            'game_type' => 'required',
            'event_date' => 'required|date',
            'fee' => 'required|numeric|min:0',
            'max_slots' => 'required|integer|min:2|max:256',
        ]);

        try {
            $data = [
                'title' => $this->title,
                'description' => $this->description,
                'game_type' => $this->game_type,
                'event_date' => $this->event_date,
                'fee' => $this->fee,
                'max_slots' => $this->max_slots,
                'status' => $this->status,
                'form_schema' => $this->fields,
            ];

            if ($this->banner) {
                if ($this->existing_banner) {
                    Storage::disk('public')->delete($this->existing_banner);
                }
                $path = $this->banner->store('banners', 'public');
                $data['banner_path'] = $path;
                $this->existing_banner = $path;
            }

            $this->tournament->update($data);

            session()->flash('success', 'Turnamen berhasil diperbarui!');
            $this->redirect(route('admin.tournaments.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan turnamen: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.edit-tournament')
            ->layout('layouts.admin', ['title' => 'Edit Turnamen']);
    }
}
