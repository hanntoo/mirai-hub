<?php

namespace App\Livewire;

use App\Models\Tournament;
use App\Models\Participant;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class PublicRegistration extends Component
{
    use WithFileUploads;

    public Tournament $tournament;
    public string $team_name = '';
    public string $captain_name = '';
    public string $whatsapp = '';
    public array $answers = [];
    public array $fileUploads = [];
    public bool $submitted = false;
    
    // Multi-step form
    public int $currentStep = 0;
    public array $sections = [];

    // Static block types that don't need input
    protected array $staticTypes = ['note', 'image-view', 'link', 'section'];

    public function mount(string $slug): void
    {
        $this->tournament = Tournament::where('slug', $slug)
            ->where('status', 'open')
            ->firstOrFail();

        // Parse sections from form_schema
        $this->parseSections();

        // Initialize answers
        foreach ($this->tournament->form_schema ?? [] as $field) {
            if (!in_array($field['type'], $this->staticTypes)) {
                $this->answers[$field['id']] = $field['type'] === 'checkbox' ? [] : '';
            }
        }

        // Auto-fill fixed fields from user profile
        $this->autoFillFromProfile();
    }

    protected function autoFillFromProfile(): void
    {
        $user = auth()->user();
        if (!$user) return;

        // Fill captain name from user name
        $this->captain_name = $user->name ?? '';
        
        // Fill whatsapp from user profile
        $this->whatsapp = $user->whatsapp ?? '';
    }

    public function getGameProfileProperty()
    {
        $user = auth()->user();
        if (!$user) return null;

        return $user->getGameProfile($this->tournament->game_type);
    }

    protected function parseSections(): void
    {
        $formSchema = $this->tournament->form_schema ?? [];
        $sections = [];
        $currentSection = [
            'title' => 'Informasi Pendaftaran',
            'description' => '',
            'fields' => [],
            'includesFixedFields' => true, // First section includes team info
        ];

        foreach ($formSchema as $field) {
            if ($field['type'] === 'section') {
                // Save current section if it has fields
                if (!empty($currentSection['fields']) || $currentSection['includesFixedFields']) {
                    $sections[] = $currentSection;
                }
                // Start new section
                $currentSection = [
                    'title' => $field['label'] ?? 'Bagian Baru',
                    'description' => $field['description'] ?? '',
                    'fields' => [],
                    'includesFixedFields' => false,
                ];
            } else {
                $currentSection['fields'][] = $field;
            }
        }

        // Add last section
        if (!empty($currentSection['fields']) || $currentSection['includesFixedFields']) {
            $sections[] = $currentSection;
        }

        // If no sections defined, create one with all fields
        if (empty($sections)) {
            $sections[] = [
                'title' => 'Informasi Pendaftaran',
                'description' => '',
                'fields' => $formSchema,
                'includesFixedFields' => true,
            ];
        }

        $this->sections = $sections;
    }

    public function getTotalStepsProperty(): int
    {
        return count($this->sections);
    }

    public function nextStep(): void
    {
        // Validate current step before proceeding
        $this->validateCurrentStep();
        
        if ($this->currentStep < $this->totalSteps - 1) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 0) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step >= 0 && $step < $this->totalSteps) {
            $this->currentStep = $step;
        }
    }

    protected function validateCurrentStep(): void
    {
        $section = $this->sections[$this->currentStep] ?? null;
        if (!$section) return;

        $rules = [];
        $messages = [];

        // Fixed fields validation (only on first section)
        if ($section['includesFixedFields'] ?? false) {
            $rules['team_name'] = 'required|min:2|max:100';
            $rules['captain_name'] = 'required|min:2|max:100';
            $rules['whatsapp'] = 'required|regex:/^[0-9]{10,15}$/';
            
            $messages['team_name.required'] = 'Nama tim wajib diisi';
            $messages['captain_name.required'] = 'Nama kapten wajib diisi';
            $messages['whatsapp.required'] = 'Nomor WhatsApp wajib diisi';
            $messages['whatsapp.regex'] = 'Format nomor WhatsApp tidak valid (10-15 digit)';
        }

        // Dynamic fields validation for current section
        foreach ($section['fields'] as $field) {
            if (in_array($field['type'], $this->staticTypes)) continue;

            $fieldRules = [];
            $label = $field['label'] ?? 'Field';
            
            if ($field['required'] ?? false) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            switch ($field['type']) {
                case 'email':
                    $fieldRules[] = 'email';
                    $messages["answers.{$field['id']}.email"] = "{$label} harus berupa email valid";
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    $messages["answers.{$field['id']}.numeric"] = "{$label} harus berupa angka";
                    break;
                case 'file':
                    $fieldRules = [($field['required'] ?? false) ? 'required' : 'nullable', 'file'];
                    $maxSize = ($field['fileSettings']['maxSize'] ?? 1048576) / 1024;
                    $fieldRules[] = 'max:' . (int)$maxSize;
                    $messages["fileUploads.{$field['id']}.required"] = "{$label} wajib diupload";
                    $messages["fileUploads.{$field['id']}.max"] = "{$label} maksimal " . $this->formatFileSize($field['fileSettings']['maxSize'] ?? 1048576);
                    break;
                case 'checkbox':
                    $fieldRules[] = 'array';
                    break;
            }

            $messages["answers.{$field['id']}.required"] = "{$label} wajib diisi";

            if ($field['type'] === 'file') {
                $rules["fileUploads.{$field['id']}"] = implode('|', $fieldRules);
            } else {
                $rules["answers.{$field['id']}"] = implode('|', $fieldRules);
            }
        }

        $this->validate($rules, $messages);
    }

    public function getRules(): array
    {
        $rules = [
            'team_name' => 'required|min:2|max:100',
            'captain_name' => 'required|min:2|max:100',
            'whatsapp' => 'required|regex:/^[0-9]{10,15}$/',
        ];

        foreach ($this->tournament->form_schema ?? [] as $field) {
            if (in_array($field['type'], $this->staticTypes)) {
                continue;
            }

            $fieldRules = [];
            
            if ($field['required'] ?? false) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            switch ($field['type']) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'file':
                    $fieldRules = array_filter($fieldRules, fn($r) => $r !== 'required' && $r !== 'nullable');
                    $fieldRules[] = ($field['required'] ?? false) ? 'required' : 'nullable';
                    $fieldRules[] = 'file';
                    $maxSize = ($field['fileSettings']['maxSize'] ?? 1048576) / 1024;
                    $fieldRules[] = 'max:' . (int)$maxSize;
                    break;
                case 'checkbox':
                    $fieldRules[] = 'array';
                    break;
            }

            if ($field['type'] === 'file') {
                $rules["fileUploads.{$field['id']}"] = implode('|', $fieldRules);
            } else {
                $rules["answers.{$field['id']}"] = implode('|', $fieldRules);
            }
        }

        return $rules;
    }

    public function getMessages(): array
    {
        $messages = [
            'team_name.required' => 'Nama tim wajib diisi',
            'captain_name.required' => 'Nama kapten wajib diisi',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi',
            'whatsapp.regex' => 'Format nomor WhatsApp tidak valid (10-15 digit)',
        ];

        foreach ($this->tournament->form_schema ?? [] as $field) {
            $label = $field['label'] ?? 'Field';
            $messages["answers.{$field['id']}.required"] = "{$label} wajib diisi";
            $messages["answers.{$field['id']}.email"] = "{$label} harus berupa email valid";
            $messages["answers.{$field['id']}.numeric"] = "{$label} harus berupa angka";
            $messages["fileUploads.{$field['id']}.required"] = "{$label} wajib diupload";
            
            $maxSize = ($field['fileSettings']['maxSize'] ?? 1048576) / 1024;
            $messages["fileUploads.{$field['id']}.max"] = "{$label} maksimal " . $this->formatFileSize($field['fileSettings']['maxSize'] ?? 1048576);
        }

        return $messages;
    }

    protected function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        return round($bytes / 1024) . ' KB';
    }

    public function submit(): void
    {
        if ($this->tournament->isFull()) {
            session()->flash('error', 'Maaf, slot pendaftaran sudah penuh!');
            return;
        }

        $this->validate($this->getRules(), $this->getMessages());

        $submissionData = $this->answers;

        foreach ($this->fileUploads as $fieldId => $file) {
            if ($file) {
                $path = $file->store('submissions/' . $this->tournament->slug, 'public');
                $submissionData[$fieldId] = $path;
            }
        }

        Participant::create([
            'tournament_id' => $this->tournament->id,
            'team_name' => $this->team_name,
            'captain_name' => $this->captain_name,
            'whatsapp' => $this->whatsapp,
            'submission_data' => $submissionData,
            'payment_status' => 'pending',
            'registered_at' => now(),
        ]);

        $this->submitted = true;
        $this->dispatch('registration-submitted');
    }

    public function render()
    {
        return view('livewire.public-registration', [
            'staticTypes' => $this->staticTypes,
            'currentSection' => $this->sections[$this->currentStep] ?? null,
        ])->layout('layouts.public', ['title' => $this->tournament->title]);
    }
}
