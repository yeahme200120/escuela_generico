<?php
namespace App\Models;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mantenimiento extends Model {
    use Auditable, SoftDeletes;
    protected $table = 'mantenimiento';
    protected $fillable = ['sede_id','edificio_id','aula_id','activo_fijo_id','titulo','descripcion','prioridad','estado','reportado_por','responsable_id','fecha_reporte','fecha_inicio','fecha_resolucion','costo','observaciones'];
    protected $casts = ['fecha_reporte'=>'date','fecha_inicio'=>'date','fecha_resolucion'=>'date','costo'=>'decimal:2'];
    public function sede(): BelongsTo { return $this->belongsTo(Sede::class); }
    public function edificio(): BelongsTo { return $this->belongsTo(Edificio::class); }
    public function aula(): BelongsTo { return $this->belongsTo(Aula::class); }
    public function reportadoPor(): BelongsTo { return $this->belongsTo(User::class,'reportado_por'); }
    public function responsable(): BelongsTo { return $this->belongsTo(User::class,'responsable_id'); }
    public function scopeAbiertos($q) { return $q->whereNotIn('estado',['resuelto','cancelado']); }
    public function scopeUrgentes($q) { return $q->where('prioridad','urgente')->abiertos(); }
}
