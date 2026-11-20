@extends('app.layouts.app')

@section('title', 'Beranda - FunFarm WebApp')

@section('content')
    <div class="rounded-2xl border border-stone-200 bg-white p-5">
        <h1 class="text-lg font-semibold text-stone-900">Halo, {{ $user->name }}</h1>
        <p class="mt-1 text-sm text-stone-500">Halo <strong class="text-stone-700">{{ $user->roleLabel() }}</strong>.</p>

        @if ($user->isPemilik())
            <p class="mt-3 inline-block rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800">Status: {{ $user->membershipLabel() }}{{ $user->is_member ? ' — harga sewa dapat potongan' : ' — harga sewa normal' }}</p>
            <p class="mt-3 text-sm text-stone-600">Role pemilik hanya dapat <strong>melihat data</strong>: profil sawah, pencatatan, dan laporan. Penambahan data dilakukan penggarap dan disetujui perusahaan.</p>
        @elseif ($user->isPenggarap())
            <p class="mt-3 text-sm text-stone-600">Role penggarap dapat <strong>melihat dan menambah data</strong> pencatatan. Setiap data yang ditambahkan menunggu <strong>persetujuan perusahaan</strong> sebelum masuk laporan resmi.</p>
        @else
            <p class="mt-3 text-sm text-stone-600">Role admin (perusahaan) memiliki <strong>semua akses</strong>: blog, webapp, persetujuan data, dan pengelolaan pengguna.</p>
            <a href="{{ route('admin.users.index') }}" class="mt-4 inline-block rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Kelola Pengguna</a>
        @endif
    </div>

    <div class="mt-4 grid grid-cols-2 gap-3">
        <a href="{{ route('app.sawah.index') }}" class="rounded-2xl border border-emerald-200 bg-white p-4">
            <p class="text-sm font-semibold text-emerald-900">Profil Sawah</p>
            <p class="mt-1 text-xs text-stone-500">Lihat sawah &amp; proyek Anda</p>
        </a>
        <a href="{{ route('app.sawah.index') }}" class="rounded-2xl border border-emerald-200 bg-white p-4">
            <p class="text-sm font-semibold text-emerald-900">Pencatatan Pupuk &amp; Obat</p>
            <p class="mt-1 text-xs text-stone-500">Buka sawah, lalu tambah catatan</p>
        </a>
        <a href="{{ route('app.calendar.index') }}" class="rounded-2xl border border-emerald-200 bg-white p-4">
            <p class="text-sm font-semibold text-emerald-900">Kalender Tanam &amp; Panen</p>
        </a>
        <a href="{{ route('app.reports.index') }}" class="rounded-2xl border border-emerald-200 bg-white p-4">
            <p class="text-sm font-semibold text-emerald-900">Laporan Biaya Produksi</p>
            <p class="mt-1 text-xs text-stone-500">laporan pengeluaran dan pemasukan</p>
        </a>
        <a href="{{ route('') }}" class="rounded-2xl border border-emerald-200 bg-white p-4">
            <p class="text-sm font-semibold text-emerald-900">Surat Perjanjian</p>
            <p class="mt-1 text-xs text-stone-500">surat perjanjian penggarapan sawah</p>
        </a>
    </div>
@endsection
