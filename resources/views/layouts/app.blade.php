<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Blog - FunFarm Platform')</title>
    <meta name="description" content="@yield('meta_description', 'Artikel pertanian, peternakan, dan teknologi dari FunFarm Platform.')">
    <style>
        :root { --green:#177245; --green-dark:#0d4d2b; --ink:#1c2521; --muted:#5d6d64; --line:#dde7e0; --bg:#f6f9f7; }
        * { box-sizing:border-box; }
        body { margin:0; font-family:system-ui,-apple-system,'Segoe UI',Roboto,sans-serif; color:var(--ink); background:var(--bg); line-height:1.6; }
        a { color:var(--green); text-decoration:none; }
        a:hover { text-decoration:underline; }
        .site-header { background:var(--green-dark); color:#fff; }
        .site-header .wrap { display:flex; align-items:center; justify-content:space-between; padding:.9rem 0; }
        .brand { color:#fff; font-weight:800; font-size:1.25rem; letter-spacing:.2px; }
        .brand:hover { text-decoration:none; }
        .brand small { display:block; font-weight:500; font-size:.72rem; opacity:.85; letter-spacing:.4px; text-transform:uppercase; }
        .wrap { max-width:1120px; margin:0 auto; padding:0 1.25rem; }
        main.wrap { padding-top:2rem; padding-bottom:3rem; }
        .hero { margin-bottom:1.75rem; }
        .hero h1 { margin:.2rem 0 .4rem; font-size:2rem; line-height:1.2; }
        .hero p { margin:0; color:var(--muted); max-width:640px; }
        .toolbar { display:flex; flex-wrap:wrap; gap:.75rem; align-items:center; justify-content:space-between; margin-bottom:1.5rem; }
        .search { display:flex; gap:.5rem; flex:1 1 280px; max-width:460px; }
        .search input { flex:1; padding:.55rem .8rem; border:1px solid var(--line); border-radius:.6rem; font-size:.95rem; background:#fff; }
        .search button { padding:.55rem 1rem; border:0; border-radius:.6rem; background:var(--green); color:#fff; font-weight:600; cursor:pointer; }
        .pills { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1.5rem; }
        .pill { padding:.35rem .85rem; border:1px solid var(--line); border-radius:999px; background:#fff; color:var(--ink); font-size:.88rem; }
        .pill:hover { text-decoration:none; border-color:var(--green); }
        .pill.active { background:var(--green); border-color:var(--green); color:#fff; }
        .pill .count { opacity:.7; font-size:.8em; }
        /* Grid dibuat dengan breakpoint tetap (bukan auto-fill) dan hanya
           dirender bila ada artikel, jadi tidak pernah ada sel/card kosong. */
        .article-grid { display:grid; gap:1.5rem; grid-template-columns:1fr; }
        @media (min-width:640px) { .article-grid { grid-template-columns:repeat(2,1fr); } }
        @media (min-width:1024px) { .article-grid { grid-template-columns:repeat(3,1fr); } }
        .card { background:#fff; border:1px solid var(--line); border-radius:.9rem; overflow:hidden; display:flex; flex-direction:column; height:100%; }
        .card:hover { border-color:var(--green); }
        .card-thumb { aspect-ratio:16/9; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; letter-spacing:.3px; text-align:center; padding:1rem; }
        .card-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
        .thumb-pertanian { background:linear-gradient(135deg,#2e9e5b,#177245); }
        .thumb-peternakan { background:linear-gradient(135deg,#d97706,#b45309); }
        .thumb-teknologi-pertanian { background:linear-gradient(135deg,#0284c7,#0369a1); }
        .thumb-informasi-umum { background:linear-gradient(135deg,#7c3aed,#5b21b6); }
        .thumb-default { background:linear-gradient(135deg,#475569,#1e293b); }
        .card-body { padding:1.1rem 1.25rem 1.35rem; display:flex; flex-direction:column; gap:.55rem; flex:1; }
        .card-meta { display:flex; gap:.6rem; align-items:center; font-size:.8rem; color:var(--muted); }
        .badge { background:#e7f4ec; color:var(--green-dark); padding:.12rem .6rem; border-radius:999px; font-weight:600; font-size:.75rem; }
        .card h3 { margin:0; font-size:1.12rem; line-height:1.35; }
        .card h3 a { color:var(--ink); }
        .card p { margin:0; color:var(--muted); font-size:.93rem; flex:1; }
        .read-more { font-weight:600; font-size:.9rem; }
        .empty-state { background:#fff; border:1px dashed var(--line); border-radius:.9rem; padding:3rem 1.5rem; text-align:center; color:var(--muted); }
        .empty-state strong { display:block; color:var(--ink); font-size:1.1rem; margin-bottom:.3rem; }
        .article-full { background:#fff; border:1px solid var(--line); border-radius:.9rem; padding:2rem; }
        .article-full h1 { margin:.4rem 0 .6rem; line-height:1.25; font-size:2rem; }
        .article-full .content p { margin:0 0 1.1rem; }
        .section-title { margin:2.5rem 0 1.25rem; font-size:1.35rem; }
        .site-footer { border-top:1px solid var(--line); padding:1.5rem 0 2.5rem; color:var(--muted); font-size:.88rem; }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="wrap">
            <a class="brand" href="{{ route('articles.index') }}">FunFarm<small>Platform Pertanian Terintegrasi</small></a>
            <nav><a href="{{ route('articles.index') }}" style="color:#fff;font-weight:600;">Blog</a></nav>
        </div>
    </header>

    <main class="wrap">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="wrap">&copy; {{ date('Y') }} FunFarm Platform - Blog pertanian, peternakan, dan teknologi.</div>
    </footer>
</body>
</html>
