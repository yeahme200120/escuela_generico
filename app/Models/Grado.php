<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grado extends Model
{
    use Auditable;

    protected $table = 'grados';

    protected $fillable = ['nivel_educativo_id', 'nombre', 'clave', 'orden', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function nivelEducativo(): BelongsTo { return $this->belongsTo(NivelEducativo::class); }
    public function grupos(): HasMany           { return $this->hasMany(Grupo::class, 'grado_id'); }

    public function scopeActivos($q) { return $q->where('activo', true)->orderBy('orden'); }
}
