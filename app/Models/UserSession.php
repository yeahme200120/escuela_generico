<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    use HasUuid;

    protected $table = 'user_sessions';

    protected $fillable = [
        'uuid', 'user_id', 'organizacion_id', 'sede_id',
        'token_id', 'sanctum_token_hash',
        'device_id', 'device_name', 'device_type',
        'sistema_operativo', 'navegador', 'version_navegador', 'user_agent',
        'ip_address', 'ip_country', 'ip_city',
        'latitud', 'longitud', 'precision_metros', 'altitud',
        'fuente_ubicacion', 'pantalla_ancho', 'pantalla_alto',
        'zona_horaria', 'idioma', 'pixel_ratio', 'es_touch',
        'first_seen_at', 'last_seen_at', 'revoked_at', 'revoked_reason', 'active',
    ];

    protected $casts = [
        'first_seen_at'    => 'datetime',
        'last_seen_at'     => 'datetime',
        'revoked_at'       => 'datetime',
        'active'           => 'boolean',
        'es_touch'         => 'boolean',
        'latitud'          => 'decimal:8',
        'longitud'         => 'decimal:8',
        'precision_metros' => 'decimal:2',
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
    public function scopeActivas($q)   { return $q->where('active', true);  }
    public function scopeDeUsuario($q, int $userId) { return $q->where('user_id', $userId); }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------
    public function revocar(string $motivo = 'logout'): void
    {
        $this->update([
            'active'         => false,
            'revoked_at'     => now(),
            'revoked_reason' => $motivo,
        ]);
    }

    public function tocarActividad(): void
    {
        $this->update(['last_seen_at' => now()]);
    }

    public function getGeoStringAttribute(): string
    {
        if (!$this->latitud) return 'Sin ubicación';
        return "{$this->latitud}, {$this->longitud} (±{$this->precision_metros}m)";
    }
}
