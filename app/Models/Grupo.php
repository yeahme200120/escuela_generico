<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grupo extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'grupos';

    protected $fillable = [
        'sede_id', 'ciclo_escolar_id', 'grado_id', 'aula_principal_id',
        'docente_tutor_id', 'nombre', 'turno', 'capacidad', 'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    public function sede(): BelongsTo         { return $this->belongsTo(Sede::class);         }
    public function cicloEscolar(): BelongsTo { return $this->belongsTo(CicloEscolar::class, 'ciclo_escolar_id'); }
    public function grado(): BelongsTo        { return $this->belongsTo(Grado::class);        }
    public function aulaPrincipal(): BelongsTo{ return $this->belongsTo(Aula::class, 'aula_principal_id');  }
    public function docenteTutor(): BelongsTo { return $this->belongsTo(User::class, 'docente_tutor_id');   }

    public function scopeActivos($q)  { return $q->where('activo', true); }
    public function scopeDeSede($q, int $sedeId) { return $q->where('sede_id', $sedeId); }

    public function getAuditDescription(): string
    {
        return "Grupo: {$this->nombre} (Grado #{$this->grado_id} / Ciclo #{$this->ciclo_escolar_id})";
    }
}
