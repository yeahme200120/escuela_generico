<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cargo extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'cargos';

    protected $fillable = [
        'alumno_id',
        'ciclo_escolar_id',
        'concepto_id',
        'sede_id',
        'referencia',
        'importe',
        'descuento',
        'recargo',
        'total',
        'fecha_vencimiento',
        'estado',
        'created_by',
    ];

    protected $casts = [
        'importe'          => 'decimal:2',
        'descuento'        => 'decimal:2',
        'recargo'          => 'decimal:2',
        'total'            => 'decimal:2',
        'fecha_vencimiento' => 'date',
    ];

    // ----------------------------------------------------------------
    // Relaciones
    // ----------------------------------------------------------------

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function cicloEscolar(): BelongsTo
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_escolar_id');
    }

    public function concepto(): BelongsTo
    {
        return $this->belongsTo(ConceptoPago::class, 'concepto_id');
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parcialidades(): HasMany
    {
        return $this->hasMany(Parcialidad::class, 'cargo_id');
    }

    public function pagoDetalles(): HasMany
    {
        return $this->hasMany(PagoDetalle::class, 'cargo_id');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeVencidos($query)
    {
        return $query->where('estado', 'vencido');
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
        return "Cargo #{$this->id} (alumno {$this->alumno_id})";
    }
}
