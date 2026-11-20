<?php

namespace App\Support;

use App\Models\AgriculturalProject;
use App\Models\ProjectExpense;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Laporan biaya produksi & bagi hasil per proyek.
 *
 * Aturan angka (konsisten dengan modul pencatatan):
 * - Biaya = hanya expense berstatus approved. Pending & rejected
 *   TIDAK dihitung (pending hanya ditampilkan sebagai info).
 * - Hasil = total seluruh catatan panen (harvest_records).
 * - Laba bersih = hasil panen - total biaya.
 * - Bagi hasil dari laba bersih positif memakai
 *   profit_share_company_percent proyek; kalau belum ada laba
 *   (bersih <= 0), bagian perusahaan 0 dan sisanya (rugi) di pemilik.
 */
class ProjectReport
{
    public static function forProject(AgriculturalProject $project): array
    {
        $project->loadMissing(['owner', 'cultivator']);

        $approved = $project->approvedExpenses()->with('creator')->orderBy('date')->get();

        $byCategory = [];
        foreach ($approved as $expense) {
            $byCategory[$expense->category]['label'] = $expense->categoryLabel();
            $byCategory[$expense->category]['total'] = ($byCategory[$expense->category]['total'] ?? 0) + $expense->total_price;
            $byCategory[$expense->category]['count'] = ($byCategory[$expense->category]['count'] ?? 0) + 1;
        }

        $approvedTotal = (int) $approved->sum('total_price');

        $pending = $project->expenses()->where('status', ProjectExpense::STATUS_PENDING)->get();

        $harvests = $project->harvests()->with('creator')->orderBy('harvest_date')->get();
        $harvestQuantity = (float) $harvests->sum('quantity_kg');
        $harvestRevenue = (int) $harvests->sum('total_revenue');

        $netProfit = $harvestRevenue - $approvedTotal;
        [$companyShare, $ownerShare] = self::shares($netProfit, $project->profit_share_company_percent);

        return [
            'project' => $project,
            'expenses' => $approved,
            'byCategory' => $byCategory,
            'approvedTotal' => $approvedTotal,
            'pendingCount' => $pending->count(),
            'pendingTotal' => (int) $pending->sum('total_price'),
            'harvests' => $harvests,
            'harvestQuantity' => $harvestQuantity,
            'harvestRevenue' => $harvestRevenue,
            'netProfit' => $netProfit,
            'companyPercent' => $project->profit_share_company_percent,
            'companyShare' => $companyShare,
            'ownerShare' => $ownerShare,
        ];
    }

    /** @return array{0: int, 1: int} [bagian perusahaan, bagian pemilik] */
    public static function shares(int $netProfit, int $companyPercent): array
    {
        if ($netProfit <= 0) {
            return [0, $netProfit];
        }

        $company = (int) round($netProfit * $companyPercent / 100);

        return [$company, $netProfit - $company];
    }

    /**
     * Ringkasan banyak proyek untuk rekap global / daftar laporan webapp.
     * Setiap item: project + approvedTotal + harvestRevenue + harvestQuantity
     * + netProfit + companyShare + ownerShare. Proyek dibatalkan dikecualikan.
     */
    public static function summaries(Builder $scopedProjects): Collection
    {
        $projects = (clone $scopedProjects)
            ->where('status', '!=', 'dibatalkan')
            ->with(['owner', 'cultivator'])
            ->withSum(['expenses as approved_total' => fn ($q) => $q->where('status', ProjectExpense::STATUS_APPROVED)], 'total_price')
            ->withSum('harvests as revenue_total', 'total_revenue')
            ->withSum('harvests as quantity_total', 'quantity_kg')
            ->orderBy('name')
            ->get();

        return $projects->map(function (AgriculturalProject $project) {
            $approvedTotal = (int) ($project->approved_total ?? 0);
            $revenue = (int) ($project->revenue_total ?? 0);
            $net = $revenue - $approvedTotal;
            [$company, $owner] = self::shares($net, $project->profit_share_company_percent);

            return [
                'project' => $project,
                'approvedTotal' => $approvedTotal,
                'harvestRevenue' => $revenue,
                'harvestQuantity' => (float) ($project->quantity_total ?? 0),
                'netProfit' => $net,
                'companyShare' => $company,
                'ownerShare' => $owner,
            ];
        });
    }
}
