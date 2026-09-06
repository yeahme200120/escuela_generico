<?php

namespace App\Policies;

use App\Models\CicloEscolar;
use App\Models\User;

class CicloEscolarPolicy extends BasePolicy
{
    public function viewAny(User $user): bool { return $user->puedeHacer('sedes.ver'); }

    public function view(User $user, CicloEscolar $ciclo): bool
    {
        return $ciclo->organizacion_id === $user->organizacion_id;
    }

    public function create(User $user): bool { return $user->puedeHacer('sedes.crear'); }

    public function update(User $user, CicloEscolar $ciclo): bool
    {
        if ($ciclo->organizacion_id !== $user->organizacion_id) return false;
        return $user->puedeHacer('sedes.editar');
    }

    public function delete(User $user, CicloEscolar $ciclo): bool
    {
        return false; // Ciclos no se eliminan físicamente §22
    }
}
