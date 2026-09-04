<?php
namespace App\Models;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Prospecto extends Model {
    use HasUuid, Auditable, SoftDeletes;
    protected $fillable = ['organizacion_id','sede_interes_id','nombres','apellido_paterno','apellido_materno','email','telefono','nivel_interes','grado_interes','ciclo_interes','estatus','asignado_a','observaciones'];
    public function sedeInteres(): BelongsTo { return $this->belongsTo(Sede::class,'sede_interes_id'); }
    public function asignadoA(): BelongsTo { return $this->belongsTo(User::class,'asignado_a'); }
    public function seguimientos(): HasMany { return $this->hasMany(SeguimientoProspecto::class); }
    public function admisiones(): HasMany { return $this->hasMany(Admision::class); }
    public function getNombreCompletoAttribute(): string { return trim("{$this->nombres} {$this->apellido_paterno} {$this->apellido_materno}"); }
    public function scopePorEstatus($q,$e) { return $q->where('estatus',$e); }
}
