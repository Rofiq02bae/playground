<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\AgriculturalProject;
use App\Support\CalendarData;
use App\Support\ProjectReport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Laporan di webapp: pemilik melihat proyeknya sendiri, admin semua.
     * Penggarap tidak punya akses laporan (urusan uang bukan ranahnya).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if($user->isPenggarap(), 403, 'Laporan hanya untuk pemilik sawah dan perusahaan.');

        return view('app.reports.index', [
            'summaries' => ProjectReport::summaries(CalendarData::scopedProjects($user)),
        ]);
    }

    public function show(Request $request, AgriculturalProject $project)
    {
        $user = $request->user();
        abort_if($user->isPenggarap(), 403, 'Laporan hanya untuk pemilik sawah dan perusahaan.');
        abort_if($user->isPemilik() && $project->owner_id !== $user->id, 404);

        return view('app.reports.show', ProjectReport::forProject($project));
    }
}
