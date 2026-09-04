<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pago extends Model
{
    use HasUuid, Auditable;

    protected $table = 'pagos';

    protected $fillable = [
        'alumno_id',
        'sede_id',
        'caja_id',
        'referencia',
        'importe',
        'fecha_pago',
        'metodo_pago_id',
        'usuario_id',
        'estado',
        'motivo_cancelacion',
        'cancelado_por',
        'cancelado_at',
        'idempotency_key',
        'request_id',
    ];

    // uuid se excluye de fillable; HasUuid lo genera automáticamente

    protected $casts = [
        'importe'      => 'decimal:2',
        'fecha_pago'   => 'date',
        'cancelado_at' => 'datetime',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function metodoPago(): BelongsTo
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function canceladoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelado_por');
    }

    public function pagoDetalles(): HasMany
    {
        return $this->hasMany(PagoDetalle::class, 'pago_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeCancelados($query)
    {
        return $query->where('estado', 'cancelado');
    }

    public function scopeDeSede($query, int $sedeId)
    {
        return $query->where('sede_id', $sedeId);
    }

    public function scopeDelAlumno($query, int $alumnoId)
    {
        return $query->where('alumno_id', $alumnoId);
    }

    // ----------------------------------------------------------------
    // Auditoría
    // ----------------------------------------------------------------

    public function getAuditDescription(): string
    {
        return "Pago #{$this->id} (alumno {$this->alumno_id}, importe {$this->importe})";
    }
}
