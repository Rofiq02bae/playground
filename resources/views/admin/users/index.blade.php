@extends('admin.layouts.app')

@section('title', 'Pengguna - Admin FunFarm')

@section('content')
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <div>
            <h1 class="text-xl font-semibold text-stone-900">Pengguna &amp; Role</h1>
            <p class="text-sm text-stone-500">Akun dibuatkan oleh perusahaan. Pemilik &amp; penggarap login pakai username, admin pakai email/username.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="ml-auto rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">+ Tambah Pengguna</a>
    </div>

    <form method="GET" class="mb-5 flex flex-wrap gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, username, email, HP..."
               class="w-full max-w-xs rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
        <select name="role" class="rounded-xl border border-stone-300 px-3 py-2.5 text-sm outline-none focus:border-emerald-600">
            <option value="">Semua role</option>
            @foreach ($roles as $role)
                <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-xl border border-stone-300 px-4 py-2.5 text-sm font-medium text-stone-700 transition hover:bg-stone-50">Filter</button>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead>
                <tr class="border-b border-stone-200 bg-stone-50 text-xs uppercase tracking-wide text-stone-500">
                    <th class="px-4 py-3 font-semibold">Nama</th>
                    <th class="px-4 py-3 font-semibold">Username / Email</th>
                    <th class="px-4 py-3 font-semibold">Role</th>
                    <th class="px-4 py-3 font-semibold">Membership</th>
                    <th class="px-4 py-3 font-semibold">Telepon</th>
                    <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-b border-stone-100 last:border-0">
                        <td class="px-4 py-3"><span class="font-medium text-stone-900">{{ $user->name }}</span>@if ($user->address)<br><span class="text-xs font-normal text-stone-400">{{ $user->address }}</span>@endif</td>
                        <td class="px-4 py-3 text-stone-600">{{ $user->username ?? '-' }}<br><span class="text-xs text-stone-400">{{ $user->email ?? 'tanpa email' }}</span></td>
                        <td class="px-4 py-3"><span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">{{ $user->roleLabel() }}</span></td>
                        <td class="px-4 py-3">{{ $user->isPemilik() ? $user->membershipLabel() : '-' }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $user->phone ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="rounded-lg border border-stone-300 px-3 py-1.5 text-xs font-medium text-stone-700 transition hover:bg-stone-50">Ubah</a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus pengguna {{ $user->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-700 transition hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-stone-500">Belum ada pengguna yang cocok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $users->links() }}</div>
@endsection
