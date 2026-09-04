<?php

namespace App\Support;

/**
 * RequestContext — Singleton por request.
 * Centraliza los datos del contexto actual: request ID, sesión de usuario,
 * sede activa, geo, etc.  Todos los servicios y middleware escriben/leen aquí.
 */
final class RequestContext
{
    private string   $requestId   = '';
    private ?string  $sessionUuid = null;
    private ?int     $userId      = null;
    private ?int     $organizacionId = null;
    private ?int     $sedeId      = null;
    private ?string  $sedeNombre  = null;
    private ?string  $userNombre  = null;
    private ?string  $userEmail   = null;
    private ?string  $userRol     = null;
    private ?GeoContext $geo      = null;
    private float    $startTime   = 0.0;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    // ----------------------------------------------------------------
    // Setters
    // ----------------------------------------------------------------
    public function setRequestId(string $id): self       { $this->requestId      = $id;     return $this; }
    public function setSessionUuid(?string $uuid): self  { $this->sessionUuid    = $uuid;   return $this; }
    public function setUserId(?int $id): self             { $this->userId         = $id;     return $this; }
    public function setOrganizacionId(?int $id): self     { $this->organizacionId = $id;     return $this; }
    public function setSedeId(?int $id): self             { $this->sedeId         = $id;     return $this; }
    public function setSedeNombre(?string $n): self       { $this->sedeNombre     = $n;      return $this; }
    public function setUserNombre(?string $n): self       { $this->userNombre     = $n;      return $this; }
    public function setUserEmail(?string $e): self        { $this->userEmail      = $e;      return $this; }
    public function setUserRol(?string $r): self          { $this->userRol        = $r;      return $this; }
    public function setGeo(?GeoContext $geo): self        { $this->geo            = $geo;    return $this; }

    // ----------------------------------------------------------------
    // Getters
    // ----------------------------------------------------------------
    public function getRequestId(): string       { return $this->requestId;      }
    public function getSessionUuid(): ?string    { return $this->sessionUuid;    }
    public function getUserId(): ?int            { return $this->userId;         }
    public function getOrganizacionId(): ?int    { return $this->organizacionId; }
    public function getSedeId(): ?int            { return $this->sedeId;         }
    public function getSedeNombre(): ?string     { return $this->sedeNombre;     }
    public function getUserNombre(): ?string     { return $this->userNombre;     }
    public function getUserEmail(): ?string      { return $this->userEmail;      }
    public function getUserRol(): ?string        { return $this->userRol;        }
    public function getGeo(): ?GeoContext        { return $this->geo;            }

    public function getDuracionMs(): int
    {
        return (int) round((microtime(true) - $this->startTime) * 1000);
    }

    /**
     * Carga datos del usuario autenticado en el contexto.
     */
    public function populateFromUser(\App\Models\User $user, ?int $sedeId = null): self
    {
        $this->setUserId($user->id)
             ->setOrganizacionId($user->organizacion_id)
             ->setUserNombre($user->nombre_completo)
             ->setUserEmail($user->email)
             ->setUserRol($user->roles()->where('user_roles.activo', true)->value('slug'));

        if ($sedeId) {
            $sede = \App\Models\Sede::find($sedeId);
            $this->setSedeId($sedeId)
                 ->setSedeNombre($sede?->nombre);
        }

        return $this;
    }
}
