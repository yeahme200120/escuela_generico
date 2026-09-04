<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserSession;

/**
 * LoginResult — Value object inmutable que representa el resultado de un intento de login.
 */
final class LoginResult
{
    private function __construct(
        public readonly bool          $success,
        public readonly string        $status,   // success | failed | blocked
        public readonly string        $message,
        public readonly ?User         $user        = null,
        public readonly ?UserSession  $session     = null,
    ) {}

    public static function success(User $user, UserSession $session): self
    {
        return new self(
            success: true,
            status:  'success',
            message: 'Bienvenido, ' . $user->nombres,
            user:    $user,
            session: $session,
        );
    }

    public static function failed(string $message): self
    {
        return new self(success: false, status: 'failed', message: $message);
    }

    public static function blocked(string $message): self
    {
        return new self(success: false, status: 'blocked', message: $message);
    }

    public function isSuccess(): bool  { return $this->success;          }
    public function isFailed(): bool   { return $this->status === 'failed';  }
    public function isBlocked(): bool  { return $this->status === 'blocked'; }
}
