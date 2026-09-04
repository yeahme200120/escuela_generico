<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSede extends Model
{
    protected $table = 'user_sedes';

    protected $fillable = ['user_id', 'sede_id', 'es_principal', 'activo'];

    protected $casts = [
        'es_principal' => 'boolean',
        'activo'       => 'boolean',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function sede(): BelongsTo { return $this->belongsTo(Sede::class); }

    public function scopeActivos($q)    { return $q->where('activo', true); }
    public function scopePrincipales($q){ return $q->where('es_principal', true); }
}
