<?php

namespace App\Http\Middleware;

use App\Support\RequestContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * SetRequestId Middleware
 *
 * Genera un ID único por request (X-Request-ID) y lo almacena en:
 *  - El header de respuesta para que el cliente pueda correlacionar errores.
 *  - El RequestContext singleton para que todos los servicios lo usen.
 *  - El contexto de logs de Laravel (withContext).
 *
 * Formato: REQ-{ULID}  — ordenable cronológicamente.
 */
class SetRequestId
{
    public function __construct(
        private readonly RequestContext $ctx,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Reutilizar si viene del cliente (útil para retry idempotentes)
        $requestId = $request->header(config('audit.request_id_header', 'X-Request-ID'))
                  ?: 'REQ-' . strtoupper((string) Str::ulid());

        $this->ctx->setRequestId($requestId);

        // Enriquecer los logs de Laravel con el request_id
        \Illuminate\Support\Facades\Log::withContext(['request_id' => $requestId]);

        /** @var Response $response */
        $response = $next($request);

        // Propagar al cliente en la respuesta
        $response->headers->set(
            config('audit.request_id_header', 'X-Request-ID'),
            $requestId
        );

        return $response;
    }
}
