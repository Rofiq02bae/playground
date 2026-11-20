<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgriculturalProject;
use App\Support\ProjectReport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /** Rekap laporan biaya produksi semua proyek (desktop perusahaan). */
    public function index(Request $request)
    {
        $summaries = ProjectReport::summaries(AgriculturalProject::query());

        return view('admin.reports.index', [
            'summaries' => $summaries,
            'grand' => [
                'approvedTotal' => $summaries->sum('approvedTotal'),
                'harvestRevenue' => $summaries->sum('harvestRevenue'),
                'netProfit' => $summaries->sum('netProfit'),
                'companyShare' => $summaries->sum('companyShare'),
                'ownerShare' => $summaries->sum('ownerShare'),
            ],
        ]);
    }

    /** Laporan per proyek, siap cetak (browser -> PDF). */
    public function show(AgriculturalProject $project)
    {
        return view('admin.reports.show', ProjectReport::forProject($project));
    }
}
