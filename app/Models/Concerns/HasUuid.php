<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Genera automáticamente un UUID v4 en el campo `uuid` al crear un modelo.
 */
trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Buscar por UUID (helper de conveniencia).
     */
    public static function findByUuid(string $uuid): ?static
    {
        return static::where('uuid', $uuid)->first();
    }

    public static function findByUuidOrFail(string $uuid): static
    {
        return static::where('uuid', $uuid)->firstOrFail();
    }
}
