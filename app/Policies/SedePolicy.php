<?php

namespace App\Policies;

use App\Models\Sede;
use App\Models\User;

class SedePolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        // Permiso base
        if (!$user->puedeHacer('sedes.ver')) return false;
        // Si es superadmin o tiene permiso global, puede ver todas
        if ($user->isSuperAdmin()) return true;
        // Si no, solo las de su organización (ya que el modelo Sede tiene organizacion_id)
        // Pero la consulta en el controlador debe filtrar por organizacion_id
        return true; // El controlador se encargará de filtrar por organización
    }

    public function view(User $user, Sede $sede): bool
    {
        // Debe pertenecer a la organización
        if ($user->organizacion_id !== $sede->organizacion_id) return false;
        // Además debe tener acceso a esa sede
        $tieneSede = $user->userSedes()->where('sede_id', $sede->id)->where('activo', true)->exists();
        return $tieneSede || $user->puedeHacer('sedes.ver', $sede->id);
    }

    public function create(User $user): bool
    {
        return $user->puedeHacer('sedes.crear');
    }

    public function update(User $user, Sede $sede): bool
    {
        if ($user->organizacion_id !== $sede->organizacion_id) return false;
        return $user->puedeHacer('sedes.editar', $sede->id);
    }

    public function delete(User $user, Sede $sede): bool
    {
        return false; // Las sedes nunca se eliminan físicamente
    }
}
