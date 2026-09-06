<?php

namespace App\Policies;

use App\Models\Grupo;
use App\Models\User;

class GrupoPolicy extends BasePolicy
{
    public function viewAny(User $user): bool { return $user->puedeHacer('grupos.ver'); }

    public function view(User $user, Grupo $grupo): bool
    {
        if ($grupo->sede?->organizacion_id !== $user->organizacion_id) return false;
        // Docente solo puede ver sus grupos asignados
        if ($user->hasRole('docente')) {
            return $grupo->docenteGrupoMaterias()->where('docente_id', $user->docente?->id)->exists();
        }
        return $user->puedeHacer('grupos.ver');
    }

    public function create(User $user): bool { return $user->puedeHacer('grupos.crear'); }

    public function update(User $user, Grupo $grupo): bool
    {
        if ($grupo->sede?->organizacion_id !== $user->organizacion_id) return false;
        return $user->puedeHacer('grupos.editar');
    }

    public function delete(User $user, Grupo $grupo): bool
    {
        return false; // Grupos no se eliminan físicamente §22
    }
}
