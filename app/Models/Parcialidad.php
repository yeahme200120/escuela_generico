<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parcialidad extends Model
{
    protected $table = 'parcialidades';

    protected $fillable = [
        'cargo_id',
        'numero',
        'fecha_vencimiento',
        'importe',
        'estado',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'importe'           => 'decimal:2',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeVencidas($query)
    {
        return $query->where('estado', 'vencido');
    }
}
