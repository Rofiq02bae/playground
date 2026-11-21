<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Tamu dari area toko diarahkan ke pintu masuk toko, sisanya ke pintu tunggal.
        $middleware->redirectGuestsTo(fn (Request $request) => ($request->is('produk') || $request->is('produk/*')) ? '/produk/masuk' : '/admin/login');
        // Webhook Midtrans tidak membawa token CSRF (keasliannya dari verifikasi signature).
        $middleware->validateCsrfTokens(except: ['midtrans/notification']);
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
