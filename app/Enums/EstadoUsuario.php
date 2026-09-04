<?php

namespace App\Enums;

enum EstadoUsuario: string
{
    case Activo   = 'activo';
    case Inactivo = 'inactivo';
    case Bloqueado = 'bloqueado';

    public function label(): string
    {
        return match($this) {
            self::Activo    => 'Activo',
            self::Inactivo  => 'Inactivo',
            self::Bloqueado => 'Bloqueado',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Activo    => 'badge-success',
            self::Inactivo  => 'badge-gray',
            self::Bloqueado => 'badge-danger',
        };
    }
}
