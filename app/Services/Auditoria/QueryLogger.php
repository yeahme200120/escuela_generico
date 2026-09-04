<?php

namespace App\Services\Auditoria;

use App\Models\QueryLog;
use App\Support\GeoContext;
use App\Support\RequestContext;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * QueryLogger — Escucha el evento QueryExecuted de Laravel y persiste
 * cada query en query_logs, vinculada al request_id y session actuales.
 *
 * Características:
 *  - Reemplaza bindings en el SQL para tener la query completa legible.
 *  - Detecta queries lentas (umbral configurable).
 *  - Infiere la tabla principal y el tipo de operación (SELECT/INSERT/etc.).
 *  - Detecta si se está dentro de una transacción.
 *  - Captura el origen (clase::método) del caller.
 *  - No falla silenciosamente la operación si hay error de log.
 */
class QueryLogger
{
    private bool $enabled     = false;
    private bool $listening   = false;
    private int  $slowThreshold = 500; // ms

    /** Buffer en memoria para escritura en lote al final del request */
    private array $buffer = [];
    private int   $bufferLimit = 200;

    public function __construct(
        private readonly RequestContext $ctx,
    ) {
        $this->enabled      = config('audit.store_queries', true) && config('audit.enabled', true);
        $this->slowThreshold = (int) config('audit.slow_query_ms', 500);
    }

    /**
     * Inicia la escucha de queries para este request.
     */
    public function startListening(): void
    {
        if (!$this->enabled || $this->listening) return;
        $this->listening = true;

        DB::listen(function (QueryExecuted $event) {
            $this->handle($event);
        });
    }

    /**
     * Vacía el buffer al final del request.
     */
    public function flush(): void
    {
        if (empty($this->buffer)) return;

        try {
            // Inserción en lote para mínimo impacto
            QueryLog::insert($this->buffer);
        } catch (\Throwable $e) {
            logger()->error('QueryLogger flush error: ' . $e->getMessage());
        }

        $this->buffer = [];
    }

    // ----------------------------------------------------------------
    // Interno
    // ----------------------------------------------------------------

    private function handle(QueryExecuted $event): void
    {
        // Evitar loguear las propias queries de inserción de logs (loop)
        if (str_contains($event->sql, 'query_logs') || str_contains($event->sql, 'audit_logs')) {
            return;
        }

        if (count($this->buffer) >= $this->bufferLimit) return;

        $sql     = $this->buildSqlWithBindings($event->sql, $event->bindings);
        $tipo    = $this->detectTipo($event->sql);
        $tabla   = $this->detectTabla($event->sql);
        $origen  = $this->detectOrigen();
        $esLenta = $event->time >= $this->slowThreshold;

        $geo = $this->ctx->getGeo();

        $this->buffer[] = [
            'request_id'      => $this->ctx->getRequestId(),
            'audit_log_uuid'  => null, // se actualiza si hay audit_log asociado
            'session_uuid'    => $this->ctx->getSessionUuid(),
            'user_id'         => $this->ctx->getUserId(),
            'organizacion_id' => $this->ctx->getOrganizacionId(),
            'sede_id'         => $this->ctx->getSedeId(),
            'connection'      => $event->connectionName,
            'sql'             => mb_substr($sql, 0, 65000),
            'sql_raw'         => mb_substr($event->sql, 0, 65000),
            'bindings'        => json_encode($event->bindings),
            'tiempo_ms'       => round($event->time, 3),
            'filas_afectadas' => null, // no disponible universalmente
            'tipo'            => $tipo,
            'tabla_principal' => $tabla,
            'origen'          => $origen,
            'en_transaccion'  => DB::transactionLevel() > 0 ? 1 : 0,
            'es_lenta'        => $esLenta ? 1 : 0,
            'ip_address'      => $geo?->ipAddress,
            'latitud'         => $geo?->latitud,
            'longitud'        => $geo?->longitud,
            'precision_metros'=> $geo?->precisionMetros,
            'created_at'      => now()->toDateTimeString(),
        ];

        if ($esLenta) {
            logger()->warning("Slow query ({$event->time}ms): {$tabla} — {$tipo}", [
                'sql'        => mb_substr($sql, 0, 500),
                'request_id' => $this->ctx->getRequestId(),
            ]);
        }
    }

    /**
     * Reemplaza los placeholders ? por los valores reales (para legibilidad).
     */
    private function buildSqlWithBindings(string $sql, array $bindings): string
    {
        $pdo = DB::getPdo();
        foreach ($bindings as $binding) {
            $value = match (true) {
                is_null($binding)   => 'NULL',
                is_bool($binding)   => $binding ? '1' : '0',
                is_int($binding),
                is_float($binding)  => (string) $binding,
                $binding instanceof \DateTimeInterface => $pdo->quote($binding->format('Y-m-d H:i:s')),
                default             => $pdo->quote((string) $binding),
            };
            $sql = Str::replaceFirst('?', $value, $sql);
        }
        return $sql;
    }

    private function detectTipo(string $sql): string
    {
        $keyword = strtoupper(trim(explode(' ', ltrim($sql))[0]));
        return match ($keyword) {
            'SELECT'   => 'SELECT',
            'INSERT'   => 'INSERT',
            'UPDATE'   => 'UPDATE',
            'DELETE'   => 'DELETE',
            'BEGIN', 'START' => 'BEGIN',
            'COMMIT'   => 'COMMIT',
            'ROLLBACK' => 'ROLLBACK',
            'CREATE'   => 'CREATE',
            'ALTER'    => 'ALTER',
            'DROP'     => 'DROP',
            default    => 'OTHER',
        };
    }

    private function detectTabla(string $sql): ?string
    {
        // FROM tabla / INTO tabla / UPDATE tabla
        if (preg_match('/(?:FROM|INTO|UPDATE|JOIN)\s+[`"]?(\w+)[`"]?/i', $sql, $m)) {
            return $m[1];
        }
        return null;
    }

    /**
     * Detecta la clase y método del caller descartando el stack del framework.
     */
    private function detectOrigen(): ?string
    {
        $skip = [
            'Illuminate\\',
            'App\\Services\\Auditoria\\',
            'App\\Models\\Concerns\\',
            'Database\\',
            'PDO',
        ];

        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20) as $frame) {
            $class = $frame['class'] ?? '';
            $skip_frame = false;
            foreach ($skip as $prefix) {
                if (str_starts_with($class, $prefix)) {
                    $skip_frame = true;
                    break;
                }
            }
            if (!$skip_frame && !empty($class)) {
                return "{$class}::{$frame['function']}";
            }
        }
        return null;
    }
}
