<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $fillable = [
        'nombre', 'slug', 'modulo', 'accion', 'descripcion', 'alcance_default', 'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id')
                    ->withPivot('alcance')
                    ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_permissions', 'permission_id', 'user_id')
                    ->withPivot(['sede_id', 'alcance', 'concedido'])
                    ->withTimestamps();
    }

    public function scopeActivos($q)        { return $q->where('activo', true);       }
    public function scopeDeModulo($q, $m)   { return $q->where('modulo', $m);         }
}
