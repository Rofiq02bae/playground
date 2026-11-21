@extends('shop.layouts.app')

@section('title', 'Masuk - Produk FunFarm')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rounded-3xl border border-stone-200 bg-white p-6 sm:p-8">
            <h1 class="text-xl font-bold text-stone-900">Masuk ke FunFarm</h1>
            <p class="mt-1 text-sm text-stone-500">Satu akun untuk belanja di toko dan (bila kamu petani mitra) webapp pencatatan.</p>

            <form method="POST" action="{{ route('shop.login.submit') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="login" class="mb-1.5 block text-sm font-medium text-stone-700">Email atau username</label>
                    <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus autocomplete="username" class="w-full rounded-xl border border-stone-300 px-3.5 py-3 text-base outline-none focus:border-emerald-600">
                    @error('login')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-stone-700">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full rounded-xl border border-stone-300 px-3.5 py-3 text-base outline-none focus:border-emerald-600">
                    @error('password')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <label class="flex items-center gap-2 text-sm text-stone-600">
                    <input type="checkbox" name="remember" value="1" class="rounded border-stone-300"> Ingat saya
                </label>
                <button type="submit" class="w-full rounded-xl bg-emerald-700 py-3 text-sm font-semibold text-white transition hover:bg-emerald-800">Masuk</button>
            </form>

            <p class="mt-5 text-center text-sm text-stone-500">Belum punya akun pembeli? <a href="{{ route('shop.register') }}" class="font-semibold text-emerald-800 hover:underline">Daftar sekarang</a></p>
        </div>
    </div>
@endsection
