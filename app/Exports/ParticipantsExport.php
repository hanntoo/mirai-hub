<?php

namespace App\Exports;

use App\Models\Tournament;
use App\Models\Participant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ParticipantsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected Tournament $tournament;
    protected array $dynamicFields = [];

    public function __construct(Tournament $tournament)
    {
        $this->tournament = $tournament;
        
        foreach ($tournament->form_schema ?? [] as $field) {
            if (!in_array($field['type'], ['image_block', 'text_block', 'link_block'])) {
                $this->dynamicFields[$field['id']] = $field['label'] ?? $field['id'];
            }
        }
    }

    public function collection()
    {
        return Participant::where('tournament_id', $this->tournament->id)
            ->orderBy('registered_at')
            ->get();
    }

    public function headings(): array
    {
        $headers = [
            'No',
            'Nama Tim',
            'Nama Kapten',
            'WhatsApp',
            'Status Pembayaran',
            'Tanggal Daftar',
        ];

        foreach ($this->dynamicFields as $label) {
            $headers[] = $label;
        }

        return $headers;
    }

    public function map($participant): array
    {
        static $index = 0;
        $index++;

        $row = [
            $index,
            $participant->team_name,
            $participant->captain_name,
            $participant->whatsapp,
            ucfirst($participant->payment_status),
            $participant->registered_at?->format('d/m/Y H:i'),
        ];

        foreach ($this->dynamicFields as $fieldId => $label) {
            $value = $participant->submission_data[$fieldId] ?? '-';
            
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            
            $row[] = $value;
        }

        return $row;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '06b6d4'],
                ],
            ],
        ];
    }
}
