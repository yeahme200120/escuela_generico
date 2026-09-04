<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Enums\ResultadoAuditoria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasUuid;

    protected $table = 'audit_logs';

    // Completamente inmutable — no existe updated_at ni delete
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'uuid', 'request_id', 'session_uuid',
        'user_id', 'user_nombre', 'user_email', 'user_rol',
        'organizacion_id', 'sede_id', 'sede_nombre',
        'modulo', 'accion', 'evento', 'descripcion',
        'model', 'model_id', 'model_descripcion',
        'before_data', 'after_data', 'changes', 'motivo',
        'permission_usado', 'alcance_permiso',
        'ip_address', 'device_id', 'device_type', 'sistema_operativo', 'navegador', 'user_agent',
        'latitud', 'longitud', 'precision_metros', 'altitud', 'velocidad',
        'fuente_ubicacion', 'dentro_geocerca',
        'resultado', 'motivo_fallo', 'http_status', 'duracion_ms',
        'metadata', 'created_at',
    ];

    protected $casts = [
        'created_at'       => 'datetime',
        'before_data'      => 'array',
        'after_data'       => 'array',
        'changes'          => 'array',
        'metadata'         => 'array',
        'latitud'          => 'decimal:8',
        'longitud'         => 'decimal:8',
        'precision_metros' => 'decimal:2',
        'dentro_geocerca'  => 'boolean',
        'resultado'        => ResultadoAuditoria::class,
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------
    public function user(): BelongsTo         { return $this->belongsTo(User::class);         }
    public function organizacion(): BelongsTo { return $this->belongsTo(Organizacion::class); }
    public function sede(): BelongsTo         { return $this->belongsTo(Sede::class);          }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------
    public function scopeDeModulo($q, string $modulo) { return $q->where('modulo', $modulo);         }
    public function scopeDeAccion($q, string $accion) { return $q->where('accion', $accion);         }
    public function scopeExitosos($q)                 { return $q->where('resultado', 'success');    }
    public function scopeFallidos($q)                 { return $q->where('resultado', '!=', 'success'); }
    public function scopeDeUsuario($q, int $userId)   { return $q->where('user_id', $userId);        }
    public function scopeDeModelo($q, string $model, int $id)
    {
        return $q->where('model', $model)->where('model_id', $id);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------
    public function getGeoStringAttribute(): string
    {
        if (!$this->latitud) return 'Sin ubicación';
        $prec = $this->precision_metros ? " (±{$this->precision_metros}m)" : '';
        return "{$this->latitud}, {$this->longitud}{$prec}";
    }

    public function tieneChanges(): bool
    {
        return !empty($this->changes);
    }

    /**
     * Describe el cambio de un campo específico en lenguaje legible.
     */
    public function getCambio(string $campo): ?array
    {
        return $this->changes[$campo] ?? null;
    }
}
