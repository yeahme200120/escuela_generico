<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calificacion extends Model
{
    use Auditable;

    protected $table = 'calificaciones';

    protected $fillable = [
        'alumno_id',
        'grupo_id',
        'materia_id',
        'docente_id',
        'periodo_evaluacion_id',
        'ciclo_escolar_id',
        'calificacion',
        'calificacion_letra',
        'resultado',
        'observaciones',
        'usuario_registra',
        'usuario_actualiza',
    ];

    protected $casts = [
        'calificacion' => 'decimal:2',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class, 'docente_id');
    }

    public function periodoEvaluacion(): BelongsTo
    {
        return $this->belongsTo(PeriodoEvaluacion::class, 'periodo_evaluacion_id');
    }

    public function cicloEscolar(): BelongsTo
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_escolar_id');
    }

    public function usuarioRegistra(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_registra');
    }

    public function usuarioActualiza(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_actualiza');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeReprobadas($query)
    {
        return $query->where('resultado', 'reprobado');
    }

    public function scopeDelAlumno($query, int $alumnoId)
    {
        return $query->where('alumno_id', $alumnoId);
    }

    public function scopeDelGrupo($query, int $grupoId)
    {
        return $query->where('grupo_id', $grupoId);
    }

    public function scopeDelPeriodo($query, int $periodoId)
    {
        return $query->where('periodo_evaluacion_id', $periodoId);
    }

    // ----------------------------------------------------------------
    // Auditoría
    // ----------------------------------------------------------------

    public function getAuditDescription(): string
    {
        return "Calificación #{$this->id} (alumno {$this->alumno_id}, materia {$this->materia_id})";
    }
}
