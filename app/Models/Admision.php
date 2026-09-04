<?php
namespace App\Models;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Admision extends Model {
    use HasUuid, Auditable;
    protected $fillable = ['prospecto_id','alumno_id','sede_id','ciclo_escolar_id','grado_id','estatus','fecha_solicitud','fecha_resolucion','observaciones','atendido_por'];
    protected $casts = ['fecha_solicitud'=>'date','fecha_resolucion'=>'date'];
    public function prospecto(): BelongsTo { return $this->belongsTo(Prospecto::class); }
    public function alumno(): BelongsTo { return $this->belongsTo(Alumno::class); }
    public function sede(): BelongsTo { return $this->belongsTo(Sede::class); }
    public function cicloEscolar(): BelongsTo { return $this->belongsTo(CicloEscolar::class); }
    public function grado(): BelongsTo { return $this->belongsTo(Grado::class); }
    public function atendidoPor(): BelongsTo { return $this->belongsTo(User::class,'atendido_por'); }
    public function scopePorEstatus($q,$e) { return $q->where('estatus',$e); }
}
