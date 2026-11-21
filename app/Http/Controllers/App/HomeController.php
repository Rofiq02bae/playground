<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Beranda webapp pencatatan (mobile view).
     * Menu lengkap (profil sawah, pencatatan, kalender, laporan, surat)
     * dibangun bertahap; fondasi role & aksesnya mulai dari sini.
     */
    public function index()
    {
        $user = Auth::user();
        $scoped = \App\Support\CalendarData::scopedProjects($user);
        $projectIds = (clone $scoped)->pluck('id');

        return view('app.home', [
            'user' => $user,
            'agreementCount' => \App\Models\AgriculturalProject::query()
                ->visibleTo($user)
                ->whereHas('agreement')
                ->count(),
            // Ringkasan cepat untuk beranda: proyek aktif, panen <= 7 hari, catatan menunggu persetujuan.
            'activeProjects' => (clone $scoped)->whereIn('status', ['persiapan', 'berjalan', 'menunggu_panen'])->count(),
            'harvestSoon' => \App\Support\CalendarData::upcomingHarvest($scoped)->count(),
            'pendingCount' => \App\Models\ProjectExpense::query()
                ->whereIn('project_id', $projectIds)
                ->where('status', \App\Models\ProjectExpense::STATUS_PENDING)
                ->count(),
        ]);
    }
}
