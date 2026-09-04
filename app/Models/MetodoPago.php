<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetodoPago extends Model
{
    protected $table = 'metodos_pago';

    protected $fillable = [
        'nombre',
        'clave',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'metodo_pago_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
