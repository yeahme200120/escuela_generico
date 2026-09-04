<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Docente extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'docentes';

    protected $fillable = [
        'user_id',
        'numero_empleado',
        'especialidad',
        'cedula',
        'fecha_ingreso',
        'tipo_contrato',
        'estatus',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(DocenteGrupoMateria::class, 'docente_id');
    }

    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(Grupo::class, 'docente_grupo_materia', 'docente_id', 'grupo_id')
                    ->withPivot(['materia_id', 'ciclo_escolar_id', 'sede_id', 'horas_semana', 'activo'])
                    ->withTimestamps();
    }

    public function materias(): BelongsToMany
    {
        return $this->belongsToMany(Materia::class, 'docente_grupo_materia', 'docente_id', 'materia_id')
                    ->withPivot(['grupo_id', 'ciclo_escolar_id', 'sede_id', 'horas_semana', 'activo'])
                    ->withTimestamps();
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeActivos($query)
    {
        return $query->where('estatus', 'activo');
    }

    // ----------------------------------------------------------------
    // Auditoría
    // ----------------------------------------------------------------

    public function getAuditDescription(): string
    {
        $empleado = $this->numero_empleado ?? $this->id;
        return "Docente #{$empleado}";
    }
}
