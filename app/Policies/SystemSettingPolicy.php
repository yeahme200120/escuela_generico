<?php

namespace App\Policies;

use App\Models\SystemSetting;
use App\Models\User;

class SystemSettingPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->puedeHacer('configuracion.ver');
    }

    public function update(User $user, SystemSetting $setting): bool
    {
        // Solo la misma organización
        if ($setting->organizacion_id && $user->organizacion_id !== $setting->organizacion_id) {
            return false;
        }
        // El tema solo lo puede tocar quien tiene el permiso específico
        if (str_starts_with($setting->key, 'theme.')) {
            return $user->puedeHacer('configuracion.apariencia.editar');
        }
        return $user->puedeHacer('configuracion.ver');
    }

    public function delete(User $user): bool { return false; }
}
