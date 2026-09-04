<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanEstudio extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'planes_estudio';

    protected $fillable = [
        'escuela_id',
        'nombre',
        'clave',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function escuela(): BelongsTo
    {
        return $this->belongsTo(Escuela::class, 'escuela_id');
    }

    /**
     * Materias del plan con pivots: grado_id, obligatoria, orden.
     */
    public function materias(): BelongsToMany
    {
        return $this->belongsToMany(Materia::class, 'plan_materias', 'plan_estudio_id', 'materia_id')
                    ->withPivot(['grado_id', 'obligatoria', 'orden'])
                    ->withTimestamps();
    }

    /**
     * Grados asociados a través de plan_materias.
     */
    public function grados(): HasManyThrough
    {
        return $this->hasManyThrough(
            Grado::class,
            PlanMateria::class,
            'plan_estudio_id', // FK en plan_materias -> planes_estudio
            'id',              // PK en grados
            'id',              // PK local en planes_estudio
            'grado_id'         // FK en plan_materias -> grados
        );
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // ----------------------------------------------------------------
    // Auditoría
    // ----------------------------------------------------------------

    public function getAuditDescription(): string
    {
        return "Plan de estudio: {$this->nombre}";
    }
}
