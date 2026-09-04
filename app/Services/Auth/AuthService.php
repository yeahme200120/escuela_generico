<?php

namespace App\Services\Auth;

use App\Models\AccessLog;
use App\Models\User;
use App\Models\UserSession;
use App\Services\Auditoria\AuditService;
use App\Support\GeoContext;
use App\Support\RequestContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * AuthService — Servicio central de autenticación.
 *
 * Responsabilidades:
 *  - Autenticar credenciales con validación de estado y bloqueo temporal.
 *  - Crear registros UserSession con geo, dispositivo y fingerprint.
 *  - Registrar access_logs con detección de anomalías.
 *  - Gestionar cierre de sesión con trazabilidad.
 *  - Controlar intentos fallidos y bloqueo temporal.
 *  - Proveer revocación de sesiones (logout de todos los dispositivos).
 */
class AuthService
{
    // Clave de rate limiter
    private const RL_PREFIX = 'login_attempt:';

    public function __construct(
        private readonly AuditService  $audit,
        private readonly RequestContext $ctx,
    ) {}

    // ================================================================
    // Login
    // ================================================================

    /**
     * Intenta autenticar al usuario.
     *
     * @param string $email
     * @param string $password
     * @param bool   $remember
     * @param array  $geoData   Datos de geolocalización y dispositivo del frontend
     * @return LoginResult
     */
    public function login(
        string $email,
        string $password,
        bool   $remember  = false,
        array  $geoData   = [],
    ): LoginResult {
        $rateLimitKey = self::RL_PREFIX . Str::lower($email) . '|' . request()->ip();

        // ── 1. Rate limiting ────────────────────────────────────────
        if (RateLimiter::tooManyAttempts($rateLimitKey, config('audit.max_login_attempts', 5))) {
            $seconds = RateLimiter::availableIn($rateLimitKey);

            $this->audit->logAccess(
                evento:        'login_failed',
                resultado:     'blocked',
                emailIntento:  $email,
                motivoRechazo: "Demasiados intentos. Espere {$seconds}s.",
            );

            return LoginResult::blocked("Demasiados intentos de acceso. Espere {$seconds} segundos.");
        }

        // ── 2. Buscar usuario ───────────────────────────────────────
        $user = User::where('email', $email)
                    ->orWhere('username', $email)
                    ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            RateLimiter::hit($rateLimitKey, 60 * config('audit.lockout_minutes', 15));

            $this->audit->logAccess(
                evento:        'login_failed',
                resultado:     'failed',
                emailIntento:  $email,
                motivoRechazo: 'Credenciales inválidas',
            );

            // Incrementar intentos fallidos en el usuario (si existe)
            if ($user) {
                $intentos = $user->intentos_fallidos + 1;
                $max      = config('audit.max_login_attempts', 5);
                $data     = ['intentos_fallidos' => $intentos];

                if ($intentos >= $max) {
                    $data['bloqueado_hasta'] = now()->addMinutes(config('audit.lockout_minutes', 15));
                }

                $user->withoutAudit(fn($u) => $u->update($data));
            }

            return LoginResult::failed('Credenciales incorrectas.');
        }

        // ── 3. Verificar estado ─────────────────────────────────────
        if (!$user->activo) {
            $this->audit->logAccess(
                evento:        'login_failed',
                resultado:     'failed',
                userId:        $user->id,
                emailIntento:  $email,
                organizacionId: $user->organizacion_id,
                motivoRechazo: 'Cuenta inactiva',
            );
            return LoginResult::failed('Tu cuenta está desactivada.');
        }

        if ($user->estaBloqueado()) {
            $this->audit->logAccess(
                evento:        'login_failed',
                resultado:     'blocked',
                userId:        $user->id,
                emailIntento:  $email,
                organizacionId: $user->organizacion_id,
                motivoRechazo: 'Cuenta bloqueada temporalmente',
            );
            return LoginResult::blocked('Tu cuenta está bloqueada. Inténtalo más tarde.');
        }

        // ── 4. Autenticar ───────────────────────────────────────────
        RateLimiter::clear($rateLimitKey);
        Auth::login($user, $remember);

        // ── 5. Actualizar último acceso ─────────────────────────────
        $user->withoutAudit(fn($u) => $u->update([
            'ultimo_acceso_at' => now(),
            'ultimo_ip'        => request()->ip(),
            'intentos_fallidos' => 0,
            'bloqueado_hasta'  => null,
        ]));

        // ── 6. Crear UserSession con geo y dispositivo ─────────────
        $userSession = $this->crearUserSession($user, $geoData);

        // Guardar UUID de sesión en la sesión Laravel
        session(['user_session_uuid' => $userSession->uuid]);

        // Actualizar el RequestContext con el usuario autenticado
        $sedeId = $user->sedePrincipal()?->id;
        $this->ctx->populateFromUser($user, $sedeId);
        $this->ctx->setSessionUuid($userSession->uuid);

        // ── 7. Registrar access_log exitoso ─────────────────────────
        $this->audit->logAccess(
            evento:         'login',
            resultado:      'success',
            userId:         $user->id,
            emailIntento:   $email,
            organizacionId: $user->organizacion_id,
            sedeId:         $sedeId,
            sessionUuid:    $userSession->uuid,
        );

        // ── 8. Audit log de login ────────────────────────────────────
        $this->audit->log(
            modulo:      'seguridad',
            accion:      'login',
            descripcion: "Inicio de sesión: {$user->nombre_completo}",
            model:       User::class,
            modelId:     $user->id,
            modelDesc:   $user->email,
            resultado:   'success',
        );

        return LoginResult::success($user, $userSession);
    }

    // ================================================================
    // Logout
    // ================================================================

    /**
     * Cierra la sesión del usuario autenticado y revoca su UserSession.
     */
    public function logout(?string $motivo = 'logout'): void
    {
        $user = Auth::user();

        if ($user) {
            // Revocar la sesión activa
            $sessionUuid = session('user_session_uuid');
            if ($sessionUuid) {
                UserSession::where('uuid', $sessionUuid)
                    ->where('user_id', $user->id)
                    ->first()
                    ?->revocar($motivo);
            }

            // Audit log
            $this->audit->log(
                modulo:      'seguridad',
                accion:      'logout',
                descripcion: "Cierre de sesión: {$user->nombre_completo}",
                model:       User::class,
                modelId:     $user->id,
                modelDesc:   $user->email,
                resultado:   'success',
            );

            // Access log
            $this->audit->logAccess(
                evento:         'logout',
                resultado:      'success',
                userId:         $user->id,
                organizacionId: $user->organizacion_id,
                sessionUuid:    $sessionUuid,
            );
        }

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    // ================================================================
    // Revocar otras sesiones (logout de todos los dispositivos)
    // ================================================================

    /**
     * Revoca todas las sesiones activas del usuario excepto la actual.
     */
    public function revocarOtrasSesiones(User $user, string $motivo = 'admin_revoke'): int
    {
        $currentUuid = session('user_session_uuid');

        $sesiones = UserSession::where('user_id', $user->id)
            ->where('active', true)
            ->when($currentUuid, fn($q) => $q->where('uuid', '!=', $currentUuid))
            ->get();

        foreach ($sesiones as $sesion) {
            $sesion->revocar($motivo);
        }

        $count = $sesiones->count();

        if ($count > 0) {
            $this->audit->log(
                modulo:      'seguridad',
                accion:      'session_revoked',
                descripcion: "Se revocaron {$count} sesiones del usuario",
                model:       User::class,
                modelId:     $user->id,
                motivo:      $motivo,
            );
        }

        return $count;
    }

    /**
     * Revoca una sesión específica por UUID (admin o el propio usuario).
     */
    public function revocarSesion(string $sessionUuid, string $motivo = 'manual'): bool
    {
        $sesion = UserSession::where('uuid', $sessionUuid)->first();
        if (!$sesion) return false;

        $sesion->revocar($motivo);

        $this->audit->log(
            modulo:      'seguridad',
            accion:      'session_revoked',
            descripcion: "Sesión revocada: {$sessionUuid}",
            model:       UserSession::class,
            modelId:     $sesion->id,
            motivo:      $motivo,
        );

        return true;
    }

    // ================================================================
    // Helpers privados
    // ================================================================

    private function crearUserSession(User $user, array $geoData): UserSession
    {
        $geo = $this->ctx->getGeo();

        return UserSession::create([
            'uuid'              => (string) Str::uuid(),
            'user_id'           => $user->id,
            'organizacion_id'   => $user->organizacion_id,
            'sede_id'           => $user->sedePrincipal()?->id,
            // Geo del momento del login (viene del frontend via GeoCapture)
            'latitud'           => $geoData['latitude']  ?? $geo?->latitud,
            'longitud'          => $geoData['longitude'] ?? $geo?->longitud,
            'precision_metros'  => $geoData['accuracy']  ?? $geo?->precisionMetros,
            'altitud'           => $geoData['altitude']  ?? $geo?->altitud,
            'fuente_ubicacion'  => $geoData['source']    ?? $geo?->fuente ?? 'unknown',
            // Dispositivo
            'device_id'         => $geo?->deviceId,
            'device_name'       => $this->buildDeviceName($geo),
            'device_type'       => $geo?->deviceType,
            'sistema_operativo' => $geo?->sistemaOperativo,
            'navegador'         => $geo?->navegador,
            'user_agent'        => $geo?->userAgent,
            // Red
            'ip_address'        => $geo?->ipAddress ?? request()->ip(),
            // Pantalla
            'pantalla_ancho'    => $geo?->pantallaAncho,
            'pantalla_alto'     => $geo?->pantallaAlto,
            'zona_horaria'      => $geo?->zonaHoraria,
            'idioma'            => $geo?->idioma,
            'pixel_ratio'       => $geo?->pixelRatio,
            'es_touch'          => $geo?->esTouch ?? false,
            // Tiempos
            'first_seen_at'     => now(),
            'last_seen_at'      => now(),
            'active'            => true,
        ]);
    }

    private function buildDeviceName(?GeoContext $geo): string
    {
        if (!$geo) return 'Dispositivo desconocido';
        $parts = array_filter([$geo->navegador, $geo->sistemaOperativo, $geo->deviceType]);
        return implode(' / ', $parts) ?: 'Dispositivo desconocido';
    }
}
