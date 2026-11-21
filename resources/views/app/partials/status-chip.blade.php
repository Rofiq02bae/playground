{{-- Chip status proyek: warna mengikuti makna status. Butuh: $project --}}
@php($chipClass = match ($project->status) {
    'berjalan' => 'bg-emerald-100 text-emerald-800',
    'menunggu_panen' => 'bg-amber-100 text-amber-800',
    'selesai' => 'bg-sky-100 text-sky-800',
    'dibatalkan' => 'bg-red-100 text-red-700',
    default => 'bg-stone-200 text-stone-600',
})
<span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $chipClass }}">{{ $project->statusLabel() }}</span>
