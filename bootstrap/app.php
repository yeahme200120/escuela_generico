<?php

use App\Http\Middleware\CheckUserActive;
use App\Http\Middleware\GeoTrace;
use App\Http\Middleware\SetRequestId;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        App\Providers\AuditServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        // ── Middlewares globales para todas las rutas web ──────────────
        $middleware->web(append: [
            SetRequestId::class,   // Genera X-Request-ID — debe ir primero
            GeoTrace::class,       // Captura geo, dispositivo e inicia QueryLogger
            CheckUserActive::class, // Verifica activo/bloqueado tras auth
        ]);

        // ── Middlewares globales para rutas API ────────────────────────
        $middleware->api(append: [
            SetRequestId::class,
            GeoTrace::class,
            CheckUserActive::class,
        ]);

        // ── Aliases de middleware ──────────────────────────────────────
        $middleware->alias([
            'verified'     => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'check.active' => CheckUserActive::class,
            'geo'          => GeoTrace::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Registrar excepciones no manejadas en el audit log
        $exceptions->report(function (\Throwable $e) {
            if (app()->bound(\App\Services\Auditoria\AuditService::class)) {
                try {
                    app(\App\Services\Auditoria\AuditService::class)->logError(
                        modulo: 'sistema',
                        accion: 'unhandled_exception',
                        e:      $e,
                    );
                } catch (\Throwable) {
                    // Never interrupt exception handling
                }
            }
        });
    })->create();
