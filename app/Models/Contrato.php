<?php
namespace App\Models;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contrato extends Model {
    use Auditable;
    protected $fillable = ['empleado_id','folio','fecha_inicio','fecha_fin','tipo','salario','documento','activo'];
    protected $casts = ['fecha_inicio'=>'date','fecha_fin'=>'date','salario'=>'decimal:2','activo'=>'boolean'];
    public function empleado(): BelongsTo { return $this->belongsTo(Empleado::class); }
    public function scopeActivos($q) { return $q->where('activo',true); }
    public function estaVigente(): bool { return $this->activo && (!$this->fecha_fin || $this->fecha_fin->isFuture()); }
}
