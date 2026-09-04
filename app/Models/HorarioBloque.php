<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HorarioBloque extends Model
{
    protected $table = 'horario_bloques';

    protected $fillable = [
        'sede_id',
        'ciclo_escolar_id',
        'nombre',
        'hora_inicio',
        'hora_fin',
        'dia_semana',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function cicloEscolar(): BelongsTo
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_escolar_id');
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class, 'horario_bloque_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeDeSede($query, int $sedeId)
    {
        return $query->where('sede_id', $sedeId);
    }
}
