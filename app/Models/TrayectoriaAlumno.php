<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para la tabla trayectorias_alumno.
 * No usa Auditable para evitar loops al registrar eventos masivos de inscripción.
 */
class TrayectoriaAlumno extends Model
{
    protected $table = 'trayectorias_alumno';

    protected $fillable = [
        'alumno_id',
        'ciclo_escolar_id',
        'sede_id',
        'grado_id',
        'grupo_id',
        'estatus',
        'situacion_academica',
        'fecha_inicio',
        'fecha_fin',
        'motivo',
        'observaciones',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function cicloEscolar(): BelongsTo
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_escolar_id');
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function grado(): BelongsTo
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
