<?php

namespace App\Models\Concerns;

use App\Services\Auditoria\AuditService;

/**
 * Trait Auditable
 *
 * Agrega trazabilidad automática a cualquier modelo Eloquent.
 * Captura el estado antes/después en create, update y delete.
 * Llama a AuditService::log() que persiste en audit_logs.
 *
 * Uso:
 *   use Auditable;
 *
 * Para excluir campos del diff:
 *   protected array $auditExclude = ['password', 'remember_token'];
 *
 * Para deshabilitar la auditoría temporalmente:
 *   $model->withoutAudit(fn() => $model->save());
 */
trait Auditable
{
    /** Estado capturado antes de la modificación */
    private array $_auditBefore = [];

    /** Flag para deshabilitar auditoría puntualmente */
    private bool $_auditDisabled = false;

    // ----------------------------------------------------------------
    // Boot
    // ----------------------------------------------------------------
    protected static function bootAuditable(): void
    {
        // CREATING — no hay "before", sólo "after"
        static::created(function ($model) {
            if ($model->_auditDisabled) return;
            $model->_fireAudit('create', [], $model->_getAuditableAttributes());
        });

        // UPDATING — capturamos before en "updating" y after en "updated"
        static::updating(function ($model) {
            if ($model->_auditDisabled) return;
            $model->_auditBefore = $model->_getOriginalAuditableAttributes();
        });

        static::updated(function ($model) {
            if ($model->_auditDisabled) return;
            $after   = $model->_getAuditableAttributes();
            $before  = $model->_auditBefore;
            $changes = $model->_diffAudit($before, $after);
            if (!empty($changes)) {
                $model->_fireAudit('update', $before, $after, $changes);
            }
        });

        // DELETING (soft o hard)
        static::deleting(function ($model) {
            if ($model->_auditDisabled) return;
            $model->_auditBefore = $model->_getAuditableAttributes();
        });

        static::deleted(function ($model) {
            if ($model->_auditDisabled) return;
            $action = method_exists($model, 'isForceDeleting') && $model->isForceDeleting()
                ? 'force_delete'
                : 'delete';
            $model->_fireAudit($action, $model->_auditBefore, []);
        });

        // RESTORED (soft delete)
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                if ($model->_auditDisabled) return;
                $model->_fireAudit('restore', [], $model->_getAuditableAttributes());
            });
        }
    }

    // ----------------------------------------------------------------
    // API pública
    // ----------------------------------------------------------------

    /**
     * Ejecuta un callback sin registrar auditoría.
     */
    public function withoutAudit(callable $callback): mixed
    {
        $this->_auditDisabled = true;
        try {
            return $callback($this);
        } finally {
            $this->_auditDisabled = false;
        }
    }

    /**
     * Campos que deben excluirse de la auditoría (complementa $auditExclude del modelo).
     */
    public function getAuditExcludeFields(): array
    {
        $global = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];
        $model  = $this->auditExclude ?? [];
        return array_unique(array_merge($global, $model));
    }

    /**
     * Descripción legible del registro (para model_descripcion en audit_log).
     * Sobreescribir en cada modelo para mayor detalle.
     */
    public function getAuditDescription(): string
    {
        return class_basename($this) . " #{$this->getKey()}";
    }

    // ----------------------------------------------------------------
    // Internals
    // ----------------------------------------------------------------

    private function _getAuditableAttributes(): array
    {
        return array_diff_key(
            $this->attributesToArray(),
            array_flip($this->getAuditExcludeFields())
        );
    }

    private function _getOriginalAuditableAttributes(): array
    {
        return array_diff_key(
            $this->getRawOriginal(),
            array_flip($this->getAuditExcludeFields())
        );
    }

    private function _diffAudit(array $before, array $after): array
    {
        $changes = [];
        $allKeys = array_unique(array_merge(array_keys($before), array_keys($after)));

        foreach ($allKeys as $key) {
            $bVal = $before[$key]  ?? null;
            $aVal = $after[$key]   ?? null;
            // Comparación laxa para evitar falsos positivos con tipos
            if ((string)$bVal !== (string)$aVal) {
                $changes[$key] = ['before' => $bVal, 'after' => $aVal];
            }
        }

        return $changes;
    }

    private function _fireAudit(string $action, array $before, array $after, array $changes = []): void
    {
        // AuditService se resuelve desde el contenedor; si no está disponible (tests mínimos), se omite
        if (!app()->bound(AuditService::class)) return;

        try {
            app(AuditService::class)->logModel(
                action: $action,
                model: $this,
                before: $before,
                after: $after,
                changes: $changes,
            );
        } catch (\Throwable $e) {
            // Nunca interrumpir la operación por un fallo de auditoría
            logger()->error('Audit trait error: ' . $e->getMessage(), ['exception' => $e]);
        }
    }
}
