<?php

namespace App\Providers;

use App\Models\AuditLog;
use App\Models\Sede;
use App\Models\SystemSetting;
use App\Models\User;
use App\Policies\AuditoriaPolicy;
use App\Policies\SedePolicy;
use App\Policies\SystemSettingPolicy;
use App\Policies\UserPolicy;
use App\Services\Configuracion\ThemeService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ThemeService requiere AuditService (scoped), así que también va scoped
        $this->app->scoped(\App\Services\Configuracion\ThemeService::class, function ($app) {
            return new \App\Services\Configuracion\ThemeService(
                audit: $app->make(\App\Services\Auditoria\AuditService::class),
            );
        });

        // Servicios de finanzas
        $this->app->scoped(\App\Services\Finanzas\PagoService::class, function ($app) {
            return new \App\Services\Finanzas\PagoService(
                audit: $app->make(\App\Services\Auditoria\AuditService::class),
            );
        });

        $this->app->scoped(\App\Services\Finanzas\CajaService::class, function ($app) {
            return new \App\Services\Finanzas\CajaService(
                audit: $app->make(\App\Services\Auditoria\AuditService::class),
            );
        });

        $this->app->scoped(\App\Services\Documentos\DocumentoService::class, function ($app) {
            return new \App\Services\Documentos\DocumentoService(
                audit: $app->make(\App\Services\Auditoria\AuditService::class),
            );
        });
    }

    public function boot(): void
    {
        // ── Paginación con Bootstrap 5 ─────────────────────────────
        Paginator::useBootstrapFive();

        // ── Rate limiter para login ────────────────────────────────
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinutes(
                config('audit.lockout_minutes', 15),
                config('audit.max_login_attempts', 5)
            )->by($request->input('email') . '|' . $request->ip());
        });

        // ── Registrar Policies ─────────────────────────────────────
        Gate::policy(User::class,          UserPolicy::class);
        Gate::policy(AuditLog::class,      AuditoriaPolicy::class);
        Gate::policy(SystemSetting::class, SystemSettingPolicy::class);
        Gate::policy(Sede::class,          SedePolicy::class);

        // ── Gate dinámico para permisos por slug ───────────────────
        // Permite usar $this->authorize('alumnos.ver') en controllers
        // y @can('alumnos.ver') en vistas sin definir un Gate por cada permiso.
        Gate::before(function (User $user, string $ability) {
            // Superadmin siempre pasa
            if ($user->esSuperadmin()) return true;

            // Inactivo/bloqueado siempre deniega
            if (!$user->activo || $user->estaBloqueado()) return false;

            return null; // seguir evaluación normal
        });

        Gate::define('*', function (User $user, string $ability, ...$args) {
            // Si el ability parece un slug de permiso (contiene punto)
            // lo resolvemos contra puedeHacer()
            if (str_contains($ability, '.')) {
                $sedeId = $args[0] ?? null;
                return $user->puedeHacer($ability, $sedeId instanceof Sede ? $sedeId->id : $sedeId);
            }
            return false;
        });
    }
}
