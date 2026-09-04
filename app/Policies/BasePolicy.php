<?php

namespace App\Policies;

use App\Models\User;

/**
 * BasePolicy — Clase base para todas las policies del sistema.
 *
 * Reglas globales:
 *  - Superadmin siempre puede. (before() retorna true)
 *  - Usuario inactivo/bloqueado nunca puede. (before() retorna false)
 *  - Cada policy verifica puedeHacer() + restricción de sede/organización.
 */
abstract class BasePolicy
{
    /**
     * Gancho previo: se ejecuta antes de cualquier método de la policy.
     * Retornar true cortocircuita y permite la acción.
     * Retornar false cortocircuita y deniega.
     * Retornar null continúa la evaluación normal.
     */
    public function before(User $user, string $ability): ?bool
    {
        // Inactivo o bloqueado → siempre deniega
        if (!$user->activo || $user->estaBloqueado()) {
            return false;
        }

        // Superadmin → siempre permite
        if ($user->esSuperadmin()) {
            return true;
        }

        return null; // continuar evaluación normal
    }

    /**
     * Verifica si el usuario tiene el permiso y además pertenece
     * a la misma organización que el recurso.
     */
    protected function tienePermisoEnOrg(User $user, string $permiso, int $orgId): bool
    {
        if ($user->organizacion_id !== $orgId) return false;
        return $user->puedeHacer($permiso);
    }

    /**
     * Verifica permiso + scope de sede.
     */
    protected function tienePermisoEnSede(User $user, string $permiso, int $sedeId): bool
    {
        return $user->puedeHacer($permiso, $sedeId);
    }
}
