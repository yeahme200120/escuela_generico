<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estudiante extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'estudiantes';

    protected $fillable = [
        'organizacion_id',
        'matricula',
        'curp',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'fecha_nacimiento',
        'sexo',
        'email',
        'telefono',
        'direccion',
        'foto',
        'fecha_ingreso',
        'estatus',
        'situacion_academica',
        'situacion_inscripcion',
        'estatus_riesgo',
        'sede_actual_id'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_ingreso' => 'date'
    ];

    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class);
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_actual_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellido_paterno} " . ($this->apellido_materno ?? '');
    }
}

