<?php

namespace App\Policies;

use App\Models\Grupo;
use App\Models\User;

class GrupoPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->puedeHacer('grupos.ver');
    }

    public function view(User $user, Grupo $grupo): bool
    {
        // Verificar que el usuario tenga acceso a la sede del grupo
        $tieneSede = $user->userSedes()->where('sede_id', $grupo->sede_id)->where('activo', true)->exists();
        return $tieneSede || $user->puedeHacer('grupos.ver', $grupo->id);
    }

    public function create(User $user): bool
    {
        return $user->puedeHacer('grupos.crear');
    }

    public function update(User $user, Grupo $grupo): bool
    {
        $tieneSede = $user->userSedes()->where('sede_id', $grupo->sede_id)->where('activo', true)->exists();
        return ($tieneSede || $user->puedeHacer('grupos.editar', $grupo->id));
    }

    public function delete(User $user, Grupo $grupo): bool
    {
        $tieneSede = $user->userSedes()->where('sede_id', $grupo->sede_id)->where('activo', true)->exists();
        return ($tieneSede || $user->puedeHacer('grupos.eliminar', $grupo->id));
    }
}