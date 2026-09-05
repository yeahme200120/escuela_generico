<?php

namespace App\Policies;

use App\Models\Materia;
use App\Models\User;

class MateriaPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->puedeHacer('materias.ver');
    }

    public function view(User $user, Materia $materia): bool
    {
        // Verificar que el usuario tenga acceso a la escuela de la materia (a través de organización)
        $escuela = $materia->escuela;
        if ($escuela && $user->organizacion_id !== $escuela->organizacion_id) return false;
        return $user->puedeHacer('materias.ver', $materia->id);
    }

    public function create(User $user): bool
    {
        return $user->puedeHacer('materias.crear');
    }

    public function update(User $user, Materia $materia): bool
    {
        $escuela = $materia->escuela;
        if ($escuela && $user->organizacion_id !== $escuela->organizacion_id) return false;
        return $user->puedeHacer('materias.editar', $materia->id);
    }

    public function delete(User $user, Materia $materia): bool
    {
        $escuela = $materia->escuela;
        if ($escuela && $user->organizacion_id !== $escuela->organizacion_id) return false;
        return $user->puedeHacer('materias.eliminar', $materia->id);
    }
}