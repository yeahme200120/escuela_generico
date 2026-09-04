<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aula extends Model
{
    use Auditable;

    protected $table = 'aulas';

    protected $fillable = [
        'sede_id', 'edificio_id', 'nombre', 'clave', 'tipo',
        'capacidad', 'piso', 'tiene_proyector', 'tiene_ac', 'activa', 'observaciones',
    ];

    protected $casts = [
        'tiene_proyector' => 'boolean',
        'tiene_ac'        => 'boolean',
        'activa'          => 'boolean',
    ];

    public function sede(): BelongsTo    { return $this->belongsTo(Sede::class);     }
    public function edificio(): BelongsTo{ return $this->belongsTo(Edificio::class); }

    public function scopeActivas($q)    { return $q->where('activa', true); }
    public function scopeDeSede($q, int $sedeId) { return $q->where('sede_id', $sedeId); }

    public function getAuditDescription(): string
    {
        return "Aula: {$this->nombre} (Sede #{$this->sede_id})";
    }
}
