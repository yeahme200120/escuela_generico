<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoCaja extends Model
{
    protected $table = 'movimientos_caja';

    /**
     * Solo tiene created_at (timestamp useCurrent), sin updated_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'turno_caja_id',
        'tipo',
        'concepto',
        'importe',
        'referencia',
        'pago_id',
        'usuario_id',
        'created_at',
    ];

    protected $casts = [
        'importe' => 'decimal:2',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function turnoCaja(): BelongsTo
    {
        return $this->belongsTo(TurnoCaja::class, 'turno_caja_id');
    }

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeIngresos($query)
    {
        return $query->where('tipo', 'ingreso');
    }

    public function scopeEgresos($query)
    {
        return $query->whereIn('tipo', ['egreso', 'retiro']);
    }

    public function scopeDeTurno($query, int $turnoId)
    {
        return $query->where('turno_caja_id', $turnoId);
    }
}
