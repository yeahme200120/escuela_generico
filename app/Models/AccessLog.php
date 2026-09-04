<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessLog extends Model
{
    use HasUuid;

    protected $table = 'access_logs';

    // Inmutable — sólo insert
    public $timestamps = false;

    protected $fillable = [
        'uuid', 'user_id', 'email_intento', 'organizacion_id', 'sede_id', 'session_uuid',
        'evento', 'resultado', 'motivo_rechazo',
        'ip_address', 'device_id', 'device_type', 'sistema_operativo', 'navegador', 'user_agent',
        'latitud', 'longitud', 'precision_metros', 'fuente_ubicacion',
        'es_nuevo_dispositivo', 'es_nueva_ubicacion', 'fuera_de_geocerca',
        'fuera_de_horario', 'viaje_imposible',
        'distancia_ultimo_acceso_km', 'minutos_desde_ultimo_acceso',
        'request_id', 'created_at',
    ];

    protected $casts = [
        'created_at'                   => 'datetime',
        'es_nuevo_dispositivo'         => 'boolean',
        'es_nueva_ubicacion'           => 'boolean',
        'fuera_de_geocerca'            => 'boolean',
        'fuera_de_horario'             => 'boolean',
        'viaje_imposible'              => 'boolean',
        'latitud'                      => 'decimal:8',
        'longitud'                     => 'decimal:8',
        'precision_metros'             => 'decimal:2',
        'distancia_ultimo_acceso_km'   => 'decimal:3',
    ];

    public function user(): BelongsTo         { return $this->belongsTo(User::class);         }
    public function organizacion(): BelongsTo { return $this->belongsTo(Organizacion::class); }
    public function sede(): BelongsTo         { return $this->belongsTo(Sede::class);          }

    public function scopeExitosos($q) { return $q->where('resultado', 'success'); }
    public function scopeFallidos($q) { return $q->where('resultado', 'failed');  }
    public function scopeSospechosos($q)
    {
        return $q->where(function ($q2) {
            $q2->where('viaje_imposible', true)
               ->orWhere('es_nuevo_dispositivo', true)
               ->orWhere('fuera_de_geocerca', true);
        });
    }

    public function tieneAnomalias(): bool
    {
        return $this->viaje_imposible
            || $this->es_nueva_ubicacion
            || $this->fuera_de_geocerca
            || $this->fuera_de_horario;
    }
}
