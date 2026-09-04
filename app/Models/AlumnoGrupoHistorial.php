<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para la tabla alumno_grupo_historial.
 * No usa Auditable para evitar loops en movimientos masivos de grupos.
 */
class AlumnoGrupoHistorial extends Model
{
    protected $table = 'alumno_grupo_historial';

    protected $fillable = [
        'alumno_id',
        'grupo_id',
        'ciclo_escolar_id',
        'fecha_inicio',
        'fecha_fin',
        'motivo',
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

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    public function cicloEscolar(): BelongsTo
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_escolar_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
