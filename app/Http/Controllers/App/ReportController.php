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
     * Laporan di webapp untuk semua role, ter-scope per peran:
     * admin semua proyek, pemilik proyeknya sendiri, penggarap proyek yang ia garap.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return view('app.reports.index', [
            'summaries' => ProjectReport::summaries(CalendarData::scopedProjects($user)),
        ]);
    }

    public function show(Request $request, AgriculturalProject $project)
    {
        $user = $request->user();
        abort_unless($project->isVisibleTo($user), 404);

        return view('app.reports.show', ProjectReport::forProject($project));
    }
}
