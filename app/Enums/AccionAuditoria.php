<?php

namespace App\Enums;

enum AccionAuditoria: string
{
    // CRUD
    case Create      = 'create';
    case Update      = 'update';
    case Delete      = 'delete';
    case ForceDelete = 'force_delete';
    case Restore     = 'restore';

    // Acceso
    case Login           = 'login';
    case Logout          = 'logout';
    case LoginFailed     = 'login_failed';
    case TokenRefresh    = 'token_refresh';
    case SessionRevoked  = 'session_revoked';
    case PasswordReset   = 'password_reset';
    case PasswordChanged = 'password_changed';

    // Operaciones críticas
    case Approve   = 'approve';
    case Reject    = 'reject';
    case Authorize = 'authorize';
    case Cancel    = 'cancel';
    case Refund    = 'refund';
    case Open      = 'open';
    case Close     = 'close';

    // Documentos
    case Export    = 'export';
    case Download  = 'download';
    case Print     = 'print';
    case Generate  = 'generate';
    case Publish   = 'publish';
    case Unpublish = 'unpublish';

    public function label(): string
    {
        return match($this) {
            self::Create       => 'Crear',
            self::Update       => 'Actualizar',
            self::Delete       => 'Eliminar',
            self::ForceDelete  => 'Eliminar definitivamente',
            self::Restore      => 'Restaurar',
            self::Login        => 'Inicio de sesión',
            self::Logout       => 'Cierre de sesión',
            self::LoginFailed  => 'Intento fallido',
            self::TokenRefresh => 'Renovar token',
            self::SessionRevoked => 'Sesión revocada',
            self::PasswordReset  => 'Restablecer contraseña',
            self::PasswordChanged => 'Cambiar contraseña',
            self::Approve      => 'Aprobar',
            self::Reject       => 'Rechazar',
            self::Authorize    => 'Autorizar',
            self::Cancel       => 'Cancelar',
            self::Refund       => 'Devolver',
            self::Open         => 'Abrir',
            self::Close        => 'Cerrar',
            self::Export       => 'Exportar',
            self::Download     => 'Descargar',
            self::Print        => 'Imprimir',
            self::Generate     => 'Generar',
            self::Publish      => 'Publicar',
            self::Unpublish    => 'Despublicar',
        };
    }
}
