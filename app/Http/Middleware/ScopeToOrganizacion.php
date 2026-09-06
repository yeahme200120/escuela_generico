<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ScopeToOrganizacion §18
 * Inyecta el organizacion_id del usuario en la request para que
 * todos los controladores y consultas siempre operen en el scope correcto.
 * También previene acceso cross-org por parámetros manipulados.
 */
class ScopeToOrganizacion
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->organizacion_id) {
            // Inyectar en el contexto del request para uso en controllers
            $request->merge(['_org_id' => $user->organizacion_id]);

            // Si la request trae organizacion_id diferente al del usuario, bloquearlo (excepto superadmin)
            if (!$user->esSuperadmin() && $request->has('organizacion_id')) {
                $requestedOrg = (int) $request->input('organizacion_id');
                if ($requestedOrg !== 0 && $requestedOrg !== (int) $user->organizacion_id) {
                    abort(403, 'Acceso cross-organización denegado.');
                }
            }
        }

        return $next($request);
    }
}
