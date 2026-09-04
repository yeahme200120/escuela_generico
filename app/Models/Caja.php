<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caja extends Model
{
    use Auditable;

    protected $table = 'cajas';

    protected $fillable = [
        'sede_id',
        'nombre',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function turnos(): HasMany
    {
        return $this->hasMany(TurnoCaja::class, 'caja_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    // ----------------------------------------------------------------
    // Auditoría
    // ----------------------------------------------------------------

    public function getAuditDescription(): string
    {
        return "Caja #{$this->id}: {$this->nombre}";
    }
}
