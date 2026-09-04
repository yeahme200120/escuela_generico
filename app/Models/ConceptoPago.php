<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConceptoPago extends Model
{
    use Auditable;

    protected $table = 'conceptos_pago';

    protected $fillable = [
        'organizacion_id',
        'sede_id',
        'nombre',
        'clave',
        'tipo',
        'importe_default',
        'activo',
    ];

    protected $casts = [
        'activo'          => 'boolean',
        'importe_default' => 'decimal:2',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function cargos(): HasMany
    {
        return $this->hasMany(Cargo::class, 'concepto_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // ----------------------------------------------------------------
    // Auditoría
    // ----------------------------------------------------------------

    public function getAuditDescription(): string
    {
        return "ConceptoPago #{$this->id}: {$this->nombre}";
    }
}
