<?php

namespace App\Policies;

use App\Models\NivelEducativo;
use App\Models\User;

class NivelEducativoPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->puedeHacer('niveles_educativos.ver');
    }

    public function view(User $user, NivelEducativo $nivel): bool
    {
        if ($user->organizacion_id !== $nivel->organizacion_id) return false;
        return $user->puedeHacer('niveles_educativos.ver', $nivel->id);
    }

    public function create(User $user): bool
    {
        return $user->puedeHacer('niveles_educativos.crear');
    }

    public function update(User $user, NivelEducativo $nivel): bool
    {
        if ($user->organizacion_id !== $nivel->organizacion_id) return false;
        return $user->puedeHacer('niveles_educativos.editar', $nivel->id);
    }

    public function delete(User $user, NivelEducativo $nivel): bool
    {
        if ($user->organizacion_id !== $nivel->organizacion_id) return false;
        return $user->puedeHacer('niveles_educativos.eliminar', $nivel->id);
    }
}