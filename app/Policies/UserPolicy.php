<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->puedeHacer('usuarios.ver');
    }

    public function view(User $user, User $target): bool
    {
        // Solo puede ver usuarios de su misma organización
        if ($user->organizacion_id !== $target->organizacion_id) return false;
        return $user->puedeHacer('usuarios.ver');
    }

    public function create(User $user): bool
    {
        return $user->puedeHacer('usuarios.crear');
    }

    public function update(User $user, User $target): bool
    {
        if ($user->organizacion_id !== $target->organizacion_id) return false;
        // No se puede editar a alguien con nivel de rol más alto
        $miNivel     = $user->roles()->min('nivel') ?? 99;
        $targetNivel = $target->roles()->min('nivel') ?? 99;
        if ($targetNivel < $miNivel) return false;
        return $user->puedeHacer('usuarios.editar');
    }

    public function delete(User $user, User $target): bool
    {
        if ($user->id === $target->id) return false; // no puede eliminarse a sí mismo
        if ($user->organizacion_id !== $target->organizacion_id) return false;
        $miNivel     = $user->roles()->min('nivel') ?? 99;
        $targetNivel = $target->roles()->min('nivel') ?? 99;
        if ($targetNivel <= $miNivel) return false;
        return $user->puedeHacer('usuarios.eliminar');
    }

    public function assignRole(User $user, User $target): bool
    {
        if ($user->organizacion_id !== $target->organizacion_id) return false;
        return $user->puedeHacer('roles.asignar');
    }
}
