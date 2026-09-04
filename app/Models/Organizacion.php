<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organizacion extends Model
{
    use HasUuid, Auditable, SoftDeletes;

    protected $table = 'organizaciones';

    protected $fillable = [
        'uuid', 'nombre', 'razon_social', 'rfc', 'clave', 'email', 'telefono',
        'sitio_web', 'logo', 'favicon', 'slogan', 'direccion', 'ciudad',
        'estado', 'pais', 'codigo_postal', 'latitud', 'longitud',
        'activa', 'modulo_finanzas_activo', 'modulo_rh_activo',
        'modulo_inventario_activo', 'modulo_admisiones_activo',
        'permite_modo_oscuro', 'configuracion',
    ];

    protected $casts = [
        'activa'                    => 'boolean',
        'modulo_finanzas_activo'    => 'boolean',
        'modulo_rh_activo'          => 'boolean',
        'modulo_inventario_activo'  => 'boolean',
        'modulo_admisiones_activo'  => 'boolean',
        'permite_modo_oscuro'       => 'boolean',
        'latitud'                   => 'decimal:8',
        'longitud'                  => 'decimal:8',
        'configuracion'             => 'array',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------
    public function escuelas(): HasMany
    {
        return $this->hasMany(Escuela::class, 'organizacion_id');
    }

    public function sedes(): HasMany
    {
        return $this->hasMany(Sede::class, 'organizacion_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'organizacion_id');
    }

    public function systemSettings(): HasMany
    {
        return $this->hasMany(SystemSetting::class, 'organizacion_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------
    public function getAuditDescription(): string
    {
        return "Organización: {$this->nombre}";
    }

    /**
     * Obtiene un setting del tema/configuración desde system_settings.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return $this->systemSettings()
            ->where('key', $key)
            ->value('value') ?? $default;
    }
}
