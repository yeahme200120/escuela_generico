<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TurnoCaja extends Model
{
    use Auditable;

    protected $table = 'turnos_caja';

    protected $fillable = [
        'caja_id',
        'usuario_id',
        'fecha_apertura',
        'fecha_cierre',
        'monto_apertura',
        'monto_cierre',
        'monto_esperado',
        'diferencia',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'monto_apertura' => 'decimal:2',
        'monto_cierre'   => 'decimal:2',
        'monto_esperado' => 'decimal:2',
        'diferencia'     => 'decimal:2',
        'fecha_apertura' => 'datetime',
        'fecha_cierre'   => 'datetime',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoCaja::class, 'turno_caja_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeAbiertos($query)
    {
        return $query->where('estado', 'abierto');
    }

    public function scopeDelUsuario($query, int $userId)
    {
        return $query->where('usuario_id', $userId);
    }

    // ----------------------------------------------------------------
    // Métodos de negocio
    // ----------------------------------------------------------------

    public function estaAbierto(): bool
    {
        return $this->estado === 'abierto';
    }

    // ----------------------------------------------------------------
    // Auditoría
    // ----------------------------------------------------------------

    public function getAuditDescription(): string
    {
        return "TurnoCaja #{$this->id} (caja {$this->caja_id}, usuario {$this->usuario_id})";
    }
}
