<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alumno extends Model
{
    use HasUuid, Auditable, SoftDeletes;

    protected $table = 'alumnos';

    protected $fillable = [
        'uuid',
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
        'sede_actual_id',
    ];

    protected $casts = [
        'fecha_nacimiento'     => 'date',
        'fecha_ingreso'        => 'date',
        'estatus'              => 'string',
        'situacion_academica'  => 'string',
        'situacion_inscripcion' => 'string',
        'estatus_riesgo'       => 'string',
    ];

    protected $hidden = [];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    public function sedeActual(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_actual_id');
    }

    public function tutores(): BelongsToMany
    {
        return $this->belongsToMany(Tutor::class, 'alumno_tutor', 'alumno_id', 'tutor_id')
                    ->withPivot(['es_principal', 'autorizado_recoger'])
                    ->withTimestamps();
    }

    public function trayectorias(): HasMany
    {
        return $this->hasMany(TrayectoriaAlumno::class, 'alumno_id');
    }

    public function bajas(): HasMany
    {
        return $this->hasMany(Baja::class, 'alumno_id');
    }

    public function grupoHistorial(): HasMany
    {
        return $this->hasMany(AlumnoGrupoHistorial::class, 'alumno_id');
    }

    // ----------------------------------------------------------------
    // Accessors
    // ----------------------------------------------------------------

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres} {$this->apellido_paterno} {$this->apellido_materno}");
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeActivos($query)
    {
        return $query->where('estatus', 'activo');
    }

    public function scopeConRiesgo($query, string $nivel)
    {
        return $query->where('estatus_riesgo', $nivel);
    }

    public function scopeDeOrganizacion($query, int $organizacionId)
    {
        return $query->where('organizacion_id', $organizacionId);
    }

    public function scopeDeSede($query, int $sedeId)
    {
        return $query->where('sede_actual_id', $sedeId);
    }

    // ----------------------------------------------------------------
    // Auditoría
    // ----------------------------------------------------------------

    public function getAuditDescription(): string
    {
        $matricula = $this->matricula ?? 'sin matrícula';
        return "Alumno: {$this->nombre_completo} ({$matricula})";
    }
}
