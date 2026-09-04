<?php

namespace App\Services\Auditoria;

use App\Models\AuditLog;
use App\Models\AccessLog;
use App\Support\GeoContext;
use App\Support\RequestContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * AuditService — Servicio central de trazabilidad.
 *
 * Responsabilidades:
 *  - Registrar eventos de auditoría (audit_logs) con geo, dispositivo y contexto completo.
 *  - Registrar intentos de acceso (access_logs) con detección de anomalías.
 *  - Calcular distancias entre accesos para detectar "viaje imposible".
 *  - Proveer helper de log manual para operaciones que no son CRUD de modelo.
 *
 * Nunca interrumpe la operación de negocio si hay un error interno.
 */
class AuditService
{
    public function __construct(
        private readonly RequestContext $ctx,
    ) {}

    // ================================================================
    // API PÚBLICA — Audit Logs
    // ================================================================

    /**
     * Registra el evento producido por el trait Auditable en un modelo.
     */
    public function logModel(
        string $action,
        Model  $model,
        array  $before  = [],
        array  $after   = [],
        array  $changes = [],
    ): ?AuditLog {
        return $this->log(
            modulo:    $this->inferModulo($model),
            accion:    $action,
            descripcion: $this->buildModelDescription($action, $model),
            model:     get_class($model),
            modelId:   $model->getKey(),
            modelDesc: method_exists($model, 'getAuditDescription') ? $model->getAuditDescription() : null,
            before:    $before,
            after:     $after,
            changes:   $changes,
        );
    }

    /**
     * Registra un evento manual (exportaciones, autorizaciones, operaciones críticas, etc.).
     *
     * @param string      $modulo     Módulo del sistema (alumnos, pagos, caja, etc.)
     * @param string      $accion     Acción ejecutada (export, approve, cancel, etc.)
     * @param string|null $descripcion Descripción legible del evento
     * @param string|null $model      FQCN del modelo afectado
     * @param int|null    $modelId    ID del registro afectado
     * @param string|null $modelDesc  Descripción del registro (matrícula, folio, etc.)
     * @param array       $before     Estado anterior
     * @param array       $after      Estado posterior
     * @param array       $changes    Diff de cambios
     * @param string|null $motivo     Motivo ingresado por el usuario
     * @param string      $resultado  success|failed|unauthorized|error
     * @param string|null $permiso    Permiso utilizado
     * @param array       $metadata   Datos extra específicos del módulo
     */
    public function log(
        string  $modulo,
        string  $accion,
        ?string $descripcion = null,
        ?string $model       = null,
        ?int    $modelId     = null,
        ?string $modelDesc   = null,
        array   $before      = [],
        array   $after       = [],
        array   $changes     = [],
        ?string $motivo      = null,
        string  $resultado   = 'success',
        ?string $permiso     = null,
        array   $metadata    = [],
    ): ?AuditLog {
        if (!config('audit.enabled', true)) return null;

        $geo = $this->ctx->getGeo();

        try {
            return AuditLog::create([
                'uuid'               => (string) Str::uuid(),
                'request_id'         => $this->ctx->getRequestId(),
                'session_uuid'       => $this->ctx->getSessionUuid(),
                'user_id'            => $this->ctx->getUserId(),
                'user_nombre'        => $this->ctx->getUserNombre(),
                'user_email'         => $this->ctx->getUserEmail(),
                'user_rol'           => $this->ctx->getUserRol(),
                'organizacion_id'    => $this->ctx->getOrganizacionId(),
                'sede_id'            => $this->ctx->getSedeId(),
                'sede_nombre'        => $this->ctx->getSedeNombre(),
                'modulo'             => $modulo,
                'accion'             => $accion,
                'evento'             => "{$modulo}.{$accion}",
                'descripcion'        => $descripcion,
                'model'              => $model,
                'model_id'           => $modelId,
                'model_descripcion'  => $modelDesc,
                'before_data'        => empty($before)  ? null : $before,
                'after_data'         => empty($after)   ? null : $after,
                'changes'            => empty($changes)  ? null : $changes,
                'motivo'             => $motivo,
                'permission_usado'   => $permiso,
                'ip_address'         => $geo?->ipAddress,
                'device_id'          => $geo?->deviceId,
                'device_type'        => $geo?->deviceType,
                'sistema_operativo'  => $geo?->sistemaOperativo,
                'navegador'          => $geo?->navegador,
                'user_agent'         => $geo?->userAgent,
                'latitud'            => $geo?->latitud,
                'longitud'           => $geo?->longitud,
                'precision_metros'   => $geo?->precisionMetros,
                'altitud'            => $geo?->altitud,
                'velocidad'          => $geo?->velocidad,
                'fuente_ubicacion'   => $geo?->fuente,
                'resultado'          => $resultado,
                'duracion_ms'        => $this->ctx->getDuracionMs(),
                'metadata'           => empty($metadata) ? null : $metadata,
                'created_at'         => now(),
            ]);
        } catch (\Throwable $e) {
            logger()->error('AuditService::log error', ['exception' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Shortcut para operaciones no autorizadas.
     */
    public function logUnauthorized(string $modulo, string $accion, string $motivo = ''): void
    {
        $this->log(
            modulo:    $modulo,
            accion:    $accion,
            resultado: 'unauthorized',
            motivo:    $motivo ?: 'Acceso denegado',
        );
    }

    /**
     * Shortcut para registrar errores del sistema.
     */
    public function logError(string $modulo, string $accion, \Throwable $e, array $metadata = []): void
    {
        $this->log(
            modulo:    $modulo,
            accion:    $accion,
            resultado: 'error',
            motivo:    $e->getMessage(),
            metadata:  array_merge($metadata, [
                'exception_class' => get_class($e),
                'file'            => $e->getFile(),
                'line'            => $e->getLine(),
            ]),
        );
    }

    // ================================================================
    // API PÚBLICA — Access Logs
    // ================================================================

    /**
     * Registra un intento de login (exitoso o fallido) con toda la geo y detección de anomalías.
     */
    public function logAccess(
        string  $evento,
        string  $resultado,
        ?int    $userId       = null,
        ?string $emailIntento = null,
        ?int    $organizacionId = null,
        ?int    $sedeId       = null,
        ?string $sessionUuid  = null,
        ?string $motivoRechazo = null,
    ): AccessLog {
        $geo = $this->ctx->getGeo();

        // Detección de anomalías
        $anomalias = $this->detectarAnomalias($userId, $geo, $sedeId);

        try {
            return AccessLog::create([
                'uuid'                          => (string) Str::uuid(),
                'user_id'                       => $userId,
                'email_intento'                 => $emailIntento,
                'organizacion_id'               => $organizacionId,
                'sede_id'                       => $sedeId,
                'session_uuid'                  => $sessionUuid,
                'evento'                        => $evento,
                'resultado'                     => $resultado,
                'motivo_rechazo'                => $motivoRechazo,
                'ip_address'                    => $geo?->ipAddress,
                'device_id'                     => $geo?->deviceId,
                'device_type'                   => $geo?->deviceType,
                'sistema_operativo'             => $geo?->sistemaOperativo,
                'navegador'                     => $geo?->navegador,
                'user_agent'                    => $geo?->userAgent,
                'latitud'                       => $geo?->latitud,
                'longitud'                      => $geo?->longitud,
                'precision_metros'              => $geo?->precisionMetros,
                'fuente_ubicacion'              => $geo?->fuente,
                'es_nuevo_dispositivo'          => $anomalias['nuevo_dispositivo'],
                'es_nueva_ubicacion'            => $anomalias['nueva_ubicacion'],
                'fuera_de_geocerca'             => $anomalias['fuera_geocerca'],
                'fuera_de_horario'              => $anomalias['fuera_horario'],
                'viaje_imposible'               => $anomalias['viaje_imposible'],
                'distancia_ultimo_acceso_km'    => $anomalias['distancia_km'],
                'minutos_desde_ultimo_acceso'   => $anomalias['minutos'],
                'request_id'                    => $this->ctx->getRequestId(),
                'created_at'                    => now(),
            ]);
        } catch (\Throwable $e) {
            logger()->error('AuditService::logAccess error', ['exception' => $e->getMessage()]);
            // Retornamos un objeto no persistido para no romper el flujo
            return new AccessLog();
        }
    }

    // ================================================================
    // Detección de anomalías
    // ================================================================

    private function detectarAnomalias(?int $userId, ?GeoContext $geo, ?int $sedeId): array
    {
        $result = [
            'nuevo_dispositivo' => false,
            'nueva_ubicacion'   => false,
            'fuera_geocerca'    => false,
            'fuera_horario'     => false,
            'viaje_imposible'   => false,
            'distancia_km'      => null,
            'minutos'           => null,
        ];

        if (!$userId) return $result;

        try {
            // ── Nuevo dispositivo ──────────────────────────────────
            if ($geo?->deviceId) {
                $result['nuevo_dispositivo'] = !AccessLog::where('user_id', $userId)
                    ->where('device_id', $geo->deviceId)
                    ->where('resultado', 'success')
                    ->exists();
            }

            // ── Último acceso exitoso ──────────────────────────────
            $ultimo = AccessLog::where('user_id', $userId)
                ->where('resultado', 'success')
                ->whereNotNull('latitud')
                ->orderByDesc('created_at')
                ->first();

            if ($ultimo && $geo?->tieneUbicacion()) {
                // Distancia
                $distKm = $this->haversineKm(
                    $ultimo->latitud, $ultimo->longitud,
                    $geo->latitud,    $geo->longitud
                );
                $minutos = (int) round(now()->diffInMinutes($ultimo->created_at));
                $result['distancia_km'] = round($distKm, 3);
                $result['minutos']      = $minutos;

                // Viaje imposible: >800 km en < 60 min (aprox. avión comercial máx)
                if ($minutos > 0 && ($distKm / ($minutos / 60)) > 900) {
                    $result['viaje_imposible']  = true;
                    $result['nueva_ubicacion']  = true;
                } elseif ($distKm > 50) {
                    $result['nueva_ubicacion'] = true;
                }

                // ── Fuera de geocerca de la sede ───────────────────
                if ($sedeId) {
                    $sede = \App\Models\Sede::find($sedeId);
                    if ($sede && !$sede->estaDentroDeGeocerca($geo->latitud, $geo->longitud)) {
                        $result['fuera_geocerca'] = true;
                    }
                }
            }

            // ── Fuera de horario (8am-10pm por defecto) ───────────
            $hora = now()->hour;
            $result['fuera_horario'] = $hora < 6 || $hora >= 23;

        } catch (\Throwable $e) {
            logger()->warning('AuditService anomaly detection failed: ' . $e->getMessage());
        }

        return $result;
    }

    // ================================================================
    // Helpers
    // ================================================================

    private function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $r    = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a    = sin($dLat / 2) ** 2
              + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $r * 2 * asin(sqrt($a));
    }

    private function inferModulo(Model $model): string
    {
        $class = class_basename($model);
        return match ($class) {
            'User'          => 'usuarios',
            'Role'          => 'roles',
            'Permission'    => 'permisos',
            'Organizacion'  => 'organizaciones',
            'Escuela'       => 'escuelas',
            'Sede'          => 'sedes',
            'Aula'          => 'aulas',
            'Edificio'      => 'edificios',
            'CicloEscolar'  => 'ciclos',
            'NivelEducativo'=> 'niveles',
            'Grado'         => 'grados',
            'Grupo'         => 'grupos',
            'SystemSetting' => 'configuracion',
            default         => strtolower($class),
        };
    }

    private function buildModelDescription(string $action, Model $model): string
    {
        $modelName = class_basename($model);
        $id        = $model->getKey();
        $labels    = [
            'create'       => 'Crear',
            'update'       => 'Actualizar',
            'delete'       => 'Eliminar',
            'force_delete' => 'Eliminar definitivamente',
            'restore'      => 'Restaurar',
        ];
        $label = $labels[$action] ?? $action;
        return "{$label} {$modelName} #{$id}";
    }
}
