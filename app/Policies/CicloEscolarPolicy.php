<?php

namespace App\Policies;

use App\Models\CicloEscolar;
use App\Models\User;

class CicloEscolarPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->puedeHacer('ciclos_escolares.ver');
    }

    public function view(User $user, CicloEscolar $ciclo): bool
    {
        if ($user->organizacion_id !== $ciclo->organizacion_id) return false;
        return $user->puedeHacer('ciclos_escolares.ver', $ciclo->id);
    }

    public function create(User $user): bool
    {
        return $user->puedeHacer('ciclos_escolares.crear');
    }

    public function update(User $user, CicloEscolar $ciclo): bool
    {
        if ($user->organizacion_id !== $ciclo->organizacion_id) return false;
        return $user->puedeHacer('ciclos_escolares.editar', $ciclo->id);
    }

    public function delete(User $user, CicloEscolar $ciclo): bool
    {
        if ($user->organizacion_id !== $ciclo->organizacion_id) return false;
        return $user->puedeHacer('ciclos_escolares.eliminar', $ciclo->id);
    }
}