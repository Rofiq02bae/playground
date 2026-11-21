@extends('shop.layouts.app')

@section('title', 'Daftar Akun Pembeli - Produk FunFarm')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rounded-3xl border border-stone-200 bg-white p-6 sm:p-8">
            <h1 class="text-xl font-bold text-stone-900">Daftar Akun Pembeli</h1>
            <p class="mt-1 text-sm text-stone-500">Akun diperlukan saat checkout agar data identitas &amp; alamat pengirimanmu lengkap.</p>

            <form method="POST" action="{{ route('shop.register.submit') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-stone-700">Nama lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="w-full rounded-xl border border-stone-300 px-3.5 py-3 text-base outline-none focus:border-emerald-600">
                    @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-stone-700">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="w-full rounded-xl border border-stone-300 px-3.5 py-3 text-base outline-none focus:border-emerald-600">
                    @error('email')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="phone" class="mb-1.5 block text-sm font-medium text-stone-700">No. WhatsApp / HP</label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel" class="w-full rounded-xl border border-stone-300 px-3.5 py-3 text-base outline-none focus:border-emerald-600">
                    @error('phone')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-stone-700">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full rounded-xl border border-stone-300 px-3.5 py-3 text-base outline-none focus:border-emerald-600">
                    <p class="mt-1 text-xs text-stone-400">Minimal 8 karakter.</p>
                    @error('password')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-stone-700">Ulangi password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full rounded-xl border border-stone-300 px-3.5 py-3 text-base outline-none focus:border-emerald-600">
                </div>
                <button type="submit" class="w-full rounded-xl bg-emerald-700 py-3 text-sm font-semibold text-white transition hover:bg-emerald-800">Daftar &amp; Masuk</button>
            </form>

            <p class="mt-5 text-center text-sm text-stone-500">Sudah punya akun? <a href="{{ route('shop.login') }}" class="font-semibold text-emerald-800 hover:underline">Masuk di sini</a></p>
        </div>
    </div>
@endsection
