{{-- Daftar agenda bulan ini. Butuh: $agenda, $projectRoute --}}
<div class="rounded-2xl border border-stone-200 bg-white">
    <h2 class="border-b border-stone-100 px-5 py-3 text-sm font-semibold text-stone-900">Agenda Bulan Ini</h2>
    @forelse ($agenda as $event)
        <div class="flex items-center gap-3 border-b border-stone-100 px-5 py-3 last:border-0 {{ $event['pending'] || $event['dimmed'] ? 'opacity-60' : '' }}">
            <div class="w-14 shrink-0 text-center">
                <p class="text-lg font-bold leading-none text-stone-900">{{ $event['date']->format('d') }}</p>
                <p class="text-xs text-stone-400">{{ $event['date']->locale('id')->translatedFormat('M Y') }}</p>
            </div>
            <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $event['type'] === 'tanam' ? 'bg-emerald-500' : ($event['type'] === 'panen' ? 'bg-amber-400' : 'bg-sky-500') }}"></span>
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-stone-900">{{ $event['label'] }}</p>
                <a href="{{ route($projectRoute, $event['project']) }}" class="text-sm text-emerald-800 hover:underline">{{ $event['project']->name }}</a>
                @if ($event['expense'])
                    <span class="text-sm text-stone-400">&middot; Rp{{ number_format($event['expense']->total_price, 0, ',', '.') }}</span>
                @endif
            </div>
            @if ($event['pending'])
                <span class="ml-auto shrink-0 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Menunggu approval</span>
            @endif
        </div>
    @empty
        <p class="px-5 py-6 text-center text-sm text-stone-400">Tidak ada agenda di bulan ini.</p>
    @endforelse
</div>
