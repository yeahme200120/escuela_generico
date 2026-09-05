<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo Reporte — vista semántica sobre python_jobs filtrado por tipo reporte.
 * No tiene tabla propia; usa python_jobs con tipo IN ('reporte','importacion').
 */
class Reporte extends Model
{
    protected $table = 'python_jobs';

    protected $fillable = [
        'job_id', 'organizacion_id', 'usuario_id',
        'tipo', 'payload', 'resultado',
        'estado', 'archivo_resultado', 'error', 'progreso',
        'iniciado_at', 'completado_at',
    ];

    protected $casts = [
        'payload'       => 'array',
        'resultado'     => 'array',
        'iniciado_at'   => 'datetime',
        'completado_at' => 'datetime',
    ];

    /** Solo registros de tipo reporte */
    protected static function booted(): void
    {
        static::addGlobalScope('reportes', fn($q) =>
            $q->whereIn('tipo', ['reporte', 'importacion', 'estadisticas'])
        );
    }

    public function organizacion(): BelongsTo { return $this->belongsTo(Organizacion::class); }
    public function usuario(): BelongsTo      { return $this->belongsTo(User::class, 'usuario_id'); }

    public function scopeCompletados($q) { return $q->where('estado', 'completado'); }
    public function scopePendientes($q)  { return $q->whereIn('estado', ['pendiente', 'procesando']); }

    public function tieneArchivo(): bool { return !empty($this->archivo_resultado); }
}
