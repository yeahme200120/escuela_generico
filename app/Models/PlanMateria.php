<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo pivot para la tabla plan_materias.
 * No usa Auditable para evitar loops en operaciones masivas de plan.
 */
class PlanMateria extends Model
{
    protected $table = 'plan_materias';

    protected $fillable = [
        'plan_estudio_id',
        'grado_id',
        'materia_id',
        'obligatoria',
        'orden',
    ];

    protected $casts = [
        'obligatoria' => 'boolean',
        'orden'       => 'integer',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function planEstudio(): BelongsTo
    {
        return $this->belongsTo(PlanEstudio::class, 'plan_estudio_id');
    }

    public function grado(): BelongsTo
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }
}
