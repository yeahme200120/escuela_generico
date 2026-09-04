<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Regularizacion extends Model
{
    use Auditable;

    protected $table = 'regularizaciones';

    protected $fillable = [
        'alumno_id',
        'materia_id',
        'ciclo_escolar_id',
        'calificacion_original',
        'calificacion_regularizacion',
        'fecha',
        'resultado',
        'observaciones',
        'usuario_id',
    ];

    protected $casts = [
        'calificacion_original'       => 'decimal:2',
        'calificacion_regularizacion' => 'decimal:2',
        'fecha'                       => 'date',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    public function cicloEscolar(): BelongsTo
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_escolar_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopePendientes($query)
    {
        return $query->where('resultado', 'pendiente');
    }

    public function scopeDelAlumno($query, int $alumnoId)
    {
        return $query->where('alumno_id', $alumnoId);
    }

    // ----------------------------------------------------------------
    // Auditoría
    // ----------------------------------------------------------------

    public function getAuditDescription(): string
    {
        return "Regularización #{$this->id} (alumno {$this->alumno_id}, materia {$this->materia_id})";
    }
}
