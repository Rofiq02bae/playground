<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Blog - FunFarm')</title>
    <meta name="description" content="@yield('meta_description', 'Artikel pertanian, peternakan, dan teknologi dari FunFarm.')">
    @hasSection('meta_keywords')
        <meta name="keywords" content="@yield('meta_keywords')">
    @endif
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', 'Blog - FunFarm')))">
    <meta property="og:description" content="@yield('og_description', trim($__env->yieldContent('meta_description', 'Artikel pertanian, peternakan, dan teknologi dari FunFarm.')))">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:image" content="@yield('og_image', asset('favicon.ico'))">
    <meta property="og:site_name" content="FunFarm">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', trim($__env->yieldContent('title', 'Blog - FunFarm')))">
    <meta name="twitter:description" content="@yield('og_description', trim($__env->yieldContent('meta_description', 'Artikel pertanian, peternakan, dan teknologi dari FunFarm.')))">
    <meta name="twitter:image" content="@yield('og_image', asset('favicon.ico'))">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-stone-50 text-stone-800 antialiased">
    <div class="flex min-h-screen flex-col overflow-x-hidden">
        @include('blog.partials.navbar')

        <main id="main-content" class="flex-1">
            @yield('content')
        </main>

        @include('blog.partials.footer')
    </div>
</body>
</html>
