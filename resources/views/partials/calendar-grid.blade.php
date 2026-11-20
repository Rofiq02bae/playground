{{-- Grid kalender bulanan. Butuh: $days, $byDate, $month, $projectRoute; opsional: $compact (mode ringkas webapp) --}}
@php($compact = $compact ?? false)
<div class="overflow-hidden rounded-2xl border border-stone-200 bg-white">
    <div class="grid grid-cols-7 border-b border-stone-200 bg-stone-50 text-center text-xs font-semibold uppercase tracking-wide text-stone-500">
        @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dayName)
            <div class="px-1 py-2">{{ $dayName }}</div>
        @endforeach
    </div>
    <div class="grid grid-cols-7">
        @foreach ($days as $day)
            @php($dayEvents = $byDate[$day->toDateString()] ?? [])
            @php($inMonth = $day->month === $month->month)
            <div class="{{ $compact ? 'min-h-[58px]' : 'min-h-[96px]' }} border-b border-r border-stone-100 p-1.5 [&:nth-child(7n)]:border-r-0 {{ $inMonth ? '' : 'bg-stone-50/60' }}">
                <p class="text-xs {{ $day->isToday() ? 'flex h-5 w-5 items-center justify-center rounded-full bg-emerald-700 font-bold text-white' : ($inMonth ? 'font-medium text-stone-700' : 'text-stone-300') }}">{{ $day->day }}</p>
                @if ($compact)
                    @if ($dayEvents)
                        <div class="mt-1 flex flex-wrap gap-1">
                            @foreach ($dayEvents as $event)
                                <span class="h-2 w-2 rounded-full {{ $event['type'] === 'tanam' ? 'bg-emerald-500' : ($event['type'] === 'panen' ? 'bg-amber-400' : 'bg-sky-500') }} {{ $event['pending'] || $event['dimmed'] ? 'opacity-40' : '' }}"></span>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="mt-1 space-y-1">
                        @foreach (array_slice($dayEvents, 0, 3) as $event)
                            <a href="{{ route($projectRoute, $event['project']) }}" title="{{ $event['label'] }} — {{ $event['project']->name }}{{ $event['pending'] ? ' (menunggu approval)' : '' }}"
                               class="block truncate rounded px-1.5 py-0.5 text-[11px] font-medium {{ $event['type'] === 'tanam' ? 'bg-emerald-100 text-emerald-900' : ($event['type'] === 'panen' ? 'bg-amber-100 text-amber-900' : 'bg-sky-100 text-sky-900') }} {{ $event['pending'] || $event['dimmed'] ? 'opacity-50' : '' }}">{{ $event['label'] }}</a>
                        @endforeach
                        @if (count($dayEvents) > 3)
                            <p class="px-1 text-[11px] text-stone-400">+{{ count($dayEvents) - 3 }} lagi</p>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
