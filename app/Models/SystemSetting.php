<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    protected $fillable = [
        'organizacion_id', 'key', 'value', 'type', 'grupo', 'descripcion', 'updated_by',
    ];

    public function organizacion(): BelongsTo
    {
        return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeDeGrupo($q, string $grupo)
    {
        return $q->where('grupo', $grupo);
    }

    public function scopeDeOrganizacion($q, int $orgId)
    {
        return $q->where('organizacion_id', $orgId);
    }

    /**
     * Obtiene el valor casteado según el tipo.
     */
    public function getValueCastedAttribute(): mixed
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'json'    => json_decode($this->value, true),
            default   => $this->value,
        };
    }
}
