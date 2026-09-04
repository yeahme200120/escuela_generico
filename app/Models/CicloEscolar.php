<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CicloEscolar extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'ciclos_escolares';

    protected $fillable = [
        'organizacion_id', 'escuela_id', 'nombre', 'clave',
        'fecha_inicio', 'fecha_fin', 'estatus', 'es_actual', 'periodos',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'es_actual'    => 'boolean',
        'periodos'     => 'array',
    ];

    public function organizacion(): BelongsTo { return $this->belongsTo(Organizacion::class); }
    public function escuela(): BelongsTo      { return $this->belongsTo(Escuela::class);      }
    public function grupos(): HasMany         { return $this->hasMany(Grupo::class, 'ciclo_escolar_id'); }

    public function scopeActivo($q)    { return $q->where('estatus', 'activo');   }
    public function scopeActual($q)    { return $q->where('es_actual', true);     }

    public function getAuditDescription(): string
    {
        return "Ciclo: {$this->nombre}";
    }
}
