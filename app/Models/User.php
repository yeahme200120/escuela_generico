<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasUuid, Auditable, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'uuid', 'organizacion_id', 'nombres', 'apellido_paterno', 'apellido_materno',
        'email', 'username', 'telefono', 'avatar', 'email_verified_at',
        'password', 'activo', 'ultimo_acceso_at', 'ultimo_ip',
        'intentos_fallidos', 'bloqueado_hasta',
        'tema_preferido', 'locale', 'zona_horaria',
        'two_factor_enabled', 'two_factor_secret', 'two_factor_recovery_codes',
    ];

    protected $hidden = [
        'password', 'remember_token',
        'two_factor_secret', 'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at'          => 'datetime',
        'ultimo_acceso_at'           => 'datetime',
        'bloqueado_hasta'            => 'datetime',
        'password'                   => 'hashed',
        'activo'                     => 'boolean',
        'two_factor_enabled'         => 'boolean',
        'two_factor_recovery_codes'  => 'array',
    ];

    // Campos excluidos de la auditoría
    protected array $auditExclude = [
        'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------
    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')
                    ->withPivot(['sede_id', 'escuela_id', 'activo', 'valido_desde', 'valido_hasta'])
                    ->withTimestamps();
    }

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class, 'user_id');
    }

    public function userPermissions(): HasMany
    {
        return $this->hasMany(UserPermission::class, 'user_id');
    }

    public function sedes(): BelongsToMany
    {
        return $this->belongsToMany(Sede::class, 'user_sedes', 'user_id', 'sede_id')
                    ->withPivot(['es_principal', 'activo'])
                    ->withTimestamps();
    }

    public function userSedes(): HasMany
    {
        return $this->hasMany(UserSede::class, 'user_id');
    }

    public function userSessions(): HasMany
    {
        return $this->hasMany(UserSession::class, 'user_id');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class, 'user_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------
    public function scopeActivos($q)
    {
        return $q->where('activo', true);
    }

    public function scopeDeOrganizacion($q, int $orgId)
    {
        return $q->where('organizacion_id', $orgId);
    }

    // ----------------------------------------------------------------
    // Helpers de RBAC
    // ----------------------------------------------------------------

    /**
     * Nombre completo del usuario.
     */
    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres} {$this->apellido_paterno} {$this->apellido_materno}");
    }

    /**
     * Verifica si el usuario tiene un rol (por slug).
     */
    public function hasRole(string $roleSlug, ?int $sedeId = null): bool
    {
        return $this->roles()
            ->where('slug', $roleSlug)
            ->where('user_roles.activo', true)
            ->when($sedeId, fn($q) => $q->where('user_roles.sede_id', $sedeId))
            ->exists();
    }

    /**
     * Verifica si el usuario es superadmin.
     */
    public function esSuperadmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    /**
     * Obtiene el listado de slugs de permisos efectivos del usuario.
     * Combina permisos de roles + permisos directos.
     */
    public function getPermisosEfectivos(?int $sedeId = null): array
    {
        // Permisos por roles activos
        $rolePerms = $this->roles()
            ->where('user_roles.activo', true)
            ->when($sedeId, fn($q) => $q->where(function ($q2) use ($sedeId) {
                $q2->where('user_roles.sede_id', $sedeId)->orWhereNull('user_roles.sede_id');
            }))
            ->with('permissions')
            ->get()
            ->flatMap(fn($r) => $r->permissions->pluck('slug'))
            ->unique()
            ->toArray();

        // Permisos directos concedidos
        $directGranted = $this->userPermissions()
            ->where('concedido', true)
            ->when($sedeId, fn($q) => $q->where(function ($q2) use ($sedeId) {
                $q2->where('sede_id', $sedeId)->orWhereNull('sede_id');
            }))
            ->with('permission')
            ->get()
            ->pluck('permission.slug')
            ->toArray();

        // Permisos denegados explícitamente
        $directDenied = $this->userPermissions()
            ->where('concedido', false)
            ->when($sedeId, fn($q) => $q->where(function ($q2) use ($sedeId) {
                $q2->where('sede_id', $sedeId)->orWhereNull('sede_id');
            }))
            ->with('permission')
            ->get()
            ->pluck('permission.slug')
            ->toArray();

        return array_diff(
            array_unique(array_merge($rolePerms, $directGranted)),
            $directDenied
        );
    }

    /**
     * Verifica si el usuario tiene un permiso específico.
     */
    public function puedeHacer(string $permissionSlug, ?int $sedeId = null): bool
    {
        if ($this->esSuperadmin()) return true;
        return in_array($permissionSlug, $this->getPermisosEfectivos($sedeId));
    }

    /**
     * Sede principal del usuario.
     */
    public function sedePrincipal(): ?Sede
    {
        return $this->sedes()
            ->wherePivot('es_principal', true)
            ->wherePivot('activo', true)
            ->first();
    }

    /**
     * ¿Está bloqueado temporalmente?
     */
    public function estaBloqueado(): bool
    {
        return $this->bloqueado_hasta && $this->bloqueado_hasta->isFuture();
    }

    /**
     * Descripción para auditoría.
     */
    public function getAuditDescription(): string
    {
        return "Usuario: {$this->nombre_completo} <{$this->email}>";
    }
}
