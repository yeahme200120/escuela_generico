<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Materia extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'materias';

    protected $fillable = [
        'escuela_id',
        'clave',
        'nombre',
        'descripcion',
        'horas_semana',
        'creditos',
        'tipo',
        'activa',
    ];

    protected $casts = [
        'activa'      => 'boolean',
        'horas_semana' => 'integer',
        'creditos'    => 'integer',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function escuela(): BelongsTo
    {
        return $this->belongsTo(Escuela::class, 'escuela_id');
    }

    public function planMaterias(): HasMany
    {
        return $this->hasMany(PlanMateria::class, 'materia_id');
    }

    public function docenteGrupoMaterias(): HasMany
    {
        return $this->hasMany(DocenteGrupoMateria::class, 'materia_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeDeEscuela($query, int $escuelaId)
    {
        return $query->where('escuela_id', $escuelaId);
    }

    public function scopeDeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // ----------------------------------------------------------------
    // Auditoría
    // ----------------------------------------------------------------

    public function getAuditDescription(): string
    {
        return "Materia: {$this->nombre} [{$this->tipo}]";
    }
}
