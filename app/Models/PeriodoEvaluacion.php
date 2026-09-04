<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodoEvaluacion extends Model
{
    use Auditable;

    protected $table = 'periodos_evaluacion';

    protected $fillable = [
        'ciclo_escolar_id',
        'nombre',
        'numero',
        'fecha_inicio',
        'fecha_fin',
        'fecha_cierre',
        'cerrado',
        'cerrado_at',
        'cerrado_por',
    ];

    protected $casts = [
        'cerrado'      => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'fecha_cierre' => 'date',
        'cerrado_at'   => 'datetime',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function cicloEscolar(): BelongsTo
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_escolar_id');
    }

    public function cerradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cerrado_por');
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class, 'periodo_evaluacion_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeAbiertos($query)
    {
        return $query->where('cerrado', false);
    }

    public function scopeDelCiclo($query, int $cicloId)
    {
        return $query->where('ciclo_escolar_id', $cicloId);
    }

    // ----------------------------------------------------------------
    // Auditoría
    // ----------------------------------------------------------------

    public function getAuditDescription(): string
    {
        return "Periodo de evaluación #{$this->id}: {$this->nombre}";
    }
}
