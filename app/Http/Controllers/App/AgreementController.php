<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\AgriculturalProject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AgreementController extends Controller
{
    /** Daftar surat perjanjian untuk proyek yang boleh dilihat user. */
    public function index()
    {
        $projects = AgriculturalProject::query()
            ->visibleTo(Auth::user())
            ->where('rental_type', 'bagi_hasil')
            ->with(['owner', 'agreement'])
            ->latest()
            ->get();

        return view('app.agreements.index', [
            'projects' => $projects,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Unduh PDF surat perjanjian dari webapp.
     * Surat adalah dokumen antara pemilik & perusahaan: yang boleh
     * mengunduh hanya admin dan pemilik proyek itu sendiri.
     * Penggarap bukan pihak dalam surat -> 403.
     */
    public function download(AgriculturalProject $project)
    {
        $user = Auth::user();

        abort_unless($project->isVisibleTo($user), 404);
        abort_unless($user->isAdmin() || ($user->isPemilik() && $project->owner_id === $user->id), 403);

        $agreement = $project->agreement;
        abort_unless($agreement && Storage::disk('local')->exists($agreement->pdf_path), 404);

        $filename = 'Surat-Perjanjian-'.preg_replace('/[^A-Za-z0-9]+/', '-', $agreement->number).'.pdf';

        return Storage::disk('local')->download($agreement->pdf_path, $filename);
    }
}
