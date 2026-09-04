<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Escuela extends Model
{
    use HasUuid, Auditable, SoftDeletes;

    protected $table = 'escuelas';

    protected $fillable = [
        'uuid', 'organizacion_id', 'nombre', 'clave', 'clave_sep',
        'tipo_sostenimiento', 'nivel_sistema', 'email', 'telefono',
        'direccion', 'ciudad', 'estado', 'pais', 'activa', 'configuracion',
    ];

    protected $casts = [
        'activa'        => 'boolean',
        'configuracion' => 'array',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------
    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    public function sedes(): HasMany
    {
        return $this->hasMany(Sede::class, 'escuela_id');
    }

    public function nivelesEducativos(): HasMany
    {
        return $this->hasMany(NivelEducativo::class, 'escuela_id');
    }

    public function ciclosEscolares(): HasMany
    {
        return $this->hasMany(CicloEscolar::class, 'escuela_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeDeOrganizacion($query, int $organizacionId)
    {
        return $query->where('organizacion_id', $organizacionId);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------
    public function getAuditDescription(): string
    {
        return "Escuela: {$this->nombre}";
    }
}
