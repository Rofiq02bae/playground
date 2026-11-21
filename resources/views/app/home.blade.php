@extends('app.layouts.app')

@section('title', 'Beranda - FunFarm WebApp')

@section('content')
    <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-700 text-base font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            <div class="min-w-0">
                <h1 class="truncate text-lg font-semibold text-stone-900">Halo, {{ $user->name }}</h1>
                <p class="text-sm text-stone-500">{{ $user->roleLabel() }} FunFarm</p>
            </div>
        </div>

        @if ($user->isPemilik())
            <p class="mt-4 inline-block rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800">Status: {{ $user->membershipLabel() }}{{ $user->is_member ? ' — harga sewa dapat potongan' : ' — harga sewa normal' }}</p>
            <p class="mt-3 text-sm leading-relaxed text-stone-600">Anda dapat <strong>melihat data</strong> sawah, pencatatan, dan laporan. Penambahan data dilakukan penggarap dan disetujui perusahaan.</p>
        @elseif ($user->isPenggarap())
            <p class="mt-4 text-sm leading-relaxed text-stone-600">Anda dapat <strong>melihat dan menambah data</strong> pencatatan, serta <strong>melihat laporan</strong> proyek yang digarap. Data baru menunggu <strong>persetujuan perusahaan</strong> sebelum masuk laporan resmi.</p>
        @else
            <p class="mt-4 text-sm leading-relaxed text-stone-600">Role admin (perusahaan) memiliki <strong>semua akses</strong>: blog, webapp, persetujuan data, dan pengelolaan pengguna.</p>
            <a href="{{ route('admin.users.index') }}" class="mt-4 inline-block rounded-xl bg-emerald-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-800 active:bg-emerald-900">Kelola Pengguna</a>
        @endif
    </div>

    {{-- Ringkasan cepat: info terpenting sekilas, tanpa perlu membuka menu --}}
    <div class="mt-4 grid grid-cols-3 gap-3">
        <a href="{{ route('app.sawah.index') }}" class="rounded-2xl border border-stone-200 bg-white p-3.5 text-center shadow-sm transition active:bg-stone-50">
            <p class="text-2xl font-bold leading-none text-stone-900">{{ $activeProjects }}</p>
            <p class="mt-1.5 text-[11px] font-medium leading-tight text-stone-500">Proyek aktif</p>
        </a>
        <a href="{{ route('app.calendar.index') }}" class="rounded-2xl border {{ $harvestSoon > 0 ? 'border-amber-300 bg-amber-50' : 'border-stone-200 bg-white' }} p-3.5 text-center shadow-sm transition active:bg-stone-50">
            <p class="text-2xl font-bold leading-none {{ $harvestSoon > 0 ? 'text-amber-800' : 'text-stone-900' }}">{{ $harvestSoon }}</p>
            <p class="mt-1.5 text-[11px] font-medium leading-tight {{ $harvestSoon > 0 ? 'text-amber-700' : 'text-stone-500' }}">Panen &le; 7 hari</p>
        </a>
        <a href="{{ route('app.sawah.index') }}" class="rounded-2xl border border-stone-200 bg-white p-3.5 text-center shadow-sm transition active:bg-stone-50">
            <p class="text-2xl font-bold leading-none text-stone-900">{{ $pendingCount }}</p>
            <p class="mt-1.5 text-[11px] font-medium leading-tight text-stone-500">Menunggu persetujuan</p>
        </a>
    </div>

    <h2 class="mb-3 mt-6 text-sm font-semibold uppercase tracking-wide text-stone-500">Menu</h2>
    <div class="grid grid-cols-2 gap-3">
        @php($menuCards = [
            ['route' => 'app.sawah.index', 'title' => 'Profil Sawah', 'desc' => 'Lihat sawah & proyek Anda', 'icon' => '<path d="M12 21s-7-5.6-7-11a7 7 0 1 1 14 0c0 5.4-7 11-7 11z" /><circle cx="12" cy="10" r="2.5" />'],
            ['route' => 'app.sawah.index', 'title' => 'Pencatatan Pupuk & Obat', 'desc' => $user->isPemilik() ? 'Lihat riwayat catatan sawah' : 'Buka sawah, lalu tambah catatan', 'icon' => '<path d="M12 20h9" /><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" />'],
            ['route' => 'app.calendar.index', 'title' => 'Kalender Tanam & Panen', 'desc' => 'Jadwal tanam, catatan & estimasi panen', 'icon' => '<rect x="4" y="5" width="16" height="16" rx="2" /><path d="M4 10h16M8 3v4M16 3v4" />'],
            ['route' => 'app.reports.index', 'title' => 'Laporan Biaya Produksi', 'desc' => 'Biaya, hasil panen & bagi hasil', 'icon' => '<path d="M7 20v-6M12 20V6M17 20v-9" /><path d="M3 20h18" />'],
            ['route' => 'app.agreements.index', 'title' => 'Surat Perjanjian', 'desc' => $agreementCount > 0 ? $agreementCount.' surat siap diunduh' : 'Lihat & unduh surat dari perusahaan', 'icon' => '<path d="M6 3h9l4 4v14H6z" /><path d="M15 3v4h4" /><path d="M9 12h6M9 16h6" />'],
        ])
        @foreach ($menuCards as $card)
            <a href="{{ route($card['route']) }}" class="group rounded-2xl border border-stone-200 bg-white p-4 shadow-sm transition hover:border-emerald-300 active:bg-emerald-50/50">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $card['icon'] !!}</svg>
                </span>
                <p class="mt-3 flex items-center gap-1 text-sm font-semibold text-stone-900">{{ $card['title'] }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-stone-300 transition group-hover:text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 6 6 6-6 6" /></svg>
                </p>
                <p class="mt-1 text-xs leading-relaxed text-stone-500">{{ $card['desc'] }}</p>
            </a>
        @endforeach
    </div>
@endsection
