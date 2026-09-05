<?php

namespace App\Policies;

use App\Models\Escuela;
use App\Models\User;

class EscuelaPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->puedeHacer('escuelas.ver');
    }

    public function view(User $user, Escuela $escuela): bool
    {
        if ($user->organizacion_id !== $escuela->organizacion_id) return false;
        return $user->puedeHacer('escuelas.ver', $escuela->id);
    }

    public function create(User $user): bool
    {
        return $user->puedeHacer('escuelas.crear');
    }

    public function update(User $user, Escuela $escuela): bool
    {
        if ($user->organizacion_id !== $escuela->organizacion_id) return false;
        return $user->puedeHacer('escuelas.editar', $escuela->id);
    }

    public function delete(User $user, Escuela $escuela): bool
    {
        if ($user->organizacion_id !== $escuela->organizacion_id) return false;
        return $user->puedeHacer('escuelas.eliminar', $escuela->id);
    }
}