<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Exports\ParticipantsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function participants(Tournament $tournament)
    {
        abort_if($tournament->user_id !== auth()->id(), 403);

        $filename = 'peserta-' . $tournament->slug . '-' . now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new ParticipantsExport($tournament), $filename);
    }
}
