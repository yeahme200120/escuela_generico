<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Horario extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'horarios';

    protected $fillable = [
        'grupo_id',
        'materia_id',
        'docente_id',
        'aula_id',
        'ciclo_escolar_id',
        'horario_bloque_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'publicado',
    ];

    protected $casts = [
        'publicado' => 'boolean',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

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

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class, 'aula_id');
    }

    public function cicloEscolar(): BelongsTo
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_escolar_id');
    }

    public function horarioBloque(): BelongsTo
    {
        return $this->belongsTo(HorarioBloque::class, 'horario_bloque_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopePublicados($query)
    {
        return $query->where('publicado', true);
    }

    public function scopeDelGrupo($query, int $grupoId)
    {
        return $query->where('grupo_id', $grupoId);
    }

    public function scopeDelDocente($query, int $docenteId)
    {
        return $query->where('docente_id', $docenteId);
    }

    // ----------------------------------------------------------------
    // Auditoría
    // ----------------------------------------------------------------

    public function getAuditDescription(): string
    {
        return "Horario #{$this->id} (grupo {$this->grupo_id}, día {$this->dia_semana})";
    }
}
