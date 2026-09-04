<?php
namespace App\Models;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reingreso extends Model {
    use Auditable;
    protected $fillable = ['alumno_id','sede_id','grado_id','grupo_id','ciclo_escolar_id','usuario_id','fecha_solicitud','fecha_reingreso','motivo','estado'];
    protected $casts = ['fecha_solicitud'=>'date','fecha_reingreso'=>'date'];
    public function alumno(): BelongsTo { return $this->belongsTo(Alumno::class); }
    public function sede(): BelongsTo { return $this->belongsTo(Sede::class); }
    public function grado(): BelongsTo { return $this->belongsTo(Grado::class); }
    public function grupo(): BelongsTo { return $this->belongsTo(Grupo::class); }
    public function cicloEscolar(): BelongsTo { return $this->belongsTo(CicloEscolar::class); }
    public function usuario(): BelongsTo { return $this->belongsTo(User::class,'usuario_id'); }
    public function scopePendientes($q) { return $q->where('estado','solicitado'); }
}
