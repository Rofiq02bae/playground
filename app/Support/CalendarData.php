<?php

namespace App\Support;

use App\Models\AgriculturalProject;
use App\Models\ProjectExpense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Kalender tanam & panen v1: semua event adalah tanggal turunan dari
 * data yang sudah ada — start_date & estimated_harvest_date proyek,
 * dan tanggal pencatatan (project_expenses.date). Tidak ada entri
 * manual; expense pending tampil sebagai event redup, expense yang
 * ditolak tidak ditampilkan.
 */
class CalendarData
{
    /** Query proyek sesuai role: admin semua, pemilik miliknya, penggarap tugasnya. */
    public static function scopedProjects(User $user): Builder
    {
        $query = AgriculturalProject::query()->with(['owner', 'cultivator']);

        if ($user->isPemilik()) {
            $query->where('owner_id', $user->id);
        } elseif ($user->isPenggarap()) {
            $query->where('cultivator_id', $user->id);
        }

        return $query;
    }

    /**
     * @return array{month: Carbon, days: array<int, Carbon>, byDate: array<string, array<int, array>>, agenda: array<int, array>, upcoming: Collection, prev: string, next: string}
     */
    public static function forMonth(User $user, Carbon $month): array
    {
        $scoped = self::scopedProjects($user);
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        // Proyek dibatalkan tidak ditampilkan; proyek selesai tampil redup.
        $projects = (clone $scoped)->where('status', '!=', 'dibatalkan')->get();
        $byId = $projects->keyBy('id');

        $events = [];

        foreach ($projects as $project) {
            $dimmed = $project->status === 'selesai';

            if ($project->start_date->between($start, $end)) {
                $events[] = self::event($project->start_date, 'tanam', $project, 'Mulai tanam', false, $dimmed);
            }

            if ($project->estimated_harvest_date?->between($start, $end)) {
                $events[] = self::event($project->estimated_harvest_date, 'panen', $project, 'Estimasi panen', false, $dimmed);
            }
        }

        $expenses = ProjectExpense::query()
            ->whereIn('project_id', $byId->keys())
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->where('status', '!=', ProjectExpense::STATUS_REJECTED)
            ->orderBy('date')
            ->get();

        foreach ($expenses as $expense) {
            $project = $byId[$expense->project_id];
            $events[] = self::event(
                $expense->date,
                'catat',
                $project,
                $expense->categoryLabel().': '.$expense->name,
                $expense->status === ProjectExpense::STATUS_PENDING,
                $project->status === 'selesai',
                $expense,
            );
        }

        usort($events, fn (array $a, array $b) => $a['date']->timestamp <=> $b['date']->timestamp);

        $byDate = [];
        foreach ($events as $event) {
            $byDate[$event['date']->toDateString()][] = $event;
        }

        $gridStart = $start->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $end->copy()->endOfWeek(Carbon::SUNDAY);
        $days = [];
        for ($day = $gridStart->copy(); $day->lte($gridEnd); $day->addDay()) {
            $days[] = $day->copy();
        }

        return [
            'month' => $month,
            'days' => $days,
            'byDate' => $byDate,
            'agenda' => $events,
            'upcoming' => self::upcomingHarvest($scoped),
            'prev' => $month->copy()->subMonth()->format('Y-m'),
            'next' => $month->copy()->addMonth()->format('Y-m'),
        ];
    }

    /** Strip "Panen Segera": estimasi panen <= 7 hari ke depan, status berjalan/menunggu panen. */
    public static function upcomingHarvest(Builder $scoped): Collection
    {
        $today = Carbon::today();

        return (clone $scoped)
            ->whereIn('status', ['berjalan', 'menunggu_panen'])
            ->whereNotNull('estimated_harvest_date')
            ->whereBetween('estimated_harvest_date', [$today->toDateString(), $today->copy()->addDays(7)->toDateString()])
            ->orderBy('estimated_harvest_date')
            ->get();
    }

    private static function event(Carbon $date, string $type, AgriculturalProject $project, string $label, bool $pending, bool $dimmed, ?ProjectExpense $expense = null): array
    {
        return [
            'date' => $date->copy(),
            'type' => $type, // tanam | panen | catat
            'project' => $project,
            'label' => $label,
            'pending' => $pending,
            'dimmed' => $dimmed,
            'expense' => $expense,
        ];
    }

    public static function parseMonth(?string $value): Carbon
    {
        if ($value && preg_match('/^\d{4}-\d{2}$/', $value)) {
            try {
                return Carbon::createFromFormat('Y-m', $value)->startOfMonth();
            } catch (\Throwable) {
                // jatuh ke bulan berjalan
            }
        }

        return Carbon::today()->startOfMonth();
    }
}
