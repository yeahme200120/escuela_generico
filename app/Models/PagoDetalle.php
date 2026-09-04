<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoDetalle extends Model
{
    protected $table = 'pago_detalle';

    protected $fillable = [
        'pago_id',
        'cargo_id',
        'parcialidad_id',
        'importe_aplicado',
    ];

    protected $casts = [
        'importe_aplicado' => 'decimal:2',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }

    public function parcialidad(): BelongsTo
    {
        return $this->belongsTo(Parcialidad::class, 'parcialidad_id');
    }
}
