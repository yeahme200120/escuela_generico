<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRole extends Model
{
    protected $table = 'user_roles';

    protected $fillable = [
        'user_id', 'role_id', 'sede_id', 'escuela_id',
        'activo', 'valido_desde', 'valido_hasta',
    ];

    protected $casts = [
        'activo'       => 'boolean',
        'valido_desde' => 'datetime',
        'valido_hasta' => 'datetime',
    ];

    public function user(): BelongsTo  { return $this->belongsTo(User::class);  }
    public function role(): BelongsTo  { return $this->belongsTo(Role::class);  }
    public function sede(): BelongsTo  { return $this->belongsTo(Sede::class);  }

    public function scopeActivos($q)   { return $q->where('activo', true); }

    public function estaVigente(): bool
    {
        $now = now();
        if ($this->valido_desde && $now->lt($this->valido_desde)) return false;
        if ($this->valido_hasta && $now->gt($this->valido_hasta)) return false;
        return $this->activo;
    }
}
