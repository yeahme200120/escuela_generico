<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    protected $table = 'asistencias';

    /**
     * Solo tiene created_at (timestamp useCurrent), sin updated_at.
     */
    public $timestamps = false;

    protected $attributes = [
        'created_at' => null,
    ];

    protected $fillable = [
        'alumno_id',
        'grupo_id',
        'materia_id',
        'docente_id',
        'ciclo_escolar_id',
        'fecha',
        'estado',
        'hora_registro',
        'observacion',
        'registrado_por',
        'created_at',
    ];

    protected $casts = [
        'fecha' => 'date',
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

    public function cicloEscolar(): BelongsTo
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_escolar_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeDelDia($query, string $fecha)
    {
        return $query->where('fecha', $fecha);
    }

    public function scopeDelGrupo($query, int $grupoId)
    {
        return $query->where('grupo_id', $grupoId);
    }

    public function scopeDeFalta($query)
    {
        return $query->where('estado', 'falta');
    }

    public function scopeDeRetardo($query)
    {
        return $query->where('estado', 'retardo');
    }
}
