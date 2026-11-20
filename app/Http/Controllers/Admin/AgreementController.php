<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgriculturalProject;
use App\Support\AgreementGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AgreementController extends Controller
{
    /** Generate surat perjanjian untuk proyek bagi hasil (sekali per proyek). */
    public function store(Request $request, AgriculturalProject $project)
    {
        abort_unless($project->isBagiHasil(), 404);
        abort_if($project->agreement()->exists(), 422, 'Surat perjanjian untuk proyek ini sudah dibuat.');

        $data = $request->validate([
            'witness1_name' => ['required', 'string', 'max:255'],
            'witness2_name' => ['required', 'string', 'max:255'],
        ]);

        $agreement = AgreementGenerator::generate($project, $data['witness1_name'], $data['witness2_name'], Auth::user());

        return redirect()->route('admin.projects.show', $project)
            ->with('success', "Surat perjanjian nomor {$agreement->number} berhasil dibuat. PDF tersimpan dan bisa diunduh kapan pun dari halaman ini.");
    }

    /** Unduh ulang PDF surat perjanjian yang sudah tersimpan. */
    public function download(AgriculturalProject $project)
    {
        $agreement = $project->agreement;
        abort_unless($agreement && Storage::disk('local')->exists($agreement->pdf_path), 404);

        $filename = 'Surat-Perjanjian-'.preg_replace('/[^A-Za-z0-9]+/', '-', $agreement->number).'.pdf';

        return Storage::disk('local')->download($agreement->pdf_path, $filename);
    }
}
