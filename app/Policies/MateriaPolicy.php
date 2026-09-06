<?php

namespace App\Policies;

use App\Models\Materia;
use App\Models\User;

class MateriaPolicy extends BasePolicy
{
    public function viewAny(User $user): bool { return $user->puedeHacer('grupos.ver'); }

    public function view(User $user, Materia $materia): bool
    {
        return $materia->escuela?->organizacion_id === $user->organizacion_id;
    }

    public function create(User $user): bool { return $user->puedeHacer('grupos.crear'); }

    public function update(User $user, Materia $materia): bool
    {
        if ($materia->escuela?->organizacion_id !== $user->organizacion_id) return false;
        return $user->puedeHacer('grupos.editar');
    }

    public function delete(User $user, Materia $materia): bool
    {
        return $user->puedeHacer('grupos.editar') && $materia->escuela?->organizacion_id === $user->organizacion_id;
    }
}
