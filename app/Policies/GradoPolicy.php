<?php

namespace App\Policies;

use App\Models\Grado;
use App\Models\User;

class GradoPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->puedeHacer('grados.ver');
    }

    public function view(User $user, Grado $grado): bool
    {
        if ($user->organizacion_id !== $grado->organizacion_id) return false;
        return $user->puedeHacer('grados.ver', $grado->id);
    }

    public function create(User $user): bool
    {
        return $user->puedeHacer('grados.crear');
    }

    public function update(User $user, Grado $grado): bool
    {
        if ($user->organizacion_id !== $grado->organizacion_id) return false;
        return $user->puedeHacer('grados.editar', $grado->id);
    }

    public function delete(User $user, Grado $grado): bool
    {
        if ($user->organizacion_id !== $grado->organizacion_id) return false;
        return $user->puedeHacer('grados.eliminar', $grado->id);
    }
}