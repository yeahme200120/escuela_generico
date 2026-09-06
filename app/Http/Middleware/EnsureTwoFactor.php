<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureTwoFactor — §63
 * Si el usuario tiene 2FA activado y no lo ha verificado en esta sesión,
 * redirige a la pantalla de desafío.
 */
class EnsureTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) return $next($request);

        // Si tiene 2FA activo y aún no verificó en esta sesión
        if ($user->two_factor_enabled && !session('2fa_verified')) {
            // Guardar el usuario pendiente y logout temporal
            session(['2fa_user_id' => $user->id]);
            auth()->logout();
            return redirect()->route('two-factor.challenge');
        }

        return $next($request);
    }
}
