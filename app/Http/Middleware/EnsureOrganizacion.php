<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureOrganizacion
 * Verifica que el usuario autenticado tenga una organización asignada.
 * Sin organización no puede operar el sistema.
 */
class EnsureOrganizacion
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->esSuperadmin() && !$user->organizacion_id) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Tu cuenta no está asignada a ninguna organización. Contacta al administrador.');
        }

        return $next($request);
    }
}
