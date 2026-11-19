@extends('admin.layouts.app')

@section('title', 'Layanan & Harga - Admin FunFarm')

@section('content')
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <div>
            <h1 class="text-xl font-semibold text-stone-900">Layanan &amp; Harga Sewa</h1>
            <p class="text-sm text-stone-500">Tarif resmi alat. Pencatatan sewa memakai harga sesuai status membership pemilik sawah.</p>
        </div>
        <a href="{{ route('admin.services.create') }}" class="ml-auto rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">+ Layanan Baru</a>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead>
                <tr class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
                    <th class="px-4 py-3 font-semibold">Layanan</th>
                    <th class="px-4 py-3 font-semibold">Cabang</th>
                    <th class="px-4 py-3 text-right font-semibold">Harga Normal</th>
                    <th class="px-4 py-3 text-right font-semibold">Harga Member</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($services as $service)
                    <tr class="border-b border-stone-100 last:border-0">
                        <td class="px-4 py-3"><span class="font-medium text-stone-900">{{ $service->name }}</span>@if ($service->description)<br><span class="text-xs text-stone-400">{{ $service->description }}</span>@endif</td>
                        <td class="px-4 py-3 text-stone-600">{{ $service->branchLabel() }}</td>
                        <td class="px-4 py-3 text-right text-stone-900">Rp{{ number_format($service->price_normal, 0, ',', '.') }}<span class="text-xs text-stone-400"> / {{ $service->unit }}</span></td>
                        <td class="px-4 py-3 text-right font-medium text-emerald-800">Rp{{ number_format($service->price_member, 0, ',', '.') }}<span class="text-xs text-stone-400"> / {{ $service->unit }}</span></td>
                        <td class="px-4 py-3">{{ $service->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.services.edit', $service) }}" class="rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-medium text-stone-700 transition hover:bg-stone-50">Ubah</a>
                                <form method="POST" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm('Hapus layanan {{ $service->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-700 transition hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-stone-500">Belum ada layanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
