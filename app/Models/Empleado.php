<?php
namespace App\Models;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empleado extends Model {
    use Auditable, SoftDeletes;
    protected $table = 'empleados';
    protected $fillable = ['user_id','organizacion_id','numero_empleado','puesto','departamento','fecha_ingreso','fecha_baja','tipo_contrato','salario','estatus'];
    protected $casts = ['fecha_ingreso'=>'date','fecha_baja'=>'date','salario'=>'decimal:2'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function organizacion(): BelongsTo { return $this->belongsTo(Organizacion::class); }
    public function contratos(): HasMany { return $this->hasMany(Contrato::class); }
    public function asistencias(): HasMany { return $this->hasMany(AsistenciaPersonal::class); }
    public function scopeActivos($q) { return $q->where('estatus','activo'); }
    public function getAuditDescription(): string { return "Empleado: {$this->user?->nombre_completo} #{$this->id}"; }
}
