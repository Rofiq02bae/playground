{{-- Strip "Panen Segera": estimasi panen <= 7 hari ke depan (dari App\Support\CalendarData). Butuh: $upcoming, $projectRoute --}}
@if ($upcoming->isNotEmpty())
    <div class="mb-6">
        <h2 class="mb-2 text-sm font-semibold text-amber-800">Panen Segera (7 hari ke depan)</h2>
        <div class="flex gap-3 overflow-x-auto pb-1">
            @foreach ($upcoming as $project)
                @php($daysLeft = (int) now()->startOfDay()->diffInDays($project->estimated_harvest_date->copy()->startOfDay(), false))
                <a href="{{ route($projectRoute, $project) }}" class="w-64 shrink-0 rounded-2xl border border-amber-200 bg-amber-50 p-4 transition hover:bg-amber-100">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Estimasi panen {{ $project->estimated_harvest_date->format('d M Y') }}</p>
                    <p class="mt-1 font-semibold text-stone-900">{{ $project->name }}</p>
                    <p class="text-sm text-stone-500">{{ $project->field_name }} &middot; {{ $project->owner->name }}</p>
                    <p class="mt-1 text-sm font-medium text-amber-800">{{ $daysLeft <= 0 ? 'Hari ini' : ($daysLeft === 1 ? 'Besok' : $daysLeft.' hari lagi') }} &middot; {{ $project->statusLabel() }}</p>
                </a>
            @endforeach
        </div>
    </div>
@endif
