<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para la tabla docente_grupo_materia.
 * No usa Auditable para evitar loops en operaciones masivas de asignación.
 */
class DocenteGrupoMateria extends Model
{
    protected $table = 'docente_grupo_materia';

    protected $fillable = [
        'docente_id',
        'grupo_id',
        'materia_id',
        'ciclo_escolar_id',
        'sede_id',
        'horas_semana',
        'activo',
    ];

    protected $casts = [
        'activo'      => 'boolean',
        'horas_semana' => 'integer',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    public function cicloEscolar(): BelongsTo
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_escolar_id');
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeDelCiclo($query, int $cicloEscolarId)
    {
        return $query->where('ciclo_escolar_id', $cicloEscolarId);
    }
}
