<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Baja extends Model
{
    use HasUuid, Auditable;

    protected $table = 'bajas';

    protected $fillable = [
        'uuid',
        'alumno_id',
        'tipo',
        'fecha_solicitud',
        'fecha_inicio',
        'fecha_fin_estimada',
        'fecha_reingreso',
        'motivo',
        'motivo_desercion',
        'documento',
        'estatus',
        'observaciones',
        'usuario_solicita',
        'usuario_autoriza',
    ];

    protected $casts = [
        'fecha_solicitud'    => 'date',
        'fecha_inicio'       => 'date',
        'fecha_fin_estimada' => 'date',
        'fecha_reingreso'    => 'date',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function usuarioSolicita(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_solicita');
    }

    public function usuarioAutoriza(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_autoriza');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeActivas($query)
    {
        return $query->where('estatus', 'activa');
    }

    public function scopeDeTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePendientesDeAprobacion($query)
    {
        return $query->where('estatus', 'solicitada');
    }

    // ----------------------------------------------------------------
    // Auditoría
    // ----------------------------------------------------------------

    public function getAuditDescription(): string
    {
        return "Baja [{$this->tipo}] alumno_id={$this->alumno_id}";
    }
}
