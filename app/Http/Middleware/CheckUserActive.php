<?php

namespace App\Http\Middleware;

use App\Services\Auditoria\AuditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckUserActive
 *
 * Verifica tras autenticar que el usuario:
 *  1. Esté activo (activo = true).
 *  2. No esté temporalmente bloqueado (bloqueado_hasta > now).
 *
 * Si falla, cierra la sesión, registra el evento en audit_logs y
 * redirige al login con mensaje.
 */
class CheckUserActive
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if (!$user->activo) {
            $this->audit->log(
                modulo:      'seguridad',
                accion:      'acceso_denegado',
                descripcion: 'Usuario inactivo intentó acceder al sistema',
                model:       \App\Models\User::class,
                modelId:     $user->id,
                resultado:   'unauthorized',
                motivo:      'Cuenta inactiva',
            );

            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Tu cuenta está desactivada. Contacta al administrador.']);
        }

        if ($user->estaBloqueado()) {
            $this->audit->log(
                modulo:      'seguridad',
                accion:      'acceso_denegado',
                descripcion: 'Usuario bloqueado intentó acceder',
                modelId:     $user->id,
                resultado:   'unauthorized',
                motivo:      'Cuenta bloqueada hasta ' . $user->bloqueado_hasta->format('d/m/Y H:i'),
            );

            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Tu cuenta está bloqueada temporalmente. Inténtalo más tarde.']);
        }

        return $next($request);
    }
}
