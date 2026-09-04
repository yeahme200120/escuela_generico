<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tutor extends Model
{
    use HasUuid, Auditable;

    protected $table = 'tutores';

    protected $fillable = [
        'uuid',
        'organizacion_id',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'parentesco',
        'email',
        'telefono',
        'telefono_trabajo',
        'ocupacion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function alumnos(): BelongsToMany
    {
        return $this->belongsToMany(Alumno::class, 'alumno_tutor', 'tutor_id', 'alumno_id')
                    ->withPivot(['es_principal', 'autorizado_recoger'])
                    ->withTimestamps();
    }

    // ----------------------------------------------------------------
    // Accessors
    // ----------------------------------------------------------------

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres} {$this->apellido_paterno} {$this->apellido_materno}");
    }

    // ----------------------------------------------------------------
    // Auditoría
    // ----------------------------------------------------------------

    public function getAuditDescription(): string
    {
        return "Tutor: {$this->nombre_completo}";
    }
}
