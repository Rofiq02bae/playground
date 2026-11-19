<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - FunFarm</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-stone-100 px-4 text-stone-800 antialiased">
    <div class="w-full max-w-sm rounded-2xl border border-stone-200 bg-white p-7 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-700 text-lg font-semibold text-white">F</span>
            <div>
                <h1 class="font-semibold leading-tight text-stone-900">FunFarm</h1>
                <p class="text-sm text-stone-500">Masuk ke panel admin atau webapp pencatatan</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.login.submit') }}" class="mt-6 flex flex-col gap-4">
            @csrf
            <div>
                <label for="login" class="mb-1.5 block text-sm font-medium text-stone-700">Email atau Username</label>
                <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus autocomplete="username"
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                @error('login')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-stone-700">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
            </div>
            <label class="flex items-center gap-2 text-sm text-stone-600">
                <input type="checkbox" name="remember" class="rounded border-stone-300"> Ingat saya
            </label>
            <button type="submit" class="rounded-xl bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Masuk</button>
        </form>

        <p class="mt-5 text-center text-sm"><a href="{{ route('blog.index') }}" class="text-emerald-700 hover:underline">&larr; Kembali ke blog</a></p>
    </div>
</body>
</html>
