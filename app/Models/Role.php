<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use Auditable;

    protected $table = 'roles';

    protected $fillable = [
        'organizacion_id', 'nombre', 'slug', 'descripcion',
        'es_sistema', 'nivel', 'activo',
    ];

    protected $casts = [
        'es_sistema' => 'boolean',
        'activo'     => 'boolean',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------
    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id')
                    ->withPivot('alcance')
                    ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id')
                    ->withPivot(['sede_id', 'escuela_id', 'activo'])
                    ->withTimestamps();
    }

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class, 'role_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------
    public function scopeActivos($q)    { return $q->where('activo', true);     }
    public function scopeDeSistema($q)  { return $q->where('es_sistema', true); }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------
    public function tienePermiso(string $slug): bool
    {
        return $this->permissions()->where('slug', $slug)->exists();
    }

    public function getAuditDescription(): string
    {
        return "Rol: {$this->nombre}";
    }
}
