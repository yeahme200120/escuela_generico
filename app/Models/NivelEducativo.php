<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NivelEducativo extends Model
{
    use Auditable;

    protected $table = 'niveles_educativos';

    protected $fillable = [
        'escuela_id', 'nombre', 'clave', 'orden', 'duracion_anos',
        'calificacion_minima', 'calificacion_maxima', 'activo',
    ];

    protected $casts = [
        'activo'              => 'boolean',
        'calificacion_minima' => 'decimal:2',
        'calificacion_maxima' => 'decimal:2',
    ];

    public function escuela(): BelongsTo { return $this->belongsTo(Escuela::class); }
    public function grados(): HasMany    { return $this->hasMany(Grado::class, 'nivel_educativo_id'); }

    public function scopeActivos($q)  { return $q->where('activo', true)->orderBy('orden'); }
}
