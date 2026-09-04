<?php

namespace App\Http\Middleware;

use App\Services\Auditoria\QueryLogger;
use App\Support\GeoContext;
use App\Support\RequestContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * GeoTrace Middleware
 *
 * Responsabilidades:
 *  1. Construye el GeoContext desde los headers del request.
 *  2. Lo inyecta en el RequestContext (singleton).
 *  3. Inicia el QueryLogger para capturar todas las queries del request.
 *  4. Al terminar el request, vacía el buffer de queries a la DB.
 *  5. Si el usuario está autenticado, puebla el RequestContext con sus datos.
 *
 * Headers esperados del frontend (enviados por Alpine.js/GeoCapture):
 *   X-Geo-Latitude     — latitud del GPS/network del dispositivo
 *   X-Geo-Longitude    — longitud
 *   X-Geo-Accuracy     — precisión en metros
 *   X-Geo-Altitude     — altitud (opcional)
 *   X-Geo-Speed        — velocidad (opcional)
 *   X-Geo-Source       — fuente: gps | network | ip | denied | unavailable
 *   X-Device-ID        — fingerprint único del dispositivo (calculado en JS)
 *   X-Device-Info      — JSON con datos del dispositivo (pantalla, SO, etc.)
 *   X-Session-ID       — UUID de la UserSession activa
 */
class GeoTrace
{
    public function __construct(
        private readonly RequestContext $ctx,
        private readonly QueryLogger    $queryLogger,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // 1. Construir y almacenar el GeoContext
        $geo = GeoContext::fromRequest($request);
        $this->ctx->setGeo($geo);

        // 2. Sesión de usuario desde header (o desde la sesión de Laravel)
        $sessionUuid = $request->header('X-Session-ID')
                    ?: $request->session()->get('user_session_uuid');
        $this->ctx->setSessionUuid($sessionUuid);

        // 3. Si hay usuario autenticado → poblar contexto
        if ($user = $request->user()) {
            $sedeId = $request->session()->get('sede_activa_id');
            $this->ctx->populateFromUser($user, $sedeId);
        }

        // 4. Iniciar captura de queries
        $this->queryLogger->startListening();

        // 5. Procesar request
        $response = $next($request);

        // 6. Vaciar buffer de queries al final del ciclo
        $this->queryLogger->flush();

        // 7. Actualizar last_seen_at de la UserSession activa
        if ($sessionUuid && $request->user()) {
            try {
                \App\Models\UserSession::where('uuid', $sessionUuid)
                    ->where('active', true)
                    ->update(['last_seen_at' => now()]);
            } catch (\Throwable) {
                // No interrumpir por esto
            }
        }

        return $response;
    }
}
