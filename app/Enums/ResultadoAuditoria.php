<?php

namespace App\Enums;

enum ResultadoAuditoria: string
{
    case Success      = 'success';
    case Failed       = 'failed';
    case Unauthorized = 'unauthorized';
    case Error        = 'error';

    public function label(): string
    {
        return match($this) {
            self::Success      => 'Exitoso',
            self::Failed       => 'Fallido',
            self::Unauthorized => 'No autorizado',
            self::Error        => 'Error',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Success      => 'badge-success',
            self::Failed       => 'badge-danger',
            self::Unauthorized => 'badge-warning',
            self::Error        => 'badge-danger',
        };
    }
}
