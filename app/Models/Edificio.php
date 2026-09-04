<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Edificio extends Model
{
    use Auditable;

    protected $table = 'edificios';

    protected $fillable = [
        'sede_id', 'nombre', 'clave', 'numero_pisos', 'descripcion', 'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    public function sede(): BelongsTo   { return $this->belongsTo(Sede::class);  }
    public function aulas(): HasMany    { return $this->hasMany(Aula::class);     }

    public function scopeActivos($q)    { return $q->where('activo', true); }

    public function getAuditDescription(): string
    {
        return "Edificio: {$this->nombre} (Sede #{$this->sede_id})";
    }
}
