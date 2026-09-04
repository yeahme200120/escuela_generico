<?php

namespace App\Providers;

use App\Services\Auth\AuthService;
use App\Services\Auditoria\AuditService;
use App\Services\Auditoria\QueryLogger;
use App\Support\GeoContext;
use App\Support\RequestContext;
use Illuminate\Support\ServiceProvider;

/**
 * AuditServiceProvider
 *
 * Registra los servicios de trazabilidad como singletons scoped al request
 * para que toda la aplicación comparta la misma instancia durante un ciclo.
 */
class AuditServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // RequestContext — un singleton por request (compartido entre todos los servicios)
        $this->app->scoped(RequestContext::class, fn() => new RequestContext());

        // QueryLogger — singleton scoped, depende de RequestContext
        $this->app->scoped(QueryLogger::class, function ($app) {
            return new QueryLogger(
                ctx: $app->make(RequestContext::class),
            );
        });

        // AuditService — singleton scoped, depende de RequestContext
        $this->app->scoped(AuditService::class, function ($app) {
            return new AuditService(
                ctx: $app->make(RequestContext::class),
            );
        });

        // AuthService — singleton scoped, depende de AuditService y RequestContext
        $this->app->scoped(AuthService::class, function ($app) {
            return new AuthService(
                audit: $app->make(AuditService::class),
                ctx:   $app->make(RequestContext::class),
            );
        });
    }

    public function boot(): void
    {
        // noop — la activación real ocurre en los middlewares
    }
}
