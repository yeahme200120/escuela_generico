<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

class AuditoriaPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->puedeHacer('auditoria.ver');
    }

    public function view(User $user, AuditLog $log): bool
    {
        // Solo puede ver logs de su propia organización
        if ($log->organizacion_id && $user->organizacion_id !== $log->organizacion_id) {
            return false;
        }
        return $user->puedeHacer('auditoria.ver');
    }

    /**
     * La auditoría es completamente inmutable.
     * Ningún usuario puede crearla manualmente, editarla ni eliminarla.
     */
    public function create(User $user): bool  { return false; }
    public function update(User $user): bool  { return false; }
    public function delete(User $user): bool  { return false; }
}
