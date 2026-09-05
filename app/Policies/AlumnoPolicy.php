<?php

namespace App\Policies;

use App\Models\Alumno;
use App\Models\User;

class AlumnoPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->puedeHacer('alumnos.ver');
    }

    public function view(User $user, Alumno $alumno): bool
    {
        if ($user->organizacion_id !== $alumno->organizacion_id) return false;
        return $user->puedeHacer('alumnos.ver', $alumno->id);
    }

    public function create(User $user): bool
    {
        return $user->puedeHacer('alumnos.crear');
    }

    public function update(User $user, Alumno $alumno): bool
    {
        if ($user->organizacion_id !== $alumno->organizacion_id) return false;
        return $user->puedeHacer('alumnos.editar', $alumno->id);
    }

    public function delete(User $user, Alumno $alumno): bool
    {
        if ($user->organizacion_id !== $alumno->organizacion_id) return false;
        return $user->puedeHacer('alumnos.eliminar', $alumno->id);
    }
}